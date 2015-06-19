<?php

namespace AppBundle\Services\Approvals;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface ApprovalQueueProviderInterface {
    /**
     * @param Registry $doctrine
     * @param UrlGeneratorInterface $url
     *
     * @return ApprovalQueueItem[]
     */
    function getItems(Registry $doctrine, UrlGeneratorInterface $url);
}