<?php

namespace AppBundle\Services\Approvals\Providers;

use AppBundle\Constants;
use AppBundle\Entity\CompetitionSessionEntry;
use AppBundle\Services\Approvals\ApprovalQueueItem;
use AppBundle\Services\Approvals\ApprovalQueueProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CompetitionEntryApprovalProvider implements ApprovalQueueProviderInterface
{
    /**
     * @param Registry $doctrine
     * @param UrlGeneratorInterface $url
     *
     * @return ApprovalQueueItem[]
     */
    function getItems(Registry $doctrine, UrlGeneratorInterface $url)
    {
        $repository = $doctrine->getRepository('AppBundle:CompetitionSessionEntry');
        $entries = $repository->findBy(['date_approved' => null]);

        return array_map(function (CompetitionSessionEntry $entry) use ($url) {
            $session = $entry->getSession();
            $competition = $session->getCompetition();
            $person = $entry->getPerson();

            $text = sprintf('%s - %s - %s',
                $competition,
                $session->getStartTime()->format(Constants::DATETIME_FORMAT),
                $person);

            return new ApprovalQueueItem('competition_entry', $text, $url->generate('competition_detail', ['id' => $competition->getId()]), $entry);
        }, $entries);
    }
}