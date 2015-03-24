<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeHolder;
use AppBundle\Form\BadgeHolderType;
use AppBundle\Form\BadgeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BadgeController extends Controller
{
    /**
     * @Route("/badges", name="badge_list")
     */
    public function indexAction()
    {
        $badgeRepository = $this->getDoctrine()->getRepository("AppBundle:Badge");

        $badges = $badgeRepository->findAll();

        return $this->render('badge/list.html.twig', array(
            'badges' => $badges
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/create", name="badge_create")
     */
    public function createAction(Request $request)
    {
        $badge = new Badge();
        $form = $this->createForm(new BadgeType(), $badge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($badge);
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                array('id' => $badge->getId())
            );
        }

        return $this->render('badge/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/badge/{id}", name="badge_detail")
     */
    public function detailAction($id)
    {
        $badgeRepository = $this->getDoctrine()->getRepository("AppBundle:Badge");

        $badge = $badgeRepository->find($id);
        if (!$badge) {
            throw $this->createNotFoundException(
                'No badge found for id ' . $id
            );
        }

        return $this->render('badge/detail.html.twig', array(
            'badge' => $badge
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/edit", name="badge_edit")
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

        $form = $this->createForm(new BadgeType(), $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                array('id' => $badge->getId())
            );
        }

        return $this->render('badge/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/award", name="badge_award")
     */
    public function awardAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $badge = $em->getRepository('AppBundle:Badge')->find($id);
        if (!$badge) {
            throw $this->createNotFoundException(
                'No badge found for id ' . $id
            );
        }

        $badgeHolder = new BadgeHolder();
        $form = $this->createForm(new BadgeHolderType(), $badgeHolder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $badgeHolder->setBadge($badge);
            $em->persist($badgeHolder);
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                array('id' => $badge->getId())
            );
        }

        return $this->render('badge/award.html.twig', array(
            'form' => $form->createView(),
            'badge' => $badge
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/award/{award_id}", name="badge_award_edit")
     */
    public function awardEditAction($id, $award_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $badgeHolder = $em->getRepository('AppBundle:BadgeHolder')->find($award_id);
        if (!$badgeHolder) {
            throw $this->createNotFoundException(
                'No badge-holder found for id ' . $award_id
            );
        }

        if($id != $badgeHolder->getBadge()->getId()) {
            throw $this->createNotFoundException(
                'Badge-holder ' . $award_id . ' not associated with badge ' . $id
            );
        }

        $form = $this->createForm(new BadgeHolderType(), $badgeHolder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                array('id' => $badgeHolder->getBadge()->getId())
            );
        }

        return $this->render('badge/award_edit.html.twig', array(
            'form' => $form->createView(),
            'badge' => $badgeHolder->getBadge()
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/award/{award_id}/delete", name="badge_award_delete")
     */
    public function awardDeleteAction($id, $award_id, Request $request)
    {
        throw new \Exception('NOT IMPLEMENTED');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/delete", name="badge_delete")
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

        if ($request->isMethod("POST")) {
            $em->remove($badge);
            $em->flush();

            return $this->redirectToRoute('badge_list');
        }

        return $this->render('badge/delete.html.twig', array(
            'badge' => $badge
        ));
    }
}