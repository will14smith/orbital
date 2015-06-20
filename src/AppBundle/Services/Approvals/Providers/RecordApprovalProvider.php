<?php

namespace AppBundle\Services\Approvals\Providers;

use AppBundle\Entity\RecordHolder;
use AppBundle\Services\Approvals\ApprovalQueueItem;
use AppBundle\Services\Approvals\ApprovalQueueProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RecordApprovalProvider implements ApprovalQueueProviderInterface
{
    /**
     * @param Registry $doctrine
     * @param UrlGeneratorInterface $url
     *
     * @return ApprovalQueueItem[]
     */
    function getItems(Registry $doctrine, UrlGeneratorInterface $url)
    {
        $repository = $doctrine->getRepository('AppBundle:RecordHolder');
        $records = $repository->findBy(['date_confirmed' => null]);

        return array_map(function (RecordHolder $recordHolder) use ($url) {
            $record = $recordHolder->getRecord();

            return new ApprovalQueueItem('record', 'Record' . ' - ' . (string)$record, $url->generate('record_detail', ['id' => $record->getId()]), $recordHolder);
        }, $records);
    }
}