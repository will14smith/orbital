<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreProof;
use AppBundle\Form\ScoreType;
use AppBundle\Services\Enum\BowType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     * @Route("/score/create", name="score_create")
     */
    public function createAction(Request $request)
    {
        $score = new Score();
        $form = $this->createForm(new ScoreType(), $score);
        $form_proof = $form->get('proof');

        $form->handleRequest($request);
        $this->handleProof($form_proof);

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
            $this->saveProof($em, $score, $form_proof);
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

        if ($request->query->get('index')) {
            return $this->redirectToRoute('score_list');
        }

        return $this->redirectToRoute(
            'score_detail',
            array('id' => $score->getId())
        );
    }

    /**
     * @Security("is_granted('EDIT', score)")
     * @Route("/score/{id}/edit", name="score_edit")
     */
    public function editAction(Score $score, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ScoreType(true), $score);
        $form_proof = $form->get('proof');

        $form->handleRequest($request);
        $this->handleProof($form_proof);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveProof($em, $score, $form_proof);
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
     * @Security("is_granted('DELETE', score)")
     * @Route("/score/{id}/delete", name="score_delete")
     */
    public function deleteAction(Score $score, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod("POST")) {
            $em->remove($score);
            $em->flush();

            return $this->redirectToRoute('score_list');
        }

        return $this->render('score/delete.html.twig', array(
            'score' => $score
        ));
    }

    private function handleProof(FormInterface $form)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (!$form->isSubmitted()) {
            return;
        }

        $data = $form->getData();
        if (count($data['proof_images']) > 0) {
            return;
        }
        if (trim($data['proof_notes'])) {
            return;
        }

        $form->addError(new FormError('Expecting some proof'));
    }

    private function saveProof(ObjectManager $em, Score $score, FormInterface $form)
    {
        $person = $this->getUser();
        $data = $form->getData();

        // images
        $image_importer = $this->get('orbital.image_importer');

        foreach ($data['proof_images'] as $image) {
            $outpath = $image_importer->persist($image);

            $proof = new ScoreProof();

            $proof->setScore($score);
            $proof->setImageName($outpath);
            $proof->setPerson($person);

            $em->persist($proof);
        }

        // notes
        $notes = trim($data['proof_notes']);
        if (!empty($notes)) {
            $proof = new ScoreProof();

            $proof->setScore($score);
            $proof->setNotes($notes);
            $proof->setPerson($person);

            $em->persist($proof);
        }
    }
}