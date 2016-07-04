<?php

namespace AppBundle\Services\Badges;

use AppBundle\Entity\Badge;
use AppBundle\Entity\BadgeHolder;
use AppBundle\Entity\Club;
use AppBundle\Entity\Person;
use AppBundle\Entity\Score;

class ColoursHandler extends BaseHandler
{
    const IDENT = 'colour';

    /**
     * @var ColourBadge[]
     */
    private $colours;

    public function __construct($doctrine, $badges)
    {
        parent::__construct($doctrine, $badges);

        $this->colours = array_map(function ($badge) {
            return new ColourBadge($badge);
        }, $badges);
    }

    public function handle(Score $score)
    {
        // must be accepted
        if (!$score->isAccepted()) {
            return;
        }
        // must be at a competition
        if (!$score->getCompetition()) {
            return;
        }

        $scoredColour = $this->getColour($score);
        if (!$scoredColour) {
            return;
        }

        $currentColour = $this->getCurrent($score->getClub(), $score->getPerson());
        if (!$scoredColour->isBetterThan($currentColour)) {
            return;
        }

        // award badge
        $this->award($scoredColour, $score);
    }

    /**
     * @param Club   $club
     * @param Person $person
     *
     * @return ColourBadge|null
     */
    private function getCurrent(Club $club, Person $person)
    {
        // get current badges
        $badgeHolderRepository = $this->doctrine->getRepository('AppBundle:BadgeHolder');
        $badges = $badgeHolderRepository->findByIdentAndPerson(self::IDENT, $person->getId());

        $badgeIds = array_map(function (Badge $badge) {
            return $badge->getId();
        }, $badges);

        // find highest ranked badge
        return $this->getHighestWhere(function (ColourBadge $colour) use ($badgeIds, $club) {
            return $club->getId() == $colour->getBadge()->getClub()->getId() && in_array($colour->getId(), $badgeIds);
        });
    }

    /**
     * @param Score $score
     *
     * @return ColourBadge|null
     */
    private function getColour(Score $score)
    {
        return $this->getHighestWhere(function (ColourBadge $colour) use ($score) {
            return $score->getClub()->getId() == $colour->getBadge()->getClub()->getId() && $colour->isLowerThan($score);
        });
    }

    /**
     * @param ColourBadge $colour
     * @param Score       $score
     */
    private function award(ColourBadge $colour, Score $score)
    {
        // convert ColourBadge to BadgeHolder
        $badgeHolder = new BadgeHolder();

        $badgeHolder->setDateAwarded($score->getDateShot());

        $badgeHolder->setBadge($colour->getBadge());
        $badgeHolder->setPerson($score->getPerson());

        $em = $this->doctrine->getManager();
        $em->persist($badgeHolder);
        $em->flush();
    }

    /**
     * @param callable $filter
     *
     * @return ColourBadge|null
     */
    private function getHighestWhere(callable $filter)
    {
        $result = null;

        foreach ($this->colours as $colour) {
            if ($filter($colour) && $colour->isBetterThan($result)) {
                $result = $colour;
            }
        }

        return $result;
    }
}
