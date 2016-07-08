<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\PdfRenderTrait;
use AppBundle\Entity\Club;
use AppBundle\Entity\Record;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use AppBundle\View\Model\RecordGroupViewModel;
use AppBundle\View\Model\RecordSubgroupViewModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecordListController extends Controller
{
    use PdfRenderTrait;

    private static $envs = [Environment::INDOOR, Environment::OUTDOOR];
    private static $skills = [Skill::SENIOR, Skill::NOVICE];
    private static $genders = [Gender::MALE, Gender::FEMALE];
    private static $bowtypes = [BowType::RECURVE, BowType::COMPOUND, BowType::BAREBOW, BowType::LONGBOW];

    /**
     * @Route("/records", name="record_list", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        $clubRepository = $this->getDoctrine()->getRepository('AppBundle:Club');
        $club_id = $request->query->getInt('club');
        $club = $clubRepository->find($club_id);
        if (!$club) {
            return $this->indexClubAction();
        }

        $recordRepository = $this->getDoctrine()->getRepository('AppBundle:Record');
        $records = $recordRepository->findAllByClub($club->getId());

        $groups = $this->buildGroups();
        $groups = $this->populateGroups($records, $groups, $club);
        $groups = $this->pruneGroups($groups);
        $groups = $this->sortRecords($groups);

        return $this->render('record/list.html.twig', [
            'groups' => $groups,
            'club' => $club,
        ]);
    }

    private function indexClubAction()
    {
        $clubRepository = $this->getDoctrine()->getRepository('AppBundle:Club');

        $clubs = $clubRepository->findAll();

        return $this->render('record/list_select_club.html.twig', [
            'clubs' => $clubs,
        ]);
    }

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
        $groups = $this->sortRecords($groups);

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
     * @return RecordGroupViewModel[]
     */
    private function buildGroups()
    {
        /** @var RecordGroupViewModel[] $groups */
        $groups = [];

        foreach (self::$envs as $env) {
            $envN = Environment::display($env);

            $groups[] = new RecordGroupViewModel($envN . ' - Teams', [
                new RecordSubgroupViewModel('Senior Team', true),
                new RecordSubgroupViewModel('Novice Team', true),
            ]);

            foreach (self::$skills as $skill) {
                $skillN = Skill::display($skill);

                foreach (self::$genders as $gender) {
                    $genderN = Gender::display($gender);
                    $subgroups = [];

                    foreach (self::$bowtypes as $bowtype) {
                        $subgroups[] = new RecordSubgroupViewModel($skillN . ' ' . $genderN . ' ' . BowType::display($bowtype), false);
                    }

                    $groups[] = new RecordGroupViewModel($envN . ' - ' . $skillN . ' ' . $genderN, $subgroups);
                }
            }
        }

        return $groups;
    }

    /**
     * @param Record[]               $records
     * @param RecordGroupViewModel[] $groups
     * @param Club                   $club
     *
     * @return RecordGroupViewModel[]
     */
    private function populateGroups($records, $groups, $club)
    {
        foreach ($records as $record) {
            $indoors = $record->isIndoor();
            $team = $record->getNumHolders() > 1;
            $novice = $record->isNovice();
            $female = $record->getGender() === Gender::FEMALE;

            $envOffset = $indoors ? 0 : (count(self::$skills) * count(self::$genders) + 1);

            /** @var RecordSubgroupViewModel $target */
            if ($team) {
                $target = $groups[$envOffset]->getSubgroup($novice ? 1 : 0);
            } else {
                $groupIdx = $envOffset + count(self::$genders) * ($novice ? 1 : 0) + ($female ? 2 : 1);
                $subgroupIdx = array_search($record->getBowtype(), self::$bowtypes, true);

                $target = $groups[$groupIdx]->getSubgroup($subgroupIdx);
            }

            $currentHolder = $record->getCurrentHolder($club);

            if ($currentHolder === null) {
                $target->addUnclaimed($record);
            } else {
                $target->addRecord($record, $currentHolder);
            }
        }

        return $groups;
    }

    /**
     * @param RecordGroupViewModel[] $groups
     *
     * @return RecordGroupViewModel[]
     */
    private function pruneGroups(array $groups)
    {
        $groups = array_map(function (RecordGroupViewModel $group) {
            $subgroups = array_filter($group->getSubgroups(), function (RecordSubgroupViewModel $subgroup) {
                return count($subgroup->getRecords()) > 0;
            });

            return new RecordGroupViewModel($group->getName(), $subgroups);
        }, $groups);

        return array_filter($groups, function (RecordGroupViewModel $group) {
            return count($group->getSubgroups()) > 0;
        });
    }

    /**
     * @param RecordGroupViewModel[] $groups
     *
     * @return RecordGroupViewModel[]
     */
    private function sortRecords(array $groups)
    {
        return array_map(function (RecordGroupViewModel $group) {
            return new RecordGroupViewModel($group->getName(), array_map(function (RecordSubgroupViewModel $subgroup) {
                return $subgroup->sort();
            }, $group->getSubgroups()));
        }, $groups);
    }
}
