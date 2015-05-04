<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Skill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRecordData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $record1 = new Record();
        $record1->setRound($this->getReference('round-1'));
        $record1->setNumHolders(1);
        $record1->setSkill(Skill::SENIOR);

        $record2 = new Record();
        $record2->setRound($this->getReference('round-2'));
        $record2->setNumHolders(2);
        $record2->setSkill(Skill::NOVICE);
        $record2->setBowtype(BowType::RECURVE);

        $holder = new RecordHolder();
        $holder->setDate(new \DateTime('now'));
        $holder->setLocation('Location 1');
        $holder->setScore(500);

        $holderPerson = new RecordHolderPerson();
        $holderPerson->setPerson($this->getReference('person-user'));
        $holderPerson->setScoreValue(500);

        $holder->addPerson($holderPerson);
        $record1->addHolder($holder);

        $manager->persist($record1);
        $manager->persist($record2);
        $manager->persist($holder);
        $manager->persist($holderPerson);

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
