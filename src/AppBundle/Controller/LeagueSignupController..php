<?php


namespace AppBundle\Controller;


use AppBundle\Entity\League;
use AppBundle\Entity\LeaguePerson;
use AppBundle\Form\LeaguePersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LeagueSignupController extends Controller {
    /**
     * @Security("is_granted('SIGNUP', league)")
     * @Route("/league/{id}/signup", name="league_signup")
     *
     * @param League $league
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupAction(League $league, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $lp_repo = $em->getRepository('AppBundle:LeaguePerson');

        $lp = new LeaguePerson();
        $lp->setDateAdded(new \DateTime('now'));
        $lp->setLeague($league);
        $lp->setPoints(0);
        $lp->setInitialPosition($lp_repo->getInitialPosition($league));

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$league->isSignedUp($this->getUser())) {
                $lp->setPerson($this->getUser());

                $em->persist($lp);
                $em->flush();
            }

            return $this->redirectToRoute('league_detail', [
                'id' => $league->getId()
            ]);
        }

        $form = $this->createForm(new LeaguePersonType(), $lp);
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
     * @Route("/league/{id}/signup/{signup_id}", name="league_signup_edit")
     *
     * @param int $id
     * @param int $signup_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupEditAction($id, $signup_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lp = $em->getRepository('AppBundle:LeaguePerson')->find($signup_id);
        if (!$lp) {
            throw $this->createNotFoundException(
                'No league-person found for id ' . $signup_id
            );
        }
        if ($lp->getLeague()->getId() != $id) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $form = $this->createForm(new LeaguePersonType(), $lp);
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
     * @Route("/league/{id}/signup/{signup_id}/delete", name="league_signup_delete")
     *
     * @param int $id
     * @param int $signup_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupRemoveAction($id, $signup_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lp = $em->getRepository('AppBundle:LeaguePerson')->find($signup_id);
        if (!$lp) {
            throw $this->createNotFoundException(
                'No league-person found for id ' . $signup_id
            );
        }
        if ($lp->getLeague()->getId() != $id) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $form = $this->createForm(new LeaguePersonType(), $lp);
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