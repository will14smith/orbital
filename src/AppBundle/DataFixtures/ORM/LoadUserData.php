<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Person;
use AppBundle\Services\Enum\Skill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new Person();
        $admin->setName("Admin User");
        $admin->setNamePreferred("Admin");
        $admin->setCuser('admin');
        $admin->setAdmin(true);
        $this->init($admin);

        $user = new Person();
        $user->setName("Normal User");
        $user->setNamePreferred("Normal");
        $user->setAdmin(false);
        $user->setClub($this->getReference('club-1'));
        $this->init($user);

        $this->addReference('person-admin', $admin);
        $this->addReference('person-user', $user);

        $manager->persist($admin);
        $manager->persist($user);

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
        return 2;
    }
}
