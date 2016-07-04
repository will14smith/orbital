<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\RoundupType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/roundup", name="roundup", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function roundupAction(Request $request)
    {
        $data = [
            'start_date' => new \DateTime('1 week ago'),
            'end_date' => new \DateTime('now'),
        ];

        $form = $this->createForm(RoundupType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $roundup = $this->generateRoundup($data['start_date'], $data['end_date'], $data['type']);

            $html = $this->renderView('pdf/roundup.pdf.twig', [
                'blocks' => $roundup,
            ]);

            return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="roundup.pdf"',
                ]
            );
        }

        return $this->render('default/roundup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function generateRoundup($start, $end, array $types)
    {
        $data = [];
        $doctrine = $this->getDoctrine();

        if (in_array('records', $types)) {
            $records = $doctrine->getRepository('AppBundle:Record')
                ->getByRoundup($start, $end);

            $data['records'] = $records;
        }

        if (in_array('badges', $types)) {
            $badges = $doctrine->getRepository('AppBundle:BadgeHolder')
                ->getByRoundup($start, $end);

            $data['badges'] = $badges;
        }

        return $data;
    }
}
