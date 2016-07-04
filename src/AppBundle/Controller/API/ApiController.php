<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class ApiController extends Controller
{
    protected function json($data)
    {
        $response = new JsonResponse();
        $response->setData($data);

        return $response;
    }
}
