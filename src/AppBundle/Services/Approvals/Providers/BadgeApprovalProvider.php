<?php

namespace AppBundle\Services\Approvals\Providers;

use AppBundle\Entity\BadgeHolder;
use AppBundle\Services\Approvals\ApprovalQueueItem;
use AppBundle\Services\Approvals\ApprovalQueueProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BadgeApprovalProvider implements ApprovalQueueProviderInterface
{
    /**
     * @param Registry $doctrine
     * @param UrlGeneratorInterface $url
     *
     * @return ApprovalQueueItem[]
     */
    function getItems(Registry $doctrine, UrlGeneratorInterface $url)
    {
        $repository = $doctrine->getRepository('AppBundle:BadgeHolder');
        $badges = $repository->findByIncomplete()->getQuery()->getResult();

        return array_map(function (BadgeHolder $badge) use ($url) {
            return new ApprovalQueueItem('badge', (string)$badge, $url->generate('badge_detail', ['id' => $badge->getBadge()->getId()]), $badge);
        }, $badges);
    }
}