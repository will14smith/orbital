<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Badge;
use AppBundle\Form\Type\BadgeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class BadgeController extends Controller
{
    /**
     * @Route("/badges", name="badge_list", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $club_id = $request->query->getInt('club');
        if ($club_id == 0) {
            return $this->indexClubAction();
        }

        $badgeRepository = $this->getDoctrine()->getRepository('AppBundle:Badge');
        if ($club_id == -1 && $this->isGranted('ROLE_ADMIN')) {
            $badges = $badgeRepository->findAll();

            return $this->render('badge/list.html.twig', [
                'badges' => $badges,
                'club' => null,
            ]);
        }

        $clubRepository = $this->getDoctrine()->getRepository('AppBundle:Club');
        $club = $clubRepository->find($club_id);
        if ($club == null) {
            return $this->indexClubAction();
        }

        $badges = $badgeRepository->getByClub($club);

        return $this->render('badge/list.html.twig', [
            'badges' => $badges,
            'club' => $club,
        ]);
    }

    private function indexClubAction()
    {
        $clubRepository = $this->getDoctrine()->getRepository('AppBundle:Club');

        $clubs = $clubRepository->findAll();

        return $this->render('badge/list_select_club.html.twig', [
            'clubs' => $clubs,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/create", name="badge_create", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $badge = new Badge();
        $form = $this->createForm(BadgeType::class, $badge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateImage($form, $badge);
            $em = $this->getDoctrine()->getManager();
            $em->persist($badge);
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                ['id' => $badge->getId()]
            );
        }

        return $this->render(
            'badge/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/badge/{id}", name="badge_detail", methods={"GET"})
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $badgeRepository = $this->getDoctrine()->getRepository('AppBundle:Badge');

        $badge = $badgeRepository->find($id);
        if (!$badge) {
            throw $this->createNotFoundException(
                'No badge found for id ' . $id
            );
        }

        return $this->render(
            'badge/detail.html.twig',
            [
                'badge' => $badge,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/edit", name="badge_edit", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $badge = $em->getRepository('AppBundle:Badge')->find($id);
        if (!$badge) {
            throw $this->createNotFoundException(
                'No badge found for id ' . $id
            );
        }

        $form = $this->createForm(BadgeType::class, $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateImage($form, $badge);
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                ['id' => $badge->getId()]
            );
        }

        return $this->render(
            'badge/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/delete", name="badge_delete", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $badge = $em->getRepository('AppBundle:Badge')->find($id);

        if (!$badge) {
            throw $this->createNotFoundException(
                'No badge found for id ' . $id
            );
        }

        if ($request->isMethod('POST')) {
            $em->remove($badge);
            $em->flush();

            return $this->redirectToRoute('badge_list');
        }

        return $this->render(
            'badge/delete.html.twig',
            [
                'badge' => $badge,
            ]
        );
    }

    private function updateImage(Form $form, Badge $badge)
    {
        $image = $form->get('image')->getData();
        if ($image === null) {
            return;
        }

        $importPath = $this->get('orbital.image_importer')->persist($image);

        $badge->setImageName($importPath);
    }
}
