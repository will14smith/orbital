<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ApprovalContoller extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/approvals")
     */
    public function actionList()
    {
        $items = $this->get('orbital.approval.manager')->getItems();

        return $this->render('approval/list.html.twig', ['items' => $items]);
    }
}