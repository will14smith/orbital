<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RoundRepository extends EntityRepository
{
    public function findAllGrouped()
    {
        /** @var Round[] $rounds */
        $rounds = $this->findAll();

        $result = [];
        $categories = [];
        foreach ($rounds as $round) {
            $key1 = +$round->getIndoor();

            if (!array_key_exists($key1, $result)) {
                $result[$key1] = [];
            }

            $category = $round->getCategory();
            $key2 = $category->getId();

            if (!array_key_exists($key2, $result[$key1])) {
                if (!array_key_exists(1 - $key1, $result) || !array_key_exists($key2, $result[1 - $key1])) {
                    $categories[$key2] = $category;
                }

                $result[$key1][$key2] = [];
            }

            $result[$key1][$key2][] = $round;
        }

        return [$result, $categories];
    }
}
