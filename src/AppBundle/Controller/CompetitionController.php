<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionEntry;
use AppBundle\Form\CompetitionEntryType;
use AppBundle\Form\CompetitionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompetitionController extends Controller
{
    /**
     * @Route("/competitions", name="competition_list")
     */
    public function indexAction()
    {
        $competitionRepository = $this->getDoctrine()->getRepository("AppBundle:Competition");
        $competitions = $competitionRepository->findAll();

        return $this->render('competition/list.html.twig', [
            'competitions' => $competitions
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/create", name="competition_create")
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
     * @Route("/competition/{id}", name="competition_detail")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $competitionRepository = $this->getDoctrine()->getRepository("AppBundle:Competition");

        $competition = $competitionRepository->find($id);
        if (!$competition) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        return $this->render('competition/detail.html.twig', [
            'competition' => $competition
        ]);
    }

    /**
     * @Security("is_granted('ENTER', competition)")
     * @Route("/competition/{id}/enter", name="competition_enter")
     *
     * @param Competition $competition
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enterAction(Competition $competition, Request $request)
    {
        $entry = new CompetitionEntry();
        $entry->setCompetition($competition);

        $is_admin = $this->isGranted('ROLE_ADMIN');

        if ($is_admin) {
            $entry->setDateApproved(new \DateTime('now'));
        } else {
            $entry->setPerson($this->getUser());
        }

        if ($entry->getPerson()) {
            if (!$entry->getGender()) {
                $entry->setGender($entry->getPerson()->getGender());
            }
            if (!$entry->getSkill()) {
                $entry->setSkill($entry->getPerson()->getSkill());
            }
            if (!$entry->getBowtype()) {
                $entry->setBowtype($entry->getPerson()->getBowtype());
            }
        }

        $form = $this->createForm(new CompetitionEntryType($is_admin, $competition->getRounds()), $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entry);
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/enter.html.twig', [
            'form' => $form->createView(),
            'competition' => $competition
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/entry/{entry_id}/edit", name="competition_entry_edit")
     *
     * @param int $id
     * @param int $entry_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entryEditAction($id, $entry_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('AppBundle:CompetitionEntry')->find($entry_id);
        if (!$entry) {
            throw $this->createNotFoundException(
                'No competition-entry found for id ' . $entry_id
            );
        }
        $competition = $entry->getCompetition();
        if ($competition->getId() != $id) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        $form = $this->createForm(new CompetitionEntryType(true, $competition->getRounds()), $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'competition_detail',
                ['id' => $competition->getId()]
            );
        }

        return $this->render('competition/entry_edit.html.twig', [
            'form' => $form->createView(),
            'competition' => $competition
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/entry/{entry_id}/approve", name="competition_entry_approve")
     *
     * @param int $id
     * @param int $entry_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entryApproveAction($id, $entry_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('AppBundle:CompetitionEntry')->find($entry_id);
        if (!$entry) {
            throw $this->createNotFoundException(
                'No competition-entry found for id ' . $entry_id
            );
        }
        if ($entry->getCompetition()->getId() != $id) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        if (!$entry->getDateApproved()) {
            $entry->setDateApproved(new \DateTime('now'));
            $em->flush();
        }

        return $this->redirectToRoute(
            'competition_detail',
            ['id' => $entry->getCompetition()->getId()]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/assign", name="competition_assign")
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function assignAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $competition = $em->getRepository('AppBundle:Competition')->find($id);
        if (!$competition) {
            throw $this->createNotFoundException(
                'No competition found for id ' . $id
            );
        }

        $this->get('orbital.competition.manager')
            ->assignTargets($competition);

        return $this->redirectToRoute(
            'competition_detail',
            ['id' => $competition->getId()]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/edit", name="competition_edit")
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
     * @Route("/competition/{id}/delete", name="competition_delete")
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