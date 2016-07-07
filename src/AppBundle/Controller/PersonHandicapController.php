<?php

namespace AppBundle\Controller;

use AppBundle\Constants;
use AppBundle\Entity\PersonHandicap;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
use AppBundle\View\Model\HandicapDetailViewModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PersonHandicapController extends Controller
{
    /**
     * @Route("/person/{id}/handicap", name="person_handicap", methods={"GET"})
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handicapDetailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $handicap_manager = $this->get('orbital.handicap.manager');

        $handicaps = $handicap_manager->getPersonDetails($person);

        return $this->render('person/handicap.html.twig', [
            'person' => $person,
            'handicaps' => $handicaps,
            'handicaps_json' => array_map([$this, 'processHandicapJson'], $handicaps),
        ]);
    }

    /**
     * @param HandicapDetailViewModel $handicap
     *
     * @return mixed
     */
    private function processHandicapJson(HandicapDetailViewModel $handicap)
    {
        return [
            'bowtype' => BowType::display($handicap->getId()->getBowtype()),
            'environment' => Environment::display($handicap->getId()->isIndoor()),
            'data' => array_map(function (PersonHandicap $handicap) {
                return [
                    'date' => $handicap->getDate()->format(Constants::DATE_FORMAT),
                    'handicap' => $handicap->getHandicap(),
                ];
            }, $handicap->getHistoric()),
        ];
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/rebuild", name="person_handicap_rebuild", methods={"GET"})
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handicapRebuildAction($id)
    {
        //TODO this should be a POST
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $handicap_manager = $this->get('orbital.handicap.manager');

        $handicap_manager->rebuildPerson($person);

        return $this->redirectToRoute(
            'person_detail',
            ['id' => $person->getId()]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/handicap/rebuild", name="handicap_rebuild", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handicapRebuildAllAction()
    {
        //TODO this REALLY should be a POST
        $people = $this->getDoctrine()->getRepository('AppBundle:Person')->findAll();

        $handicap_manager = $this->get('orbital.handicap.manager');

        foreach ($people as $person) {
            $handicap_manager->rebuildPerson($person);
        }

        return $this->redirectToRoute('person_list');
    }
}
