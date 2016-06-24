<?php

namespace AppBundle\Services\Approvals\Providers;

use AppBundle\Entity\Club;
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
        $records = $repository->getUnconfirmed();

        return array_map(function (RecordHolder $recordHolder) use ($url) {
            return $this->createApprovalItem($url, $recordHolder);
        }, $records);
    }

    /**
     * @param Registry $doctrine
     * @param UrlGeneratorInterface $url
     * @param Club $club
     *
     * @return ApprovalQueueItem[]
     */
    function getItemsByClub(Registry $doctrine, UrlGeneratorInterface $url, Club $club)
    {
        $repository = $doctrine->getRepository('AppBundle:RecordHolder');
        $records = $repository->getUnconfirmedByClub($club);

        return array_map(function (RecordHolder $recordHolder) use ($url) {
            return $this->createApprovalItem($url, $recordHolder);
        }, $records);    }

    /**
     * @param RecordHolder $recordHolder
     * @param $url
     * @return ApprovalQueueItem
     */
    function createApprovalItem(UrlGeneratorInterface $url, RecordHolder $recordHolder)
    {
        $record = $recordHolder->getRecord();

        return new ApprovalQueueItem('record', 'Record' . ' - ' . (string)$record, $url->generate('record_detail', ['id' => $record->getId(), 'club' => $recordHolder->getClub()->getId()]), $recordHolder);
    }
}