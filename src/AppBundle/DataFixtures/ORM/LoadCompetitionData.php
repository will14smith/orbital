<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Competition;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCompetitionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $competition1 = new Competition();
        $competition1->setName('Competition 1');
        $competition1->setDate(new \DateTime('2010-01-01'));

        $competition2 = new Competition();
        $competition2->setName('Competition 2');
        $competition2->setDate(new \DateTime('yesterday'));

        $this->addReference('competition-1', $competition1);
        $this->addReference('competition-2', $competition2);

        $manager->persist($competition1);
        $manager->persist($competition2);

        $manager->flush();
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
