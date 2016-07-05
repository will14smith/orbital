<?php

namespace AppBundle\Controller;

use AppBundle\Constants;
use AppBundle\Controller\Traits\PdfRenderTrait;
use AppBundle\Entity\Club;
use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use AppBundle\Services\Records\RecordManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// TODO make groups a proper data structure

class RecordPdfController extends Controller
{
    use PdfRenderTrait;

    private static $envs = [Environment::INDOOR, Environment::OUTDOOR];
    private static $skills = [Skill::SENIOR, Skill::NOVICE];
    private static $genders = [Gender::MALE, Gender::FEMALE];
    private static $bowtypes = [BowType::RECURVE, BowType::COMPOUND, BowType::BAREBOW, BowType::LONGBOW];

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

        $groups = $this->buildGroups();
        $groups = $this->populateGroups($records, $groups, $club);
        $groups = $this->pruneGroups($groups);

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

    /**
     * @return array
     */
    private function buildGroups()
    {
        $groups = [];

        foreach (self::$envs as $env) {
            $envN = Environment::display($env);

            array_push($groups, [
                'name' => $envN . ' - Teams',
                'subgroups' => [
                    ['name' => 'Senior Team', 'isTeam' => true, 'records' => []],
                    ['name' => 'Novice Team', 'isTeam' => true, 'records' => []],
                ],
            ]);

            foreach (self::$skills as $skill) {
                $skillN = Skill::display($skill);

                foreach (self::$genders as $gender) {
                    $genderN = Gender::display($gender);
                    $subgroups = [];

                    foreach (self::$bowtypes as $bowtype) {
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

        return $groups;
    }

    /**
     * @param Record[] $records
     * @param array    $groups
     * @param Club     $club
     *
     * @return array
     */
    private function populateGroups($records, $groups, $club)
    {
        foreach ($records as $record) {
            $indoors = $record->isIndoor();
            $team = $record->getNumHolders() > 1;
            $novice = $record->isNovice();
            $female = $record->getGender() === Gender::FEMALE;

            $envOffset = $indoors ? 0 : (count(self::$skills) * count(self::$genders) + 1);

            if ($team) {
                $target = &$groups[$envOffset]['subgroups'][$novice ? 1 : 0]['records'];
            } else {
                $groupIdx = $envOffset + count(self::$genders) * ($novice ? 1 : 0) + ($female ? 2 : 1);
                $subgroupIdx = array_search($record->getBowtype(), self::$bowtypes, true);

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

        return $groups;
    }

    /**
     * @param array $groups
     *
     * @return array
     */
    private function pruneGroups($groups)
    {
        return array_filter($groups, function ($group) {
            $group['subgroups'] = array_filter($group['subgroups'], function ($subgroup) {
                return count($subgroup['records']) > 0;
            });

            return count($group['subgroups']) > 0;
        });
    }
}
