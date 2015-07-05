<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\CompetitionSessionEntry;
use AppBundle\Form\Type\CompetitionEntryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompetitionEntryController extends Controller
{
    /**
     * @Security("is_granted('ENTER', session)")
     * @Route("/competition/{competition_id}/session/{session_id}/enter", name="competition_enter", methods={"GET", "POST"})
     * @ParamConverter("session", class="AppBundle:CompetitionSession", options={"id" = "session_id"})
     *
     * @param CompetitionSession $session
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enterAction(CompetitionSession $session, Request $request)
    {
        $competition = $session->getCompetition();

        $entry = new CompetitionSessionEntry();
        $entry->setSession($session);
        $entry->setDateEntered(new \DateTime());

        $admin = $this->getUser()->isAdmin();
        if (!$admin) {
            $entry->setPerson($this->getUser());
            $entry->setClub($this->getUser()->getClub());
        }

        $form = $this->createForm(new CompetitionEntryType($admin, $session->getRounds()), $entry);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $person = $entry->getPerson();

            $entry->setSkill($person->getSkill());
            $entry->setGender($person->getGender());
            if ($entry->getBowtype() === null) {
                $entry->setBowtype($person->getBowtype());
            }

            //TODO prevent duplicate entries for the same person

            $em = $this->getDoctrine()->getManager();
            $em->persist($entry);
            $em->flush();

            return $this->redirectToRoute('competition_detail', ['id' => $competition->getId()]);
        }

        return $this->render(':competition:enter.html.twig', [
            'competition' => $competition,
            'session' => $session,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ENTER', entry.getSession())")
     * @Route("/competition/{competition_id}/session/{session_id}/approve/{id}", name="competition_entry_approve", methods={"GET"})
     *
     * @param CompetitionSessionEntry $entry
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function approveAction(CompetitionSessionEntry $entry)
    {
        $em = $this->getDoctrine()->getManager();
        $entry->setDateApproved(new \DateTime());
        $em->flush();

        return $this->redirectToRoute('competition_detail', [
            'id' => $entry->getSession()->getCompetition()->getId()
        ]);
    }
}
