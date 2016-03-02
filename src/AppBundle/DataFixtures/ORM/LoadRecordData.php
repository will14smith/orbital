<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Record;
use AppBundle\Entity\RecordHolder;
use AppBundle\Entity\RecordHolderPerson;
use AppBundle\Entity\RecordRound;
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
        $record1->setNumHolders(1);
        $recordRound1 = new RecordRound();
        $recordRound1->setCount(1);
        $recordRound1->setRound($this->getReference('round-1'));
        $recordRound1->setSkill(Skill::SENIOR);
        $record1->addRound($recordRound1);

        $record2 = new Record();
        $record2->setNumHolders(2);
        $recordRound2 = new RecordRound();
        $recordRound2->setCount(1);
        $recordRound2->setRound($this->getReference('round-2'));
        $recordRound2->setSkill(Skill::NOVICE);
        $recordRound2->setBowtype(BowType::RECURVE);
        $record2->addRound($recordRound2);

        $holder = new RecordHolder();
        $holder->setRecord($record1);
        $holder->setDate(new \DateTime('now'));
        $holder->setCompetition($this->getReference('competition-1'));
        $holder->setScore(500);

        $holderPerson = new RecordHolderPerson();
        $holderPerson->setRecordHolder($holder);
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
