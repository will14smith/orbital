<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\ProofControllerTrait;
use AppBundle\Entity\ProofEntity;
use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreProof;
use AppBundle\Form\Type\ScoreType;
use AppBundle\Utilities\DateUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ScoreController extends Controller
{
    use ProofControllerTrait;

    /**
     * @Route("/scores", name="score_list", methods={"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $scoreRepository = $this->getDoctrine()->getRepository("AppBundle:Score");

        switch ($request->query->get('filter', 'all')) {
            case 'mine':
                $query = $scoreRepository->findByPerson($this->getUser()->getId());
                break;
            case 'person':
                $query = $scoreRepository->findByPerson($request->query->get('person'));
                break;
            case 'competition':
                $query = $scoreRepository->findByCompetition(true);
                break;
            case 'unapproved':
                $query = $scoreRepository->findByApproval(false);
                break;
            default:
                $query = $scoreRepository->findAll();
                break;
        }

        $paginator = $this->get('knp_paginator');
        $scores = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('score/list.html.twig', [
            'scores' => $scores
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @Route("/score/create", name="score_create", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $score = new Score();
        $score->setDateShot(DateUtils::getRoundedNow());

        if (!$this->isGranted('ROLE_ADMIN')) {
            // default normal users to themselves
            $score->setPerson($this->getUser());
        } else {
            // auto accept admin entries
            $score->accept();
        }

        // continue
        if ($request->query->has('person')) {
            $score->setPerson(
                $this->getDoctrine()->getRepository('AppBundle:Person')
                    ->find($request->query->get('person')));
        }
        if ($request->query->has('date')) {
            $score->setDateShot(new \DateTime($request->query->get('date')));
        }
        if ($request->query->has('competition')) {
            $score->setCompetition(!!$request->query->get('competition'));
        }
        if ($request->query->has('round')) {
            $score->setRound(
                $this->getDoctrine()->getRepository('AppBundle:Round')
                    ->find($request->query->get('round')));
        }

        $form = $this->createForm(ScoreType::class, $score, ['editing' => false]);

        $form->handleRequest($request);
        $this->validateScore($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($score);
            $em->flush();

            if ($request->get('continue')) {
                return $this->redirectToRoute(
                    'score_create',
                    [
                        'person' => $score->getPerson()->getId(),
                        'date' => $score->getDateShot()->format('Y-m-d'),
                        'competition' => $score->getCompetition(),
                        'round' => $score->getRound()->getId(),
                    ]
                );
            }

            return $this->redirectToRoute(
                'score_detail',
                ['id' => $score->getId()]
            );
        }

        return $this->render('score/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/score/{id}", name="score_detail", methods={"GET"})
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $scoreRepository = $this->getDoctrine()->getRepository("AppBundle:Score");

        $score = $scoreRepository->find($id);
        if (!$score) {
            throw $this->createNotFoundException(
                'No score found for id ' . $id
            );
        }

        $handicap = $this->get('orbital.handicap.calculate')->handicapForScore($score);

        return $this->render('score/detail.html.twig', [
            'score' => $score,
            'handicap' => $handicap
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/score/{id}/accept", name="score_accept", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $score = $em->getRepository('AppBundle:Score')->find($id);
        if (!$score) {
            throw $this->createNotFoundException(
                'No score found for id ' . $id
            );
        }

        $confirmProof = $this->confirmProof($request);
        if ($confirmProof !== false) {
            return $this->render('score/proof_confirm.html.twig', [
                'form' => $confirmProof,
                'score' => $score
            ]);
        }

        $score->accept();
        $em->flush();

        if ($request->query->get('index')) {
            return $this->redirectToRoute('score_list');
        }

        return $this->redirectToRoute(
            'score_detail',
            ['id' => $score->getId()]
        );
    }

    /**
     * @Security("is_granted('EDIT', score)")
     * @Route("/score/{id}/edit", name="score_edit", methods={"GET", "POST"})
     *
     * @param Score $score
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Score $score, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ScoreType::class, $score, ['editing' => true]);

        $form->handleRequest($request);
        $this->validateScore($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'score_detail',
                ['id' => $score->getId()]
            );
        }

        return $this->render('score/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('DELETE', score)")
     * @Route("/score/{id}/delete", name="score_delete", methods={"GET", "POST"})
     *
     * @param Score $score
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Score $score, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod("POST")) {
            $em->remove($score);
            $em->flush();

            return $this->redirectToRoute('score_list');
        }

        return $this->render('score/delete.html.twig', [
            'score' => $score
        ]);
    }

    /**
     * @param $object
     *
     * @return ProofEntity
     */
    protected function createProof($object)
    {
        $proof = new ScoreProof();
        $proof->setScore($object);

        return $proof;
    }

    private function validateScore(FormInterface $form)
    {
        /** @var Score $score */
        $score = $form->getData();
        $person = $score->getPerson();

        if (!$form->isSubmitted()) {
            return;
        }

        if (!$score->getClub()) {
            $defaultClub = $person->getClub();
            $score->setClub($defaultClub);
        }

        if (!$score->getBowtype()) {
            $defaultBowtype = $person->getBowtype();
            if (!$defaultBowtype) {
                $form->get('bowtype')->addError(new FormError('Must provide bow type'));
            } else {
                $score->setBowtype($defaultBowtype);
            }
        }
    }
}
