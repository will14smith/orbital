<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApprovalController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/approvals", name="approvals")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $club_repository = $this->getDoctrine()->getRepository('AppBundle:Club');
        $club_id = $request->query->getInt('club');
        $club = $club_repository->find($club_id);

        if ($club == null) {
            $items = $this->get('orbital.approval.manager')->getItems();
        } else {
            $items = $this->get('orbital.approval.manager')->getItemsByClub($club);
        }

        return $this->render('approval/list.html.twig', [
            'items' => $items,
            'club' => $club
        ]);
    }
}