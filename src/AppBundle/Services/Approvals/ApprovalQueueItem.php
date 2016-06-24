<?php

namespace AppBundle\Services\Approvals;

use AppBundle\Entity\Club;

class ApprovalQueueItem
{
    private $type;
    private $name;
    private $url;
    private $data;

    public function __construct($type, $name, $url, $data = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->url = $url;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}