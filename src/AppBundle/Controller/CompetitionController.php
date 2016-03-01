<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Form\Type\CompetitionType;
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
        $form = $this->createForm(CompetitionType::class, $competition);

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

        $form = $this->createForm(CompetitionType::class, $competition);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('competition_list');
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
