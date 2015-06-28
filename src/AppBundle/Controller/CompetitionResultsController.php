<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
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

        $teamFilter = $request->query->get('team') === 'true';
        $genderFilter = $this->getFilter($request, 'gender', array_keys(Gender::$choices));
        $skillFilter = $this->getFilter($request, 'skill', array_keys(Skill::$choices));
        $bowtypeFilter = $this->getFilterArray($request, 'bowtype', array_keys(BowType::$choices));

        $results = $this->get('orbital.competition.result_manager')
            ->getResults($competition, $session, [
                'team' => $teamFilter,
                'gender' => $genderFilter,
                'skill' => $skillFilter,
                'bowtype' => $bowtypeFilter
            ]);

        return $this->render('competition/results.html.twig', [
            'results' => $results,
            'competition' => $competition,
            'session' => $session
        ]);
    }

    /**
     * @param Request $request
     * @param string $key
     * @param string[] $allowedValues
     *
     * @return string
     */
    private function getFilter(Request $request, $key, array $allowedValues)
    {
        $value = $request->query->get($key);
        if($value === null) {
            return null;
        }

        $value = strtolower($value);
        if(!in_array($value, $allowedValues)) {
            return null;
        }

        return $value;
    }

    /**
     * @param Request $request
     * @param string $key
     * @param string[] $allowedValues
     *
     * @return string[]
     */
    private function getFilterArray(Request $request, $key, array $allowedValues)
    {
        $values = $request->query->get($key);
        if($values === null || !is_array($values) || count($values) === 0) {
            return null;
        }

        $result = [];

        foreach($values as $value) {
            $value = strtolower($value);
            if(in_array($value, $allowedValues)) {
                $result[] = $value;
            }
        }

        return $result;
    }
}
