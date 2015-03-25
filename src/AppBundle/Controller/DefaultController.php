<?php

namespace AppBundle\Controller;

use AppBundle\Form\RoundupType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/roundup", name="roundup")
     */
    public function roundupAction(Request $request)
    {
        $data = [
            'start_date' => new \DateTime('1 week ago'),
            'end_date' => new \DateTime('now')
        ];

        $form = $this->createForm(new RoundupType(), $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $roundup = $this->generateRoundup($data['start_date'], $data['end_date']);

            return $this->render('default/roundup.html.twig', array_merge([
                'form' => $form->createView(),
            ], $roundup));
        }

        return $this->render('default/roundup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function generateRoundup($start_date, $end_date)
    {
        $doctrine = $this->getDoctrine();

        $records = $doctrine->getRepository('AppBundle:Record')
            //TODO
            ->findAll();
        $badges = $doctrine->getRepository('AppBundle:BadgeHolder')
            ->getByRoundup($start_date, $end_date);

        return [
            'records' => $records,
            'badges' => $badges
        ];
    }
}