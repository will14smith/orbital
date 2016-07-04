<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Round;
use AppBundle\Form\Type\RoundType;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Classification;
use AppBundle\Services\Enum\Gender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RoundController extends Controller
{
    /**
     * @Route("/rounds", name="round_list", methods={"GET"})
     */
    public function indexAction()
    {
        $roundRepository = $this->getDoctrine()->getRepository('AppBundle:Round');

        $rounds = $roundRepository->findAllGrouped();

        return $this->render('round/list.html.twig', [
            'groupedRounds' => $rounds[0],
            'categories' => $rounds[1],
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/round/create", name="round_create", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $round = new Round();
        $form = $this->createForm(RoundType::class, $round);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($round);
            $em->flush();

            return $this->redirectToRoute(
                'round_detail',
                ['id' => $round->getId()]
            );
        }

        return $this->render('round/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/round/{id}", name="round_detail", methods={"GET"})
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction($id)
    {
        $doctrine = $this->getDoctrine();

        $roundRepository = $doctrine->getRepository('AppBundle:Round');
        $round = $roundRepository->find($id);
        if (!$round) {
            throw $this->createNotFoundException(
                'No round found for id ' . $id
            );
        }

        return $this->render('round/detail.html.twig', [
            'round' => $round,
        ]);
    }

    /**
     * @Route("/round/{id}/classification", name="round_classification_detail", methods={"GET"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function classificationAction($id, Request $request)
    {
        $doctrine = $this->getDoctrine();

        $roundRepository = $doctrine->getRepository('AppBundle:Round');
        $round = $roundRepository->find($id);
        if (!$round) {
            throw $this->createNotFoundException(
                'No round found for id ' . $id
            );
        }

        $gender = $request->get('gender');
        $bowtype = $request->get('bowtype');

        $genders = [Gender::MALE, Gender::FEMALE];
        $bowtypes = [BowType::RECURVE, BowType::LONGBOW, BowType::COMPOUND, BowType::BAREBOW];
        $classifications = [Classification::THIRD, Classification::SECOND, Classification::FIRST, Classification::BOWMAN, Classification::MASTER_BOWMAN, Classification::GRAND_MASTER_BOWMAN];

        $calc = $this->get('orbital.handicap.classification.calculate');

        if ($gender && $bowtype) {
            $scores = [];

            foreach ($classifications as $classification) {
                $valid = $calc->isValidClassifiation($round, $gender, $bowtype, $classification);
                $score = $calc->calculateRoundScore($round, $gender, $bowtype, $classification);

                $targets = [];
                foreach ($round->getTargets() as $rt) {
                    $targetScore = $calc->calculateTargetScore($rt, $gender, $bowtype, $classification);

                    $targets[] = [
                        'distance' => ['value' => $rt->getDistanceValue(), 'unit' => $rt->getDistanceUnit()],
                        'size' => ['value' => $rt->getTargetValue(), 'unit' => $rt->getTargetUnit()],
                        'score' => round($targetScore, 1),
                        'end_average' => round(($targetScore / $rt->getArrowCount()) * $rt->getEndSize(), 2),
                        'arrow_average' => round($targetScore / $rt->getArrowCount(), 2),
                    ];
                }

                $scores[] = [
                    'classification' => $classification,
                    'score' => $score,
                    'targets' => $targets,
                    'valid' => $valid,
                ];
            }

            return new JsonResponse([
                'round_id' => $round->getId(),
                'classifications' => $scores,
            ]);
        }

        $classificationTable = [];
        foreach ($genders as $gender) {
            foreach ($bowtypes as $bowtype) {
                $scores = [];

                foreach ($classifications as $classification) {
                    $valid = $calc->isValidClassifiation($round, $gender, $bowtype, $classification);

                    $score = $calc->calculateRoundScore($round, $gender, $bowtype, $classification);
                    $scores[] = [
                        'classification' => $classification,
                        'score' => $score,
                        'valid' => $valid,
                    ];
                }

                $classificationTable[] = [
                    'gender' => $gender,
                    'bowtype' => $bowtype,
                    'scores' => $scores,
                ];
            }
        }

        return new JsonResponse([
            'round_id' => $round->getId(),
            'classifications' => $classificationTable,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/round/{id}/edit", name="round_edit", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $round = $em->getRepository('AppBundle:Round')->find($id);
        if (!$round) {
            throw $this->createNotFoundException(
                'No round found for id ' . $id
            );
        }

        $form = $this->createForm(RoundType::class, $round);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'round_detail',
                ['id' => $round->getId()]
            );
        }

        return $this->render('round/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/round/{id}/delete", name="round_delete", methods={"GET", "POST"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $round = $em->getRepository('AppBundle:Round')->find($id);

        if (!$round) {
            throw $this->createNotFoundException(
                'No round found for id ' . $id
            );
        }

        if ($request->isMethod('POST')) {
            $em->remove($round);
            $em->flush();

            return $this->redirectToRoute('round_list');
        }

        return $this->render('round/delete.html.twig', [
            'round' => $round,
        ]);
    }
}
