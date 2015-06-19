<?php

namespace AppBundle\Services\Approvals;

interface ApprovalQueueProviderInterface {
    /**
     * @return ApprovalQueueItem[]
     */
    function getItems();
}