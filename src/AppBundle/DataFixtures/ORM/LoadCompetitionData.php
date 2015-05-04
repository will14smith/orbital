<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionEntry;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
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
        $competition1->setDate(new \DateTime('now'));
        $competition1->setInfoOnly(false);

        $competition2 = new Competition();
        $competition2->setName('Competition 2');
        $competition2->setDate(new \DateTime('+2 days'));
        $competition2->setInfoOnly(true);

        $entry = new CompetitionEntry();
        $entry->setPerson($this->getReference('person-user'));
        $entry->setSkill(Skill::SENIOR);
        $entry->setBowtype(BowType::RECURVE);
        $entry->setGender(Gender::MALE);

        $competition1->addEntry($entry);

        $manager->persist($competition1);
        $manager->persist($competition2);
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
