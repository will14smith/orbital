<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompetitionResultsController extends Controller
{
    /**
     * @Route("/competition/{id}/results", name="competition_results", methods={"GET"})
     *
     * @param Competition $competition
     * @param Request $request
     *
     * @return Response
     */
    public function resultsAction(Competition $competition, Request $request)
    {
        $session = null;
        if($request->query->has('session')) {
            $sessionRepository = $this->getDoctrine()->getRepository('AppBundle:CompetitionSession');
            $session = $sessionRepository->find(intval($request->query->get('session'), 10));
        }

        $results = $this->get('orbital.competition.result_manager')
            //TODO pass more filters
            ->getResults($competition, $session);

        return $this->render('competition/results.html.twig', [
            'results' => $results,
            'competition' => $competition,
            'session' => $session
        ]);
    }
}
