<?php

namespace AppBundle\Services\Approvals;

use AppBundle\Entity\Club;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Router;

class ApprovalQueueManager {
    /** @var ApprovalQueueProviderInterface[] */
    private $providers = [];

    /** @var Registry */
    private $doctrine;
    /** @var Router */
    private $router;

    public function __construct(Registry $doctrine, Router $router)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }
    /**
     * @return ApprovalQueueItem[]
     */
    public function getItems() {
        return array_reduce(array_map(function(ApprovalQueueProviderInterface $item) {
            return $item->getItems($this->doctrine, $this->router);
        }, $this->providers), 'array_merge', []);
    }

    /**
     * @param Club $club
     * 
     * @return ApprovalQueueItem[]
     */
    public function getItemsByClub(Club $club) {
        return array_reduce(array_map(function(ApprovalQueueProviderInterface $item) use ($club) {
            return $item->getItemsByClub($this->doctrine, $this->router, $club);
        }, $this->providers), 'array_merge', []);
    }

    public function addProvider(ApprovalQueueProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

}