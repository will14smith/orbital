<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ClubFilterController extends Controller
{
    /**
     * @Route("/__club_filter", name="club_filter", methods={"GET"})
     */
    public function filterAction()
    {
        $clubRepository = $this->getDoctrine()->getRepository("AppBundle:Club");

        $clubs = $clubRepository->findAll();

        return $this->render('club/filter.html.twig', [
            'clubs' => $clubs
        ]);
    }
}