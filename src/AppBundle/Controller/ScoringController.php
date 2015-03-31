<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreArrow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ScoringController extends Controller
{
    /**
     * @Security("is_granted('SCORE', score)")
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
     * @Security("is_granted('SCORE', score)")
     * @Route("/score/{id}/arrows/add", name="score_data_arrows_add", methods={"POST"})
     *
     * @param Score $score
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Score $score, Request $request)
    {
        $params = [];
        $content = $request->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true); // 2nd param to get as array
        }

        /** @var string[] $arrows */
        $arrows = $params['arrows'];
        /** @var int $index */
        $index = $params['index'];

        //TODO check index (is it even needed?
        //TODO check number of arrows < round arrows

        $em = $this->getDoctrine()->getManager();
        foreach ($arrows as $value) {
            $arrow = new ScoreArrow();

            $arrow->setScore($score);
            $arrow->setNumber($index++);
            $arrow->setValue($value);

            $arrow->setInputBy($this->getUser());
            $arrow->setInputTime(new \DateTime('now'));

            $em->persist($arrow);
        }
        $em->flush();

        return JsonResponse::create([
            'success' => true
        ]);
    }

    /**
     * @Security("is_granted('JUDGE', score)")
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
     * @Security("is_granted('JUDGE', score)")
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
     * @Security("is_granted('SIGN', score)")
     * @Route("/score/{id}/complete", name="score_data_complete", methods={"POST"})
     *
     * @param Score $score
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function completeAction(Score $score)
    {
        //TODO check all arrows are scored

        $em = $this->getDoctrine()->getManager();
        $score->setComplete(true);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'url' => $this->generateUrl('score_detail', ['id' => $score->getId()])]);
    }
}