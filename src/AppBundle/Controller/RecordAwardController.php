<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Form\Type\RecordHolderType;
use AppBundle\Services\Records\RecordManager;
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
        $em = $this->getDoctrine()->getManager();
        $recordRepository = $em->getRepository('AppBundle:Record');
        /** @var Record $record */
        $record = $recordRepository->find($id);
        if (!$record) {
            throw $this->createNotFoundException(
                'No record found for id ' . $id
            );
        }

        $holder = new RecordHolder();
        $holder->setRecord($record);

        // TODO handle multi-rounds (i.e. Double Portsmouth)
        $numHolders = $record->getNumHolders();
        for ($i = 0; $i < $numHolders; $i++) {
            $holder->addPerson(new RecordHolderPerson());
        }
        $form = $this->createForm(RecordHolderType::class, $holder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            RecordManager::syncHolder($holder);
            RecordManager::approveHolder($record, $holder);

            foreach($holder->getPeople() as $person) {
                $em->persist($person);
            }
            $em->persist($holder);

            $em->flush();

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
     * @Route("/record/{recordId}/confirm/{holderId}", name="record_holder_confirm", methods={"GET"})
     */
    public function recordConfirmAction($recordId, $holderId)
    {
        $em = $this->getDoctrine()->getManager();
        $rhRepository = $em->getRepository('AppBundle:RecordHolder');

        $holder = $rhRepository->find($holderId);
        if (!$holder) {
            throw $this->createNotFoundException(
                'No record holder found for id ' . $holderId
            );
        }

        $record = $holder->getRecord();

        RecordManager::approveHolder($record, $holder);

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