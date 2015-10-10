<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PersonHandicap;
use AppBundle\Form\Type\ReassessType;
use AppBundle\Services\Enum\HandicapType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PersonHandicapController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/reassess", name="person_handicap_reassess", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handicapReassessAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $data = [];
        $form = $this->createForm(new ReassessType(), $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->get('orbital.handicap.manager')->reassess($person, $data['start_date'], $data['end_date']);

            return $this->redirectToRoute(
                'person_detail',
                ['id' => $person->getId()]
            );
        }

        return $this->render('person/reassess.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/hc_manual", name="person_handicap_manual", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handicapManualAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $handicap = new PersonHandicap();
        $handicap->setPerson($person);
        $handicap->setType(HandicapType::MANUAL);
        $handicap->setDate(new \DateTime('now'));

        $form = $this->createFormBuilder($handicap)
            ->add('handicap', 'text')
            ->add('indoor', 'checkbox', ['required' => false])
            ->add('date', 'date', ['widget' => 'single_text'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($handicap);
            $em->flush();

            $this->get('orbital.handicap.manager')
                ->rebuild($person, $handicap->getIndoor(), $handicap);

            return $this->redirectToRoute(
                'person_detail',
                ['id' => $person->getId()]
            );
        }

        return $this->render('person/create_handicap.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
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

        $handicap_manager->rebuildFromLastManual($person, true);
        $handicap_manager->rebuildFromLastManual($person, false);

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
            $handicap_manager->rebuildFromLastManual($person, true);
            $handicap_manager->rebuildFromLastManual($person, false);
        }

        return $this->redirectToRoute('person_list');
    }
}