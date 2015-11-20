<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\ProofControllerTrait;
use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeHolder;
use AppBundle\Entity\BadgeHolderProof;
use AppBundle\Entity\ProofEntity;
use AppBundle\Form\Type\BadgeHolderType;
use AppBundle\Services\Enum\BadgeState;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BadgeAwardController extends Controller
{
    use ProofControllerTrait;

    /**
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @Route("/badge/claim", name="badge_award", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function awardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $badgeHolder = new BadgeHolder();
        $badgeHolder->setDateAwarded(new \DateTime('now'));

        $badge_id = $request->get('badge');
        if ($badge_id) {
            $badge = $em->getRepository('AppBundle:Badge')->find($badge_id);
            if ($badge) {
                $badgeHolder->setBadge($badge);
            }
        }

        $is_admin = $this->isGranted('ROLE_ADMIN');
        if ($is_admin) {
            $badgeHolder->setDateConfirmed(new \DateTime('now'));
        }

        $form = $this->createForm(new BadgeHolderType($is_admin), $badgeHolder);
        $form_proof = $form->get('proof');

        $form->handleRequest($request);
        $this->handleProof($form_proof);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveProof($em, $badgeHolder, $form_proof);
            $em->persist($badgeHolder);
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                ['id' => $badgeHolder->getBadge()->getId()]
            );
        }

        return $this->render(
            'badge/award.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/award/{award_id}", name="badge_award_edit", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $award_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
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

        if ($id != $badgeHolder->getBadge()->getId()) {
            throw $this->createNotFoundException(
                'Badge-holder ' . $award_id . ' not associated with badge ' . $id
            );
        }

        $form = $this->createForm(new BadgeHolderType(true, false), $badgeHolder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                ['id' => $badgeHolder->getBadge()->getId()]
            );
        }

        return $this->render(
            'badge/award_edit.html.twig',
            [
                'form' => $form->createView(),
                'holder' => $badgeHolder,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/award/{award_id}/state", name="badge_award_state", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $award_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function awardStateAction($id, $award_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var BadgeHolder $badge_holder */
        $badge_holder = $em->getRepository('AppBundle:BadgeHolder')->find($award_id);
        if (!$badge_holder) {
            throw $this->createNotFoundException(
                'No badge-holder found for id ' . $award_id
            );
        }

        if ($id != $badge_holder->getBadge()->getId()) {
            throw $this->createNotFoundException(
                'Badge-holder ' . $award_id . ' not associated with badge ' . $id
            );
        }

        switch ($badge_holder->getState()) {
            case BadgeState::UNCONFIRMED:
                $confirm_proof = $this->confirmProof($request);
                if ($confirm_proof !== false) {
                    return $this->render('badge/proof_confirm.html.twig',
                        ['form' => $confirm_proof, 'badge' => $badge_holder]
                    );
                }

                $badge_holder->setDateConfirmed(new \DateTime('now'));
                break;
            case BadgeState::CONFIRMED:
                $badge_holder->setDateMade(new \DateTime('now'));
                break;
            case BadgeState::MADE:
                $badge_holder->setDateDelivered(new \DateTime('now'));
                break;
        }
        $em->flush();

        if ($request->query->get('person')) {
            return $this->redirectToRoute('person_detail', ['id' => $badge_holder->getPerson()->getId()]);
        } else {
            return $this->redirectToRoute('badge_detail', ['id' => $badge_holder->getBadge()->getId()]);
        }
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/award/{award_id}/delete", name="badge_award_delete", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $award_id
     * @param Request $request
     *
     * @throws \Exception
     */
    public function awardDeleteAction($id, $award_id, Request $request)
    {
        throw new \Exception('NOT IMPLEMENTED');
    }

    /**
     * @param $object
     *
     * @return ProofEntity
     */
    protected function createProof($object)
    {
        $proof = new BadgeHolderProof();
        $proof->setBadgeHolder($object);

        return $proof;
    }
}
