<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Person;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PersonApiController extends ApiController
{
    /**
     * @Route("/api/people", name="api_person_list", methods={"GET"})
     */
    public function indexAction()
    {
        $personRepository = $this->getDoctrine()->getRepository("AppBundle:Person");

        $people = $personRepository->findAll();

        return $this->json([
            'people' => array_map([$this, 'indexMap'], $people)
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function indexMap(Person $person)
    {
        return [
            'id' => $person->getId(),
            'name' => $person->getDisplayName(),

            'skill' => $person->getCurrentSkill(),
            'bowtype' => $person->getBowtype()
        ];
    }
}