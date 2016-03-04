<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompetitionRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy([], ['date' => 'DESC']);
    }
}
