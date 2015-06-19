<?php

namespace AppBundle\Services\Approvals;

class ApprovalQueueManager {
    /** @var ApprovalQueueProviderInterface[] */
    private $providers = [];

    /**
     * @return ApprovalQueueItem[]
     */
    public function getItems() {
        return array_reduce(array_map(function(ApprovalQueueProviderInterface $item) {
            return $item->getItems();
        }, $this->providers), 'array_merge', []);
    }

    public function addProvider(ApprovalQueueProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

}