<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Form\Type\RecordHolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RecordAwardController extends Controller
{
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
     * @Route("/record/{record_id}/confirm/{holder_id}", name="record_holder_confirm", methods={"GET"})
     */
    public function recordConfirmAction($record_id, $holder_id)
    {
        $em = $this->getDoctrine()->getManager();
        $recordHolderRepository = $em->getRepository('AppBundle:RecordHolder');

        $holder = $recordHolderRepository->find($holder_id);
        if (!$holder) {
            throw $this->createNotFoundException(
                'No record holder found for id ' . $holder_id
            );
        }

        $record = $holder->getRecord();

        $current_holder = $record->getCurrentHolder();
        if ($current_holder) {
            $current_holder->setDateBroken($holder->getDate());
        }

        $holder->setDateConfirmed(new \DateTime());

        $em->flush();

        return $this->redirectToRoute('record_detail', ['id' => $record->getId()]);
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
}