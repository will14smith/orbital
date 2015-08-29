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
        // default normal users to themselves
        if (!$this->isGranted('ROLE_ADMIN')) {
            $score->setPerson($this->getUser());
        }

        $form = $this->createForm(new ScoreType(), $score);

        $form->handleRequest($request);
        $this->validateScore($form);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$score->getSkill()) {
                $score->setSkill($score->getPerson()->getSkill());
            }
            if (!$score->getBowtype()) {
                $score->setBowtype($score->getPerson()->getBowtype());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($score);
            $em->flush();

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

        if ($score->getComplete()) {
            $handicap = $this->get('orbital.handicap.calculate')->handicapForScore($score);
        } else {
            $handicap = null;
        }

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

        $confirm_proof = $this->confirmProof($request);
        if ($confirm_proof !== false) {
            return $this->render('score/proof_confirm.html.twig', [
                'form' => $confirm_proof,
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

        $form = $this->createForm(new ScoreType(true), $score);
        $form_proof = $form->get('proof');

        $form->handleRequest($request);
        $this->validateScore($form);
        // don't need proof on edit, must have been supplied by user on create

        if ($form->isSubmitted() && $form->isValid()) {
            //  however, if they provide more we should save it
            $this->saveProof($em, $score, $form_proof);
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
        if (!$form->isSubmitted()) {
            return;
        }

        $score_form = $form->get('score');

        /** @var Score $data */
        $data = $form->getData();

        if ($data->getScore() === null) {
            if ($data->getComplete()) {
                $score_form->get('score')->addError(new FormError('Score is required if completed.'));
            } else {
                $data->setScore(0);
            }
        }
        if ($data->getHits() === null) {
            if ($data->getComplete()) {
                $score_form->get('hits')->addError(new FormError('Hits are required if completed.'));
            } else {
                $data->setHits(0);
            }
        }
        if ($data->getGolds() === null) {
            if ($data->getComplete()) {
                $score_form->get('golds')->addError(new FormError('Golds are required if completed.'));
            } else {
                $data->setGolds(0);
            }
        }
    }
}
