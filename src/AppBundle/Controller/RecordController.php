<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Form\RecordHolderType;
use AppBundle\Form\RecordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RecordController extends Controller
{
    /**
     * @Route("/records", name="record_list")
     */
    public function indexAction()
    {
        $recordRepository = $this->getDoctrine()->getRepository("AppBundle:Record");

        $records = $recordRepository->findAll();

        return $this->render('record/list.html.twig', array(
            'records' => $records
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/create", name="record_create")
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
                array('id' => $record->getId())
            );
        }

        return $this->render('record/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/record/{id}", name="record_detail")
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

        return $this->render('record/detail.html.twig', array(
            'record' => $record
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/{id}/edit", name="record_edit")
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
                array('id' => $record->getId())
            );
        }

        return $this->render('record/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/{id}/award", name="record_award")
     */
    public function awardAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('AppBundle:Record')->find($id);
        if (!$record) {
            throw $this->createNotFoundException(
                'No record found for id ' . $id
            );
        }


        /** @var RecordHolder[] $recordHolders */
        $recordHolders = [];
        for ($i = 0; $i < $record->getNumHolders(); $i++) {
            $recordHolders[] = new RecordHolder();
        }
        $data = ['holders' => $recordHolders];

        $form = $this->createForm(new RecordHolderType($record->getNumHolders()), $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO verify this breaks the record

            foreach ($recordHolders as $recordHolder) {
                $recordHolder->setDate($data['date']);
                $recordHolder->setLocation($data['location']);
                $recordHolder->setRecord($record);
                $em->persist($recordHolder);
            }
            $em->flush();

            return $this->redirectToRoute(
                'record_detail',
                array('id' => $record->getId())
            );
        }

        return $this->render('record/award.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/{id}/revoke", name="record_revoke")
     */
    public function revokeAction($id, Request $request)
    {
        throw new \Exception("NOT IMPLEMENTED");
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/record/{id}/delete", name="record_delete")
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

        return $this->render('record/delete.html.twig', array(
            'record' => $record
        ));
    }
}