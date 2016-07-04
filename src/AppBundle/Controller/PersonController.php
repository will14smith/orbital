<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Form\Type\PersonType;
use AppBundle\Services\Importing\PersonImportParameters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{
    /**
     * @Route("/people", name="person_list", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $personRepository = $this->getDoctrine()->getRepository('AppBundle:Person');

        $club = $request->query->getInt('club');
        if ($club == 0) {
            $people = $personRepository->findAll();
        } else {
            $people = $personRepository->findBy(['club' => $club]);
        }

        return $this->render('person/list.html.twig', [
            'people' => $people,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/import", name="person_import", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request)
    {
        $importParameters = new PersonImportParameters();
        $form = $this->createFormBuilder($importParameters)
            ->add('file', 'file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $importer = $this->get('orbital.person_importer');
            $importer->import($importParameters);

            return $this->redirectToRoute('person_list');
        }

        return $this->render('person/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/create", name="person_create", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $person = new Person();
        $person->setPassword('');

        $form = $this->createForm(PersonType::class, $person);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute(
                'person_detail',
                ['id' => $person->getId()]
            );
        }

        return $this->render('person/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/person/{id}", name="person_detail", methods={"GET"})
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $doctrine = $this->getDoctrine();
        $personRepository = $doctrine->getRepository('AppBundle:Person');

        /** @var Person $person */
        $person = $personRepository->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $badges = $doctrine->getRepository('AppBundle:BadgeHolder')
            ->findBy([
                'person' => $person->getId(),
            ], ['date_awarded' => 'DESC']);

        $records = $doctrine->getRepository('AppBundle:Record')
            ->findByPerson($person->getId());

        $scoreRepository = $doctrine->getRepository('AppBundle:Score');
        $recent_scores = $scoreRepository
            ->findBy([
                'person' => $person->getId(),
            ], ['date_shot' => 'DESC'], 5);

        $pbs = $scoreRepository->getPersonalBests($person);

        return $this->render('person/detail.html.twig', [
            'person' => $person,
            'badges' => $badges,
            'records' => $records,
            'scores' => $recent_scores,
            'pbs' => $pbs,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/edit", name="person_edit", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $form = $this->createForm(PersonType::class, $person);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'person_detail',
                ['id' => $person->getId()]
            );
        }

        return $this->render('person/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/delete", name="person_delete", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);

        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        if ($request->isMethod('POST')) {
            $em->remove($person);
            $em->flush();

            return $this->redirectToRoute('person_list');
        }

        return $this->render('person/delete.html.twig', [
            'person' => $person,
        ]);
    }
}
