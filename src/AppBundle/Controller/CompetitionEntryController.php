<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionEntry;
use AppBundle\Form\Type\CompetitionEntryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompetitionEntryController extends Controller {
    /**
     * @Security("is_granted('ENTER', competition)")
     * @Route("/competition/{id}/enter", name="competition_enter", methods={"GET", "POST"})
     *
     * @param Competition $competition
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enterAction(Competition $competition, Request $request)
    {
        $entry = new CompetitionEntry();
        $entry->setCompetition($competition);

        $is_admin = $this->isGranted('ROLE_ADMIN');

        if ($is_admin) {
            $entry->setDateApproved(new \DateTime('now'));
        } else {
            $entry->setPerson($this->getUser());
        }

        if ($entry->getPerson()) {
            if (!$entry->getGender()) {
                $entry->setGender($entry->getPerson()->getGender());
            }
            if (!$entry->getSkill()) {
                $entry->setSkill($entry->getPerson()->getSkill());
            }
            if (!$entry->getBowtype()) {
                $entry->setBowtype($entry->getPerson()->getBowtype());
            }
        }

        $form = $this->createForm(new CompetitionEntryType($is_admin, $competition->getRounds()), $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entry);
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/enter.html.twig', [
            'form' => $form->createView(),
            'competition' => $competition
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/entry/{entry_id}/edit", name="competition_entry_edit", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $entry_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entryEditAction($id, $entry_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('AppBundle:CompetitionEntry')->find($entry_id);
        if (!$entry) {
            throw $this->createNotFoundException(
                'No competition-entry found for id ' . $entry_id
            );
        }
        $competition = $entry->getCompetition();
        if ($competition->getId() != $id) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        $form = $this->createForm(new CompetitionEntryType(true, $competition->getRounds()), $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/entry_edit.html.twig', [
            'form' => $form->createView(),
            'competition' => $competition
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/entry/{entry_id}/approve", name="competition_entry_approve", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $entry_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entryApproveAction($id, $entry_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('AppBundle:CompetitionEntry')->find($entry_id);
        if (!$entry) {
            throw $this->createNotFoundException(
                'No competition-entry found for id ' . $entry_id
            );
        }
        if ($entry->getCompetition()->getId() != $id) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        if (!$entry->getDateApproved()) {
            $entry->setDateApproved(new \DateTime('now'));
            $em->flush();
        }

        return $this->redirectToRoute(
            'competition_detail',
            ['id' => $entry->getCompetition()->getId()]
        );
    }
}
