<?php

namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\LeaguePerson;
use AppBundle\Form\Type\LeaguePersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LeagueSignupController extends Controller
{
    /**
     * @Security("is_granted('SIGNUP', league)")
     * @Route("/league/{id}/signup", name="league_signup", methods={"GET", "POST"})
     *
     * @param League  $league
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupAction(League $league, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $lpRepository = $em->getRepository('AppBundle:LeaguePerson');

        $lp = new LeaguePerson();
        $lp->setDateAdded(new \DateTime('now'));
        $lp->setLeague($league);
        $lp->setPoints(0);
        $lp->setInitialPosition($lpRepository->getInitialPosition($league));

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$league->isSignedUp($this->getUser())) {
                $lp->setPerson($this->getUser());

                $em->persist($lp);
                $em->flush();
            }

            return $this->redirectToRoute('league_detail', [
                'id' => $league->getId(),
            ]);
        }

        $form = $this->createForm(LeaguePersonType::class, $lp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lp);
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $league->getId()]
            );
        }

        return $this->render('league/create_person.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/signup/{signupId}", name="league_signup_edit", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param int     $signupId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupEditAction($id, $signupId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lp = $em->getRepository('AppBundle:LeaguePerson')->find($signupId);
        if (!$lp) {
            throw $this->createNotFoundException(
                'No league-person found for id ' . $signupId
            );
        }
        if ($lp->getLeague()->getId() != $id) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $form = $this->createForm(LeaguePersonType::class, $lp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $lp->getLeague()->getId()]
            );
        }

        return $this->render('league/edit_person.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/signup/{signupId}/delete", name="league_signup_delete", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param int     $signupId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupRemoveAction($id, $signupId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lp = $em->getRepository('AppBundle:LeaguePerson')->find($signupId);
        if (!$lp) {
            throw $this->createNotFoundException(
                'No league-person found for id ' . $signupId
            );
        }
        if ($lp->getLeague()->getId() != $id) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $form = $this->createForm(LeaguePersonType::class, $lp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($lp);
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $lp->getLeague()->getId()]
            );
        }

        return $this->render('league/delete_person.html.twig', [
            'form' => $form->createView(),
            'person' => $lp,
            'league' => $lp->getLeague(),
        ]);
    }
}
