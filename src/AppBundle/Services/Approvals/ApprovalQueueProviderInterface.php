<?php

namespace AppBundle\Services\Approvals;

use AppBundle\Entity\Club;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface ApprovalQueueProviderInterface
{
    /**
     * @param Registry              $doctrine
     * @param UrlGeneratorInterface $url
     *
     * @return ApprovalQueueItem[]
     */
    public function getItems(Registry $doctrine, UrlGeneratorInterface $url);

    /**
     * @param Registry              $doctrine
     * @param UrlGeneratorInterface $url
     * @param Club                  $club
     *
     * @return ApprovalQueueItem[]
     */
    public function getItemsByClub(Registry $doctrine, UrlGeneratorInterface $url, Club $club);
}
