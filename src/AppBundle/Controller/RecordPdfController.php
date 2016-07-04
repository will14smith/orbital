<?php

namespace AppBundle\Controller;

use AppBundle\Constants;
use AppBundle\Controller\Traits\PdfRenderTrait;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use AppBundle\Services\Records\RecordManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecordPdfController extends Controller
{
    use PdfRenderTrait;

    /**
     * @Route("/records/pdf", name="record_pdf", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pdfAction(Request $request)
    {
        $clubRepository = $this->getDoctrine()->getRepository('AppBundle:Club');
        $club_id = $request->query->getInt('club');
        $club = $clubRepository->find($club_id);
        if (!$club) {
            throw $this->createNotFoundException(
                'No club found for id ' . $club_id
            );
        }

        $recordRepository = $this->getDoctrine()->getRepository('AppBundle:Record');
        $records = $recordRepository->findAllByClub($club->getId());

        $groups = [];

        $envs = [
            Environment::INDOOR,
            Environment::OUTDOOR,
        ];

        $skills = [
            Skill::SENIOR,
            Skill::NOVICE,
        ];

        $genders = [
            Gender::MALE,
            Gender::FEMALE,
        ];

        $bowtypes = [
            BowType::RECURVE,
            BowType::COMPOUND,
            BowType::BAREBOW,
            BowType::LONGBOW,
        ];

        foreach ($envs as $env) {
            $envN = Environment::display($env);

            array_push($groups, [
                'name' => $envN . ' - Teams',
                'subgroups' => [
                    ['name' => 'Senior Team', 'isTeam' => true, 'records' => []],
                    ['name' => 'Novice Team', 'isTeam' => true, 'records' => []],
                ],
            ]);

            foreach ($skills as $skill) {
                $skillN = Skill::display($skill);

                foreach ($genders as $gender) {
                    $genderN = Gender::display($gender);
                    $subgroups = [];

                    foreach ($bowtypes as $bowtype) {
                        array_push($subgroups, [
                            'name' => $skillN . ' ' . $genderN . ' ' . BowType::display($bowtype),
                            'isTeam' => false,
                            'records' => [],
                        ]);
                    }

                    array_push($groups, [
                        'name' => $envN . ' - ' . $skillN . ' ' . $genderN,
                        'subgroups' => $subgroups,
                    ]);
                }
            }
        }


        foreach ($records as $record) {
            $indoors = $record->isIndoor();
            $team = $record->getNumHolders() > 1;
            $novice = $record->isNovice();
            $female = $record->getGender() === Gender::FEMALE;

            $envOffset = $indoors ? 0 : (count($skills) * count($genders) + 1);

            if ($team) {
                $target = &$groups[$envOffset]['subgroups'][$novice ? 1 : 0]['records'];
            } else {
                $groupIdx = $envOffset + count($genders) * ($novice ? 1 : 0) + ($female ? 2 : 1);
                $subgroupIdx = array_search($record->getBowtype(), $bowtypes, true);

                $target = &$groups[$groupIdx]['subgroups'][$subgroupIdx]['records'];
            }

            $currentHolder = $record->getCurrentHolder($club);

            $roundName = RecordManager::getRoundName($record);

            if ($currentHolder === null) {
                array_push($target, [
                    'round' => $roundName,
                    'unclaimed' => true,
                ]);
            } else {
                array_push($target, [
                    'round' => $roundName,
                    'unclaimed' => false,
                    'score' => $currentHolder->getScore(),
                    'holders' => $currentHolder->getPeople()->map(function (RecordHolderPerson $person) {
                        return ['name' => $person->getPerson()->getName(), 'score' => $person->getScoreValue()];
                    }),
                    'details' => $currentHolder->getCompetition()->getName() . ', ' . $currentHolder->getDate()->format(Constants::DATE_FORMAT),
                ]);
            }
        }

        $data = [
            'title' => $club->getRecordsTitle(),
            'image_url' => $club->getRecordsImageUrl(),
            'preface' => $club->getRecordsPreface(),
            'appendix' => $club->getRecordsAppendix(),

            'groups' => $groups,
        ];

        if ($request->query->has('html')) {
            return $this->render('record/list.pdf.twig', $data);
        }

        return $this->renderPdf('record/list.pdf.twig', $data, [
            'margin-top' => '12mm',
            'margin-bottom' => '12mm',
            'margin-left' => '24mm',
            'margin-right' => '24mm',

            'orientation' => 'Landscape',

            'footer-html' => $this->renderView('record/list_footer.pdf.twig', $data),
        ]);
    }
}
