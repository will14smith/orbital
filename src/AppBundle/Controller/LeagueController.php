<?php


namespace AppBundle\Controller;


use AppBundle\Entity\League;
use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\LeagueMatchProof;
use AppBundle\Entity\LeaguePerson;
use AppBundle\Form\LeagueMatchType;
use AppBundle\Form\LeaguePersonType;
use AppBundle\Form\LeagueType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
        $leagueManager = $this->get('orbital.league.manager');

        $league = new League();
        $form = $this->createForm(new LeagueType($leagueManager), $league);

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

        $leagueManager = $this->get('orbital.league.manager');
        $form = $this->createForm(new LeagueType($leagueManager), $league);
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
     * @Security("is_granted('SIGNUP', league)")
     * @Route("/league/{id}/signup", name="league_signup")
     *
     * @param League $league
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupAction(League $league, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $lp_repo = $em->getRepository('AppBundle:LeaguePerson');

        $lp = new LeaguePerson();
        $lp->setDateAdded(new \DateTime('now'));
        $lp->setLeague($league);
        $lp->setPoints(0);
        $lp->setInitialPosition($lp_repo->getInitialPosition($league));

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$league->isSignedUp($this->getUser())) {
                $lp->setPerson($this->getUser());

                $em->persist($lp);
                $em->flush();
            }

            return $this->redirectToRoute('league_detail', [
                'id' => $league->getId()
            ]);
        }

        $form = $this->createForm(new LeaguePersonType(), $lp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lp);
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $league->getId()]
            );
        }

        return $this->render('league/create_person.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/signup/{signup_id}", name="league_signup_edit")
     *
     * @param int $id
     * @param int $signup_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupEditAction($id, $signup_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lp = $em->getRepository('AppBundle:LeaguePerson')->find($signup_id);
        if (!$lp) {
            throw $this->createNotFoundException(
                'No league-person found for id ' . $signup_id
            );
        }
        if ($lp->getLeague()->getId() != $id) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $form = $this->createForm(new LeaguePersonType(), $lp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $lp->getLeague()->getId()]
            );
        }

        return $this->render('league/edit_person.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/signup/{signup_id}/delete", name="league_signup_delete")
     *
     * @param int $id
     * @param int $signup_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupRemoveAction($id, $signup_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lp = $em->getRepository('AppBundle:LeaguePerson')->find($signup_id);
        if (!$lp) {
            throw $this->createNotFoundException(
                'No league-person found for id ' . $signup_id
            );
        }
        if ($lp->getLeague()->getId() != $id) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $form = $this->createForm(new LeaguePersonType(), $lp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($lp);
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $lp->getLeague()->getId()]
            );
        }

        return $this->render('league/delete_person.html.twig', [
            'form' => $form->createView(),
            'person' => $lp,
            'league' => $lp->getLeague(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/init", name="league_init")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function initAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $league = $em->getRepository('AppBundle:League')->find($id);
        if (!$league) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $algo = $this->get('orbital.league.manager')->getAlgorithm($league->getAlgoName());

        $algo->init($league->getPeople()->toArray());
        $em->flush();

        return $this->redirectToRoute('league_detail', [
            'id' => $league->getId()
        ]);
    }

    /**
     * @Security("is_granted('SUBMIT', league)")
     * @Route("/league/{id}/submit", name="league_submit")
     *
     * @param League $league
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submitChallenge(League $league, Request $request)
    {
        $lm = new LeagueMatch();
        $lm->setLeague($league);

        $is_admin = $this->isGranted('ROLE_ADMIN');
        if ($is_admin) {
            $lm->setDateConfirmed(new \DateTime('now'));
        } else {
            $lm->setChallenger($this->getUser());
        }

        $form = $this->createForm(new LeagueMatchType($is_admin), $lm);
        $form_proof = $form->get('proof');

        $form->handleRequest($request);
        $this->handleProof($form_proof);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->saveProof($em, $lm, $form_proof);
            $em->persist($lm);
            $em->flush();

            return $this->redirectToRoute(
                'league_detail',
                ['id' => $lm->getLeague()->getId()]
            );
        }

        return $this->render('league/create_match.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/league/{id}/match/{match_id}/accept", name="league_match_accept")
     *
     * @param int $id
     * @param int $match_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmChallenge($id, $match_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $em->getRepository('AppBundle:LeagueMatch')->find($match_id);
        if (!$lm) {
            throw $this->createNotFoundException(
                'No league-match found for id ' . $match_id
            );
        }
        if ($lm->getLeague()->getId() != $id) {
            throw $this->createNotFoundException(
                'No league found for id ' . $id
            );
        }

        $confirm_proof = $this->confirmProof($lm, $request);
        if ($confirm_proof !== false) {
            return $confirm_proof;
        }

        $lm->setDateConfirmed(new \DateTime('now'));
        $em->flush();

        return $this->redirectToRoute(
            'league_detail',
            ['id' => $lm->getLeague()->getId()]
        );
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

    private function handleProof(FormInterface $form)
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

    private function saveProof(ObjectManager $em, LeagueMatch $match, FormInterface $form)
    {
        $person = $this->getUser();
        $data = $form->getData();

        // images
        $image_importer = $this->get('orbital.image_importer');

        foreach ($data['proof_images'] as $image) {
            $outpath = $image_importer->persist($image);

            $proof = new LeagueMatchProof();

            $proof->setMatch($match);
            $proof->setImageName($outpath);
            $proof->setPerson($person);

            $em->persist($proof);
        }

        // notes
        $notes = trim($data['proof_notes']);
        if (!empty($notes)) {
            $proof = new LeagueMatchProof();

            $proof->setMatch($match);
            $proof->setNotes($notes);
            $proof->setPerson($person);

            $em->persist($proof);
        }
    }

    /**
     * @param LeagueMatch $match
     * @param Request $request
     *
     * @return bool|\Symfony\Component\HttpFoundation\Response
     */
    private function confirmProof(LeagueMatch $match, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return false;
        }

        return $this->render('league/proof_confirm.html.twig', [
            'form' => $form->createView(),
            'match' => $match
        ]);
    }
}