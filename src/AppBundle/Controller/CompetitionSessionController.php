<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;
use AppBundle\Form\Type\CompetitionSessionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompetitionSessionController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{competition_id}/session/create", name="competition_session_create", methods={"GET", "POST"})
     * @ParamConverter("competition", class="AppBundle:Competition", options={"id" = "competition_id"})
     *
     * @param Competition $competition
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Competition $competition, Request $request)
    {
        $session = new CompetitionSession();
        $session->setCompetition($competition);

        $form = $this->createForm(new CompetitionSessionType(), $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach($session->getRounds() as $round) {
                $round->setSession($session);
                $em->persist($round);
            }
            $em->persist($session);
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/create_session.html.twig', [
            'competition' => $competition,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{competition_id}/session/{id}/edit", name="competition_session_edit", methods={"GET", "POST"})
     * @ParamConverter("competition", class="AppBundle:Competition", options={"id" = "competition_id"})
     *
     * @param int $id
     * @param Competition $competition
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($id, Competition $competition, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $em->getRepository('AppBundle:CompetitionSession')->find($id);
        if (!$session) {
            throw $this->createNotFoundException(
                'No competition session found for id ' . $id
            );
        }

        $originalRounds = new ArrayCollection();

        foreach ($session->getRounds() as $round) {
            $originalRounds->add($round);
        }

        $form = $this->createForm(new CompetitionSessionType(), $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($originalRounds as $round) {
                if (!$session->getRounds()->contains($round)) {
                    $em->remove($round);
                }
            }
            foreach($session->getRounds() as $round) {
                $round->setSession($session);
                $em->persist($round);
            }
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/edit_session.html.twig', [
            'competition' => $competition,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{competition_id}/session/{id}/delete", name="competition_session_delete", methods={"GET", "POST"})
     * @ParamConverter("competition", class="AppBundle:Competition", options={"id" = "competition_id"})
     *
     * @param int $id
     * @param Competition $competition
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction($id, Competition $competition, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $em->getRepository('AppBundle:CompetitionSession')->find($id);

        if (!$session) {
            throw $this->createNotFoundException(
                'No competition session found for id ' . $id
            );
        }

        if ($request->isMethod("POST")) {
            $em->remove($session);
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/delete_session.html.twig', [
            'competition' => $competition,
            'session' => $session
        ]);
    }
}
