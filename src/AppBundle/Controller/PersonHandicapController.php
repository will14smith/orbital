<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PersonHandicapController extends Controller
{
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
