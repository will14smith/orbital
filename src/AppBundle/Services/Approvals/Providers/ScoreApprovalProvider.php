<?php

namespace AppBundle\Services\Approvals\Providers;

use AppBundle\Entity\Score;
use AppBundle\Services\Approvals\ApprovalQueueItem;
use AppBundle\Services\Approvals\ApprovalQueueProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ScoreApprovalProvider implements ApprovalQueueProviderInterface
{
    /**
     * @param Registry $doctrine
     * @param UrlGeneratorInterface $url
     *
     * @return ApprovalQueueItem[]
     */
    function getItems(Registry $doctrine, UrlGeneratorInterface $url)
    {
        $repository = $doctrine->getRepository('AppBundle:Score');
        $scores = $repository->findByApproval(false)->getQuery()->getResult();

        return array_map(function (Score $score) use ($url) {
            return new ApprovalQueueItem('score', (string)$score, $url->generate('score_detail', ['id' => $score->getId()]), $score);
        }, $scores);
    }
}