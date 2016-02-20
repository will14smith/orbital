<?php


namespace AppBundle\Controller;

use AppBundle\Controller\Traits\ProofControllerTrait;
use AppBundle\Entity\League;
use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\LeagueMatchProof;
use AppBundle\Entity\ProofEntity;
use AppBundle\Form\Type\LeagueMatchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LeagueChallengeController extends Controller
{
    use ProofControllerTrait;

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

        $isAdmin = $this->isGranted('ROLE_ADMIN');
        if ($isAdmin) {
            $lm->setDateConfirmed(new \DateTime('now'));
        } else {
            $lm->setChallenger($this->getUser());
        }

        $form = $this->createForm(LeagueMatchType::class, $lm, ['admin' => $isAdmin]);
        $formProof = $form->get('proof');

        $form->handleRequest($request);
        $this->handleProof($formProof);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->saveProof($em, $lm, $formProof);
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
     * @Security("is_granted('ACCEPT', leagueMatch)")
     * @Route("/league/{id}/match/{match_id}/accept", name="league_match_accept", methods={"GET", "POST"})
     * @ParamConverter("leagueMatch", options={"id" = "match_id"})
     *
     * @param League $league
     * @param LeagueMatch $leagueMatch
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmChallengeAction(League $league, LeagueMatch $leagueMatch, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($leagueMatch->getLeague()->getId() != $league->getId()) {
            throw $this->createNotFoundException(
                'No league found for id ' . $league->getId()
            );
        }

        $confirmProof = $this->confirmProof($request);
        if ($confirmProof !== false) {
            return $this->render(':league:proof_confirm.html.twig', [
                'form' => $confirmProof,
                'match' => $leagueMatch
            ]);
        }

        $leagueMatch->setDateConfirmed(new \DateTime('now'));
        $em->flush();

        return $this->redirectToRoute(
            'league_detail',
            ['id' => $league->getId()]
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
