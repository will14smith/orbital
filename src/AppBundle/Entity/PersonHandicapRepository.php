<?php

namespace AppBundle\Entity;

use AppBundle\Services\Handicap\HandicapIdentifier;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class PersonHandicapRepository extends EntityRepository
{
    public function exists(Score $score)
    {
        $q = $this->createQueryBuilder('ph')
            ->select('count(ph.id)')
            ->where('ph.score = :score')
            ->setParameter('score', $score->getId());

        return $q->getQuery()->getSingleScalarResult();
    }

    public function findAfter(HandicapIdentifier $id, \DateTime $date)
    {
        return $this->createQueryBuilder('ph')
            ->where('ph.person = :person')
            ->andWhere('ph.indoor = :indoor')
            ->andWhere('ph.bowType = :bowtype')
            ->andWhere('ph.date > :date')
            ->setParameter('person', $id->getPerson())
            ->setParameter('indoor', $id->isIndoor())
            ->setParameter('bowtype', $id->getBowtype())
            ->setParameter('date', $date, Type::DATE)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param HandicapIdentifier $id
     *
     * @return PersonHandicap|null
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCurrent(HandicapIdentifier $id)
    {
        return $this->createQueryBuilder('ph')
            ->where('ph.person = :person')
            ->andWhere('ph.indoor = :indoor')
            ->andWhere('ph.bowType = :bowtype')

            ->orderBy('ph.date', 'DESC')
            ->setMaxResults(1)

            ->setParameter('person', $id->getPerson())
            ->setParameter('indoor', $id->isIndoor())
            ->setParameter('bowtype', $id->getBowtype())

            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param HandicapIdentifier $id
     * @param \DateTime          $dt
     *
     * @return PersonHandicap|null
     */
    public function findLastBefore(HandicapIdentifier $id, \DateTime $dt)
    {
        return $this->createQueryBuilder('ph')
            ->where('ph.person = :person')
            ->andWhere('ph.indoor = :indoor')
            ->andWhere('ph.bowType = :bowtype')
            ->andWhere('ph.date < :date')

            ->orderBy('ph.date', 'DESC')
            ->setMaxResults(1)

            ->setParameter('person', $id->getPerson())
            ->setParameter('indoor', $id->isIndoor())
            ->setParameter('bowtype', $id->getBowtype())
            ->setParameter('date', $dt, Type::DATE)

            ->getQuery()
            ->getOneOrNullResult();
    }
}
