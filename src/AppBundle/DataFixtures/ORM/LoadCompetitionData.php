<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Competition;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCompetitionData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $competition1 = new Competition();
        $competition1->setName('Competition 1');
        $competition1->setHosted(true);

        $competition2 = new Competition();
        $competition2->setName('Competition 2');
        $competition2->setHosted(false);

        $manager->persist($competition1);
        $manager->persist($competition2);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }
}
