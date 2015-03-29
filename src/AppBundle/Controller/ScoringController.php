<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Score;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ScoringController extends Controller
{
    /**
     * @Security("is_granted('EDIT', score)")
     * @Route("/score/{id}/input", name="score_input")
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inputAction(Score $score)
    {
        return $this->render('score/live.html.twig', [
            'score' => $score,
            'scoring' => true
        ]);
    }

    /**
     * @Route("/score/{id}/live", name="score_live")
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function liveAction(Score $score)
    {
        return $this->render('score/live.html.twig', [
            'score' => $score
        ]);
    }

    /**
     * @Route("/score/{id}/arrows", name="score_data_arrows")
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dataAction(Score $score)
    {
        //TODO return arrows
    }
    /**
     * @Route("/score/{id}/arrows/add", name="score_data_arrows_add", methods={"POST"})
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Score $score)
    {
        //TODO add arrows
    }
    /**
     * @Route("/score/{id}/arrows/amend", name="score_data_arrows_amend", methods={"POST"})
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function amendAction(Score $score)
    {
        //TODO amend arrows
    }
    /**
     * @Route("/score/{id}/arrows/remove", name="score_data_arrows_remove", methods={"POST"})
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Score $score)
    {
        //TODO remove arrows
    }
    /**
     * @Route("/score/{id}/complete", name="score_data_complete", methods={"POST"})
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function completeAction(Score $score)
    {
        //TODO mark score as complete
    }
}