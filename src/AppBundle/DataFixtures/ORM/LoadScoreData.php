<?php

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Skill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadScoreData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $score1 = new Score();
        $score1->setPerson($this->getReference('person-user'));
        $score1->setRound($this->getReference('round-1'));
        $score1->setDateShot(new \DateTime('now'));
        $score1->setDateAccepted(new \DateTime('now'));
        $score1->setBowtype(BowType::RECURVE);
        $score1->setCompetition(null);
        $score1->setScore(500);
        $score1->setGolds(25);
        $score1->setHits(60);

        $manager->persist($score1);
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
