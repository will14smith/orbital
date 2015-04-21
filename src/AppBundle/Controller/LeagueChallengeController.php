<?php


namespace AppBundle\Controller;

use AppBundle\Entity\League;
use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\LeagueMatchProof;
use AppBundle\Entity\ProofEntity;
use AppBundle\Form\Type\LeagueMatchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class LeagueChallengeController extends ProofController {
    /**
     * @Security("is_granted('SUBMIT', league)")
     * @Route("/league/{id}/submit", name="league_submit", methods={"GET", "POST"})
     *
     * @param League $league
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submitChallengeAction(League $league, Request $request)
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
     * @Route("/league/{id}/match/{match_id}/accept", name="league_match_accept", methods={"GET", "POST"})
     *
     * @param int $id
     * @param int $match_id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmChallengeAction($id, $match_id, Request $request)
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

        $confirm_proof = $this->confirmProof($request);
        if ($confirm_proof !== false) {
            return $this->render('', [
                'form' => $confirm_proof,
                'match' => $lm
            ]);
        }

        $lm->setDateConfirmed(new \DateTime('now'));
        $em->flush();

        return $this->redirectToRoute(
            'league_detail',
            ['id' => $lm->getLeague()->getId()]
        );
    }

    /**
     * @param $object
     *
     * @return ProofEntity
     *
     */
    protected function createProof($object)
    {
        $proof = new LeagueMatchProof();
        $proof->setMatch($object);

        return $proof;
    }
}
