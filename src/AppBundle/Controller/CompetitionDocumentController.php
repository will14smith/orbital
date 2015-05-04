<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompetitionDocumentController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/scoresheets", name="competition_scoresheets", methods={"GET"})
     *
     * @param Competition $competition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scoreSheetAction(Competition $competition, Request $request)
    {
        $html = $this->renderView('pdf/scoresheets.pdf.twig', [
            'competition' => $competition
        ]);

        if($request->query->has('plain')) {
            return new Response($html);
        }

        return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, [
                'Content-Type' => 'application/pdf'
            ]
        );
    }
}
