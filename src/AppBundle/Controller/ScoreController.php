<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Score;
use AppBundle\Form\ScoreType;
use AppBundle\Services\Enum\BowType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ScoreController extends Controller
{
    /**
     * @Route("/scores", name="score_list")
     */
    public function indexAction()
    {
        $scoreRepository = $this->getDoctrine()->getRepository("AppBundle:Score");

        $scores = $scoreRepository->findAll();

        return $this->render('score/list.html.twig', array(
            'scores' => $scores
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/score/create", name="score_create")
     */
    public function createAction(Request $request)
    {
        $score = new Score();
        $form = $this->createForm(new ScoreType(), $score);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // fill the default values
            if (!$score->getSkill()) {
                $score->setSkill($score->getPerson()->getSkill());
            }
            if (!$score->getBowtype()) {
                $score->setBowtype($score->getPerson()->getBowtype());
            }
            // auto approve admin entered scores
            if ($this->isGranted('ROLE_ADMIN')) {
                $score->accept();
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($score);
            $em->flush();

            return $this->redirectToRoute(
                'score_detail',
                array('id' => $score->getId())
            );
        }

        return $this->render('score/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/score/{id}", name="score_detail")
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

        return $this->render('score/detail.html.twig', array(
            'score' => $score,
            'handicap' => $handicap
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/score/{id}/accept", name="score_accept")
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

        $score->accept();
        $em->flush();

        if($request->query->get('index')) {
            return $this->redirectToRoute('score_list');
        }

        return $this->redirectToRoute(
            'score_detail',
            array('id' => $score->getId())
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/score/{id}/edit", name="score_edit")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $score = $em->getRepository('AppBundle:Score')->find($id);
        if (!$score) {
            throw $this->createNotFoundException(
                'No score found for id ' . $id
            );
        }

        $form = $this->createForm(new ScoreType(true), $score);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'score_detail',
                array('id' => $score->getId())
            );
        }

        return $this->render('score/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/score/{id}/delete", name="score_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $score = $em->getRepository('AppBundle:Score')->find($id);

        if (!$score) {
            throw $this->createNotFoundException(
                'No score found for id ' . $id
            );
        }

        if ($request->isMethod("POST")) {
            $em->remove($score);
            $em->flush();

            return $this->redirectToRoute('score_list');
        }

        return $this->render('score/delete.html.twig', array(
            'score' => $score
        ));
    }
}