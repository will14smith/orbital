<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Person;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new Person();
        $admin->setName('Admin User');
        $admin->setNamePreferred('Admin');
        $admin->setUsername('admin');
        $admin->setEmail('admin@test.com');
        $admin->setPassword('');
        $admin->setEnabled(true);
        $admin->addRole('ROLE_ADMIN');
        $admin->setClub($this->getReference('club-1'));
        $this->init($admin);

        $user = new Person();
        $user->setName('Normal User');
        $user->setNamePreferred('Normal');
        $user->setUsername('user');
        $user->setEmail('user@test.com');
        $user->setPassword('');
        $user->setClub($this->getReference('club-2'));
        $this->init($user);

        $this->addReference('person-admin', $admin);
        $this->addReference('person-user', $user);

        $manager->persist($admin);
        $manager->persist($user);

        $manager->flush();
    }

    private function init(Person $person)
    {
        $person->setDateStarted((new \DateTime('now'))->sub(new \DateInterval('P2Y')));
    }

    /**
     * Get the order of this fixture.
     *
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
}
