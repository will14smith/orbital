<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeHolder;
use AppBundle\Entity\BadgeHolderProof;
use AppBundle\Form\BadgeHolderType;
use AppBundle\Form\BadgeType;
use AppBundle\Services\Enum\BadgeState;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @Route("/badge/claim", name="badge_award")
     */
    public function awardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $badgeHolder = new BadgeHolder();

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
        $this->handle_proof($form_proof);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->save_proof($em, $badgeHolder, $form_proof);
            $em->persist($badgeHolder);
            $em->flush();

            return $this->redirectToRoute(
                'badge_detail',
                array('id' => $badgeHolder->getBadge()->getId())
            );
        }

        return $this->render('badge/award.html.twig', array(
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
                array('id' => $badgeHolder->getBadge()->getId())
            );
        }

        return $this->render('badge/award_edit.html.twig', array(
            'form' => $form->createView(),
            'holder' => $badgeHolder,
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badge/{id}/award/{award_id}/state", name="badge_award_state")
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
                //TODO ask the admin to look at the proof
                $confirm_proof = $this->confirm_proof($badge_holder, $request);
                if ($confirm_proof !== false) {
                    return $confirm_proof;
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
            return $this->redirectToRoute('person_detail', [
                'id' => $badge_holder->getPerson()->getId()
            ]);
        } else {
            return $this->redirectToRoute('badge_detail', [
                'id' => $badge_holder->getBadge()->getId()
            ]);
        }
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

    private function handle_proof(FormInterface $form)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (!$form->isSubmitted()) {
            return;
        }

        $data = $form->getData();
        if (count($data['proof_images']) > 0) {
            return;
        }
        if (trim($data['proof_notes'])) {
            return;
        }

        $form->addError(new FormError('Expecting some proof'));
    }

    private function save_proof(ObjectManager $em, BadgeHolder $badge_holder, FormInterface $form)
    {
        $person = $this->getUser();
        $data = $form->getData();

        // images
        $image_importer = $this->get('orbital.image_importer');

        foreach ($data['proof_images'] as $image) {
            $outpath = $image_importer->persist($image);

            $proof = new BadgeHolderProof();

            $proof->setBadgeHolder($badge_holder);
            $proof->setImageName($outpath);
            $proof->setPerson($person);

            $em->persist($proof);
        }

        // notes
        $notes = trim($data['proof_notes']);
        if (!empty($notes)) {
            $proof = new BadgeHolderProof();

            $proof->setBadgeHolder($badge_holder);
            $proof->setNotes($notes);
            $proof->setPerson($person);

            $em->persist($proof);
        }
    }

    /**
     * @param BadgeHolder $holder
     * @param Request $request
     * @return bool|\Symfony\Component\HttpFoundation\Response
     */
    private function confirm_proof(BadgeHolder $holder, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return false;
        }

        return $this->render('badge/proof_confirm.html.twig', [
            'form' => $form->createView(),
            'badge' => $holder
        ]);
    }
}