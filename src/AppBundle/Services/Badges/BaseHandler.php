<?php

namespace AppBundle\Services\Badges;

use AppBundle\Entity\Badge;
use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class BaseHandler
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var Badge[]
     */
    protected $badges;

    /**
     * BaseHandler constructor.
     *
     * @param Registry $doctrine
     * @param Badge[]  $badges
     */
    public function __construct(Registry $doctrine, array $badges)
    {
        $this->doctrine = $doctrine;
        $this->badges = $badges;
    }
}
