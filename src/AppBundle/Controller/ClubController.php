<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Club;
use AppBundle\Form\ClubType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClubController extends Controller
{
    /**
     * @Route("/clubs", name="club_list")
     */
    public function indexAction()
    {
        $clubRepository = $this->getDoctrine()->getRepository("AppBundle:Club");

        $clubs = $clubRepository->findAll();

        return $this->render('club/list.html.twig', array(
            'clubs' => $clubs
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/club/create", name="club_create")
     */
    public function createAction(Request $request)
    {
        $club = new Club();
        $form = $this->createForm(new ClubType(), $club);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($club);
            $em->flush();

            return $this->redirectToRoute(
                'club_detail',
                array('id' => $club->getId())
            );
        }

        return $this->render('club/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/club/{id}", name="club_detail")
     */
    public function detailAction($id)
    {
        $clubRepository = $this->getDoctrine()->getRepository("AppBundle:Club");

        $club = $clubRepository->find($id);
        if (!$club) {
            throw $this->createNotFoundException(
                'No club found for id ' . $id
            );
        }

        return $this->render('club/detail.html.twig', array(
            'club' => $club
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/club/{id}/edit", name="club_edit")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('AppBundle:Club')->find($id);
        if (!$club) {
            throw $this->createNotFoundException(
                'No club found for id ' . $id
            );
        }

        $form = $this->createForm(new ClubType(), $club);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'club_detail',
                array('id' => $club->getId())
            );
        }

        return $this->render('club/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/club/{id}/delete", name="club_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $club = $em->getRepository('AppBundle:Club')->find($id);

        if (!$club) {
            throw $this->createNotFoundException(
                'No club found for id ' . $id
            );
        }

        if ($request->isMethod("POST")) {
            $em->remove($club);
            $em->flush();

            return $this->redirectToRoute('club_list');
        }

        return $this->render('club/delete.html.twig', array(
            'club' => $club
        ));
    }
}