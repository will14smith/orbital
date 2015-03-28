<?php


namespace AppBundle\Controller;


use AppBundle\Entity\League;
use AppBundle\Form\LeagueType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LeagueController extends Controller
{
    /**
     * @Route("/leagues", name="league_list")
     */
    public function indexAction()
    {
        $leagueRepository = $this->getDoctrine()->getRepository("AppBundle:League");

        $leagues = $leagueRepository->findAll();

        return $this->render('league/list.html.twig', [
            'leagues' => $leagues
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/create", name="league_create")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $league = new League();
        $form = $this->createForm(new LeagueType(), $league);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($league);
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $league->getId()]
            );
        }

        return $this->render('league/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/league/{id}", name="league_detail")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $leagueRepository = $this->getDoctrine()->getRepository("AppBundle:League");

        $league = $leagueRepository->find($id);
        if (!$league) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        return $this->render('league/detail.html.twig', [
            'league' => $league
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/edit", name="league_edit")
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $league = $em->getRepository('AppBundle:League')->find($id);
        if (!$league) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $form = $this->createForm(new LeagueType(), $league);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $league->getId()]
            );
        }

        return $this->render('league/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/delete", name="league_delete")
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $league = $em->getRepository('AppBundle:League')->find($id);

        if (!$league) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        if ($request->isMethod("POST")) {
            $em->remove($league);
            $em->flush();

            return $this->redirectToRoute('league_list');
        }

        return $this->render('league/delete.html.twig', [
            'league' => $league
        ]);
    }
}