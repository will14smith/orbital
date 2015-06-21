<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\PdfRenderTrait;
use AppBundle\Entity\Competition;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompetitionDocumentController extends Controller
{
    use PdfRenderTrait;

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/competition/{id}/scoresheets", name="competition_scoresheets", methods={"GET"})
     *
     * @param Competition $competition
     * @param Request $request
     *
     * @return Response
     */
    public function scoreSheetAction(Competition $competition, Request $request)
    {
        return $this->renderPdf('pdf/scoresheets.pdf.twig', [
            'competition' => $competition
        ]);
    }
}
