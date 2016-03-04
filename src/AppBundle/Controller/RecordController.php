<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordRound;
use AppBundle\Form\Type\RecordMatrixType;
use AppBundle\Form\Type\RecordType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RecordController extends Controller
{
    /**
     * @Route("/records", name="record_list", methods={"GET"})
     */
    public function indexAction()
    {
        $recordRepository = $this->getDoctrine()->getRepository("AppBundle:Record");

        $records = $recordRepository->findAll();

        return $this->render('record/list.html.twig', [
            'records' => $records
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $recordRepository = $this->getDoctrine()->getRepository("AppBundle:Record");

        $record = $recordRepository->find($id);
        if (!$record) {
            throw $this->createNotFoundException(
                'No record found for id ' . $id
            );
        }

        return $this->render('record/detail.html.twig', [
            'record' => $record
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
