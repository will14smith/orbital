<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Form\Type\RecordHolderType;
use AppBundle\Form\Type\RecordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $form = $this->createForm(new RecordType(), $record);

        $form->handleRequest($request);

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

        $form = $this->createForm(new RecordType(), $record);
        $form->handleRequest($request);

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
     * @Route("/record/{id}/award", name="record_award", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function awardAction($id, Request $request)
    {
        $record_repository = $this->getDoctrine()->getRepository('AppBundle:Record');
        $record = $record_repository->find($id);
        if (!$record) {
            throw $this->createNotFoundException(
                'No record found for id ' . $id
            );
        }

        $recordHolder = new RecordHolder();

        $numHolders = $record->getNumHolders();
        for ($i = 0; $i < $numHolders; $i++) {
            $recordHolder->addPerson(new RecordHolderPerson());
        }
        $form = $this->createForm(new RecordHolderType(), $recordHolder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $record_repository->award($record, $recordHolder);

            return $this->redirectToRoute(
                'record_detail',
                ['id' => $record->getId()]
            );
        }

        return $this->render('record/award.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/{id}/revoke", name="record_revoke", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @throws \Exception
     */
    public function revokeAction($id, Request $request)
    {
        throw new \Exception("NOT IMPLEMENTED");
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
}
