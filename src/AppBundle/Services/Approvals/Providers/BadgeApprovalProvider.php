<?php

namespace AppBundle\Services\Approvals\Providers;

use AppBundle\Entity\BadgeHolder;
use AppBundle\Entity\Club;
use AppBundle\Services\Approvals\ApprovalQueueItem;
use AppBundle\Services\Approvals\ApprovalQueueProviderInterface;
use AppBundle\Services\Enum\BadgeState;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BadgeApprovalProvider implements ApprovalQueueProviderInterface
{
    /**
     * @param Registry              $doctrine
     * @param UrlGeneratorInterface $url
     *
     * @return ApprovalQueueItem[]
     */
    public function getItems(Registry $doctrine, UrlGeneratorInterface $url)
    {
        $repository = $doctrine->getRepository('AppBundle:BadgeHolder');
        $badges = $repository->findByIncomplete()->getQuery()->getResult();

        return array_map(function (BadgeHolder $holder) use ($url) {
            return $this->createApprovalItem($url, $holder);
        }, $badges);
    }

    /**
     * @param Registry              $doctrine
     * @param UrlGeneratorInterface $url
     * @param Club                  $club
     *
     * @return ApprovalQueueItem[]
     */
    public function getItemsByClub(Registry $doctrine, UrlGeneratorInterface $url, Club $club)
    {
        $repository = $doctrine->getRepository('AppBundle:BadgeHolder');
        $badges = $repository->findByIncompleteAndClub($club)->getQuery()->getResult();

        return array_map(function (BadgeHolder $holder) use ($url) {
            return $this->createApprovalItem($url, $holder);
        }, $badges);
    }

    private function createApprovalItem(UrlGeneratorInterface $url, BadgeHolder $holder)
    {
        $name = (string) $holder . ' - ' . BadgeState::$choices[$holder->getState()];

        return new ApprovalQueueItem('badge', $name, $url->generate('badge_detail', ['id' => $holder->getBadge()->getId()]), $holder);
    }
}
