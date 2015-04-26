<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Badge;
use AppBundle\Entity\Club;
use AppBundle\Entity\Person;
use AppBundle\Services\Enum\BadgeCategory;
use AppBundle\Services\Enum\Skill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBadgeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $badge1 = new Badge();
        $badge1->setName("Badge 1");
        $badge1->setDescription("This is badge number 1");
        $badge1->setCategory(BadgeCategory::SKILL);
        $badge1->setMultiple(false);

        $badge2 = new Badge();
        $badge2->setName("Badge 2");
        $badge2->setDescription("This is badge number 2");
        $badge2->setCategory(BadgeCategory::COLOUR);
        $badge2->setMultiple(true);

        $manager->persist($badge1);
        $manager->persist($badge2);

        $manager->flush();
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
