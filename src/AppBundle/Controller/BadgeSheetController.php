<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BadgeSheet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BadgeSheetController extends Controller {
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badges/sheet", name="badge_sheet", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sheetAction(Request $request)
    {
        $badges = $request->get('badge', []);

        $doctrine = $this->getDoctrine();

        if (is_array($badges) && count($badges) > 0) {
            $badgeHolderRepository = $doctrine->getRepository("AppBundle:BadgeHolder");
            $badges = $badgeHolderRepository->findBy([
                'badge' => $badges,
                'date_made' => null
            ]);

            $em = $doctrine->getManager();
            $sheet = new BadgeSheet($badges);

            $em->persist($sheet);
            $em->flush();

            $html = $this->renderView('pdf/badge_sheet.pdf.twig', [
                'badges' => $badges,
                'sheet' => $sheet
            ]);

            $footer = $this->renderView('pdf/_badge_sheet_footer.pdf.twig', ['sheet' => $sheet]);

            $options = ['footer-html' => $footer];

            return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options), 200, [
                    'Content-Type' => 'application/pdf'
                ]
            );
        } else {
            $badgeRepository = $doctrine->getRepository("AppBundle:Badge");
            $badges = $badgeRepository->findAll();

            return $this->render('badge/sheet.html.twig', ['badges' => $badges]);
        }
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/badges/sheet/mark", name="badge_sheet_mark", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sheetMarkAction(Request $request)
    {
        $sheetId = intval($request->request->get('sheet_id'));
        $markType = intval($request->request->get('mark_type'));

        $badgeSheetRepository = $this->getDoctrine()->getRepository("AppBundle:BadgeSheet");
        $sheet = $badgeSheetRepository->find($sheetId);

        if (!$sheet) {
            throw $this->createNotFoundException(
                'No badge sheet found for id ' . $sheetId
            );
        }

        $badgeSheetRepository->mark($sheet, $markType);

        return $this->redirectToRoute('badge_list');
    }
}