<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\CompetitionSessionRound;
use AppBundle\Form\Type\CompetitionSessionType;
use AppBundle\Services\Competitions\CompetitionManager;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompetitionScoringController extends Controller
{
    /**
     * @Security("is_granted('START', session)")
     * @Route("/competition/{competition_id}/session/{session_id}/start", name="competition_session_start", methods={"GET", "POST"})
     * @ParamConverter("session", class="AppBundle:CompetitionSession", options={"id" = "session_id"})
     *
     * @param CompetitionSession $session
     *
     * @return Response
     */
    public function startAction(CompetitionSession $session)
    {
        $em = $this->getDoctrine()->getManager();

        CompetitionManager::startSession($em, $session);

        return $this->redirectToRoute('competition_detail', ['id' => $session->getCompetition()->getId()]);
    }

    /**
     * @Security("is_granted('SCORE', session)")
     * @Route("/competition/{competition_id}/session/{session_id}/score", name="competition_session_score", methods={"GET"})
     * @ParamConverter("session", class="AppBundle:CompetitionSession", options={"id" = "session_id"})
     *
     * @param CompetitionSession $session
     *
     * @return Response
     */
    public function scoreAction(CompetitionSession $session)
    {
        $rounds = [];
        foreach($session->getRounds() as $sessionRound) {
            $round = $sessionRound->getRound();

            $rounds[$round->getId()] = $round;
        }

        $targets = [];
        foreach($session->getEntries() as $entry) {
            $bossNumber = $entry->getBossNumber();
            $targetNumber = $entry->getTargetNumber();

            if(!$bossNumber || !$targetNumber) {
                continue;
            }

            if(!isset($targets[$bossNumber])) {
                $targets[$bossNumber] = [];
            }

            $targets[$bossNumber][$targetNumber] = $entry->getRound()->getId();
        }

        return $this->render('competition/score.html.twig', [
            'session' => $session,
            'rounds' => $rounds,
            'targets' => $targets
        ]);
    }

    /**
     * @Security("is_granted('END', session)")
     * @Route("/competition/{competition_id}/session/{session_id}/end", name="competition_session_end", methods={"GET", "POST"})
     * @ParamConverter("session", class="AppBundle:CompetitionSession", options={"id" = "session_id"})
     *
     * @param CompetitionSession $session
     *
     * @return Response
     */
    public function endAction(CompetitionSession $session)
    {
        throw new \Exception("Not Implemented");
    }
}
