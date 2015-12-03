<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Form\Type\CompetitionType;
use AppBundle\Services\Competitions\CompetitionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompetitionController extends Controller
{
    /**
     * @Route("/competitions", name="competition_list", methods={"GET"})
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Competition");
        $competitions = $repository->findAll();

        return $this->render('competition/list.html.twig', [
            'competitions' => $competitions
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/create", name="competition_create", methods={"GET", "POST"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $competition = new Competition();
        $form = $this->createForm(new CompetitionType(), $competition);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($competition);
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/competition/{id}", name="competition_detail", methods={"GET"})
     *
     * @param Competition $competition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction(Competition $competition)
    {
        return $this->render('competition/detail.html.twig', [
            'competition' => $competition
        ]);
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/open", name="competition_open", methods={"GET"})
     *
     * @param Competition $competition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function openAction(Competition $competition)
    {
        $em = $this->getDoctrine()->getManager();

        $competition->open();
        $em->flush();

        return $this->redirectToRoute('competition_detail', ['id' => $competition->getId()]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/close", name="competition_close", methods={"GET"})
     *
     * @param Competition $competition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closeAction(Competition $competition)
    {
        $em = $this->getDoctrine()->getManager();

        $competition->close();
        $em->flush();

        return $this->redirectToRoute('competition_detail', ['id' => $competition->getId()]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/assign_targets", name="competition_assign_targets", methods={"GET"})
     *
     * @param Competition $competition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function assignTargetsAction(Competition $competition)
    {
        $em = $this->getDoctrine()->getManager();

        CompetitionManager::assignTargets($competition);

        $em->flush();

        return $this->redirectToRoute('competition_detail', ['id' => $competition->getId()]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/edit", name="competition_edit", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $competition = $em->getRepository('AppBundle:Competition')->find($id);
        if (!$competition) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        $form = $this->createForm(new CompetitionType(), $competition);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/delete", name="competition_delete", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $competition = $em->getRepository('AppBundle:Competition')->find($id);

        if (!$competition) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        if ($request->isMethod("POST")) {
            $em->remove($competition);
            $em->flush();

            return $this->redirectToRoute('competition_list');
        }

        return $this->render('competition/delete.html.twig', [
            'competition' => $competition
        ]);
    }
}
