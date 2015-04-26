<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Club;
use AppBundle\Entity\Person;
use AppBundle\Services\Enum\Skill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadClubData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $club1 = new Club();
        $club1->setName("Club 1");

        $club2 = new Club();
        $club2->setName("Club 2");

        $this->setReference("club-1", $club1);
        $this->setReference("club-2", $club2);

        $manager->persist($club1);
        $manager->persist($club2);

        $manager->flush();
    }

    private function init(Person $person)
    {
        $person->setSkill(Skill::SENIOR);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
