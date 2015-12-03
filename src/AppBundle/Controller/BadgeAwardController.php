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

        $badgeId = $request->get('badge');
        if ($badgeId) {
            $badge = $em->getRepository('AppBundle:Badge')->find($badgeId);
            if ($badge) {
                $badgeHolder->setBadge($badge);
            }
        }

        $isAdmin = $this->isGranted('ROLE_ADMIN');
        if ($isAdmin) {
            $badgeHolder->setDateConfirmed(new \DateTime('now'));
        }

        $form = $this->createForm(new BadgeHolderType($isAdmin), $badgeHolder);
        $formProof = $form->get('proof');

        $form->handleRequest($request);
        $this->handleProof($formProof);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveProof($em, $badgeHolder, $formProof);
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
     * @Route("/badge/{id}/award/{awardId}", name="badge_award_edit", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $awardId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function awardEditAction($id, $awardId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $badgeHolder = $em->getRepository('AppBundle:BadgeHolder')->find($awardId);
        if (!$badgeHolder) {
            throw $this->createNotFoundException(
                'No badge-holder found for id ' . $awardId
            );
        }

        if ($id != $badgeHolder->getBadge()->getId()) {
            throw $this->createNotFoundException(
                'Badge-holder ' . $awardId . ' not associated with badge ' . $id
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
     * @Route("/badge/{id}/award/{awardId}/state", name="badge_award_state", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $awardId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function awardStateAction($id, $awardId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var BadgeHolder $badgeHolder */
        $badgeHolder = $em->getRepository('AppBundle:BadgeHolder')->find($awardId);
        if (!$badgeHolder) {
            throw $this->createNotFoundException(
                'No badge-holder found for id ' . $awardId
            );
        }

        if ($id != $badgeHolder->getBadge()->getId()) {
            throw $this->createNotFoundException(
                'Badge-holder ' . $awardId . ' not associated with badge ' . $id
            );
        }

        switch ($badgeHolder->getState()) {
            case BadgeState::UNCONFIRMED:
                $confirmProof = $this->confirmProof($request);
                if ($confirmProof !== false) {
                    return $this->render('badge/proof_confirm.html.twig',
                        ['form' => $confirmProof, 'badge' => $badgeHolder]
                    );
                }

                $badgeHolder->setDateConfirmed(new \DateTime('now'));
                break;
            case BadgeState::CONFIRMED:
                $badgeHolder->setDateMade(new \DateTime('now'));
                break;
            case BadgeState::MADE:
                $badgeHolder->setDateDelivered(new \DateTime('now'));
                break;
        }
        $em->flush();

        if ($request->query->get('person')) {
            return $this->redirectToRoute('person_detail', ['id' => $badgeHolder->getPerson()->getId()]);
        } else {
            return $this->redirectToRoute('badge_detail', ['id' => $badgeHolder->getBadge()->getId()]);
        }
    }

    // TODO add DELETE method

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
