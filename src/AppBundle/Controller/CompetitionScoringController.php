<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompetitionSession;
use AppBundle\Services\Competitions\CompetitionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        foreach ($session->getRounds() as $sessionRound) {
            $round = $sessionRound->getRound();

            $rounds[$round->getId()] = $round;
        }

        $targets = [];
        foreach ($session->getEntries() as $entry) {
            $bossNumber = $entry->getBossNumber();
            $targetNumber = $entry->getTargetNumber();

            if (!$bossNumber || !$targetNumber) {
                continue;
            }

            if (!isset($targets[$bossNumber])) {
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
     * @Security("is_granted('SCORE', session)")
     * @Route("/competition/{competition_id}/session/{session_id}/flush", name="competition_session_flush", methods={"POST"})
     * @ParamConverter("session", class="AppBundle:CompetitionSession", options={"id" = "session_id"})
     *
     * @param CompetitionSession $session
     * @param Request $request
     *
     * @return Response
     */
    public function flushAction(CompetitionSession $session, Request $request)
    {
        $params = [];
        $content = $request->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true); // 2nd param to get as array
        }

        $em = $this->getDoctrine()->getManager();
        $rejectedData = CompetitionManager::handleFlush($em, $this->getUser(), $session, $params);
        $em->flush();

        if (count($rejectedData) > 0) {
            return JsonResponse::create([
                'success' => false,
                'rejected' => $rejectedData
            ]);
        }

        return JsonResponse::create(['success' => true]);
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
        $em = $this->getDoctrine()->getManager();

        CompetitionManager::endSession($em, $session);

        return $this->redirectToRoute('competition_detail', ['id' => $session->getCompetition()->getId()]);
    }
}
