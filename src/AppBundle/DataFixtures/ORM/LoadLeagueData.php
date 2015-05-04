<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\League;
use AppBundle\Entity\LeaguePerson;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLeagueData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $league1 = new League();
        $league1->setName('League 1');
        $league1->setDescription('This is league number 1');
        $league1->setOpenDate(new \DateTime('now'));

        $league2 = new League();
        $league2->setName('League 2');
        $league2->setDescription('This is league number 2');
        $league2->setOpenDate(new \DateTime('now'));

        $entry = new LeaguePerson();
        $entry->setPerson($this->getReference('person-user'));
        $entry->setDateAdded(new \DateTime('now'));
        $entry->setInitialPosition(1);
        $entry->setPoints(10);

        $league1->addPerson($entry);

        $manager->persist($league1);
        $manager->persist($league2);
        $manager->persist($entry);

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
