<?php

namespace AppBundle\Controller;

use AppBundle\Constants;
use AppBundle\Controller\Traits\PdfRenderTrait;
use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Entity\RecordRound;
use AppBundle\Form\Type\RecordMatrixType;
use AppBundle\Form\Type\RecordType;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use AppBundle\Services\Records\RecordManager;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RecordController extends Controller
{
    use PdfRenderTrait;

    /**
     * @Route("/records", name="record_list", methods={"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $club_id = $request->query->getInt('club');
        if ($club_id == 0) {
            return $this->indexClubAction();
        }

        $recordRepository = $this->getDoctrine()->getRepository("AppBundle:Record");
        if( $club_id == -1 && $this->isGranted('ROLE_ADMIN')) {
            $records = $recordRepository->findAll();

            return $this->render('record/list.html.twig', [
                'records' => $records,
                'club' => null,
            ]);
        }

        $clubRepository = $this->getDoctrine()->getRepository("AppBundle:Club");
        $club = $clubRepository->find($club_id);
        if ($club == null) {
            return $this->indexClubAction();
        }

        $records = $recordRepository->getByClub($club);

        return $this->render('record/list.html.twig', [
            'records' => $records,
            'club' => $club,
        ]);
    }


    private function indexClubAction()
    {
        $clubRepository = $this->getDoctrine()->getRepository("AppBundle:Club");

        $clubs = $clubRepository->findAll();

        return $this->render('record/list_select_club.html.twig', [
            'clubs' => $clubs
        ]);
    }

    /**
     * @Route("/records/pdf", name="record_pdf", methods={"GET"})
     */
    public function pdfAction(Request $req)
    {
        $recordRepository = $this->getDoctrine()->getRepository("AppBundle:Record");

        $records = $recordRepository->findAll();

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
                    ['name' => 'Novice Team', 'isTeam' => true, 'records' => []]
                ]
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
                            'records' => []
                        ]);
                    }

                    array_push($groups, [
                        'name' => $envN . ' - ' . $skillN . ' ' . $genderN,
                        'subgroups' => $subgroups
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

            $currentHolder = $record->getCurrentHolder();

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
            'groups' => $groups
        ];

        if ($req->query->has('html')) {
            return $this->render('record/list.pdf.twig', $data);
        }

        return $this->renderPdf('record/list.pdf.twig', $data, [
            'margin-top' => '12mm',
            'margin-bottom' => '12mm',
            'margin-left' => '24mm',
            'margin-right' => '24mm',

            'orientation' => 'Landscape',

            'footer-html' => $this->renderView('record/list_footer.pdf.twig', $data)
        ]);

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/create", name="record_create", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $record = new Record();
        $form = $this->createForm(RecordType::class, $record);

        $form->handleRequest($request);
        $this->validateRecord($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($record);
            $em->flush();

            return $this->redirectToRoute(
                'record_detail',
                ['id' => $record->getId()]
            );
        }

        return $this->render('record/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/matrix", name="record_matrix", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matrixCreateAction(Request $request)
    {
        $form = $this->createForm(RecordMatrixType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->buildMatrixFromFormData($em, $form->getData());
            $em->flush();

            return $this->redirectToRoute('record_list');
        }

        return $this->render('record/matrix.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/record/{id}", name="record_detail", methods={"GET"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id, Request $request)
    {
        $recordRepository = $this->getDoctrine()->getRepository("AppBundle:Record");
        $record = $recordRepository->find($id);
        if (!$record) {
            throw $this->createNotFoundException(
                'No record found for id ' . $id
            );
        }

        $club_id = $request->query->getInt('club');

        $clubRepository = $this->getDoctrine()->getRepository("AppBundle:Club");
        $club = $clubRepository->find($club_id);
        if (!$club) {
            throw $this->createNotFoundException(
                'No club found for id ' . $club_id
            );
        }

        return $this->render('record/detail.html.twig', [
            'record' => $record,
            'club' => $club
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/{id}/edit", name="record_edit", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Record')->find($id);
        if (!$record) {
            throw $this->createNotFoundException(
                'No record found for id ' . $id
            );
        }

        $form = $this->createForm(RecordType::class, $record);
        $form->handleRequest($request);
        $this->validateRecord($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'record_detail',
                ['id' => $record->getId()]
            );
        }

        return $this->render('record/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/{id}/delete", name="record_delete", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Record')->find($id);

        if (!$record) {
            throw $this->createNotFoundException(
                'No record found for id ' . $id
            );
        }

        if ($request->isMethod("POST")) {
            $em->remove($record);
            $em->flush();

            return $this->redirectToRoute('record_list');
        }

        return $this->render('record/delete.html.twig', [
            'record' => $record
        ]);
    }

    /**
     * @param ObjectManager $em
     * @param array $data
     */
    private function buildMatrixFromFormData($em, $data)
    {
        $round = $data['round'];
        $num_holders = $data['num_holders'];
        $skills = $data['skill'];
        $genders = $data['gender'];
        $bowtypes = $data['bowtype'];

        if (count($genders) === 0) {
            $genders = [null];
        }
        if (count($bowtypes) === 0) {
            $bowtypes = [null];
        }

        foreach ($skills as $skill) {
            foreach ($genders as $gender) {
                foreach ($bowtypes as $bowtype) {
                    $record = new Record();
                    $record->setNumHolders($num_holders);

                    $recordRound = new RecordRound();
                    $recordRound->setRound($round);
                    $recordRound->setSkill($skill);
                    $recordRound->setGender($gender);
                    $recordRound->setBowtype($bowtype);

                    $record->addRound($recordRound);

                    $em->persist($record);
                }
            }
        }
    }

    private function validateRecord(FormInterface $form)
    {
        /** @var Record $record */
        $record = $form->getData();
        /** @var FormInterface[] $roundForms */
        $roundForms = $form->get('rounds');

        foreach ($roundForms as $roundForm) {
            /** @var RecordRound $round */
            $round = $roundForm->getData();

            if ($round->getCount() < 1) {
                $roundForm->get('count')->addError(new FormError("Count must be greater than 1"));
            }

            if ($record->getNumHolders() > 1 && $round->getCount() > 1) {
                $roundForm->get('count')->addError(new FormError("For team rounds count must be 1"));
            }
        }
    }
}
