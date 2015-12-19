<?php

namespace AppBundle\Services\Badges;

use AppBundle\Entity\Badge;
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

        $this->colours = array_map(function ($badge) { return new ColourBadge($badge); }, $badges);
    }

    public function handle(Score $score)
    {
        // must be accepted
        if(!$score->isAccepted()) { return; }
        // must be at a competition
        if(!$score->getCompetition()) { return; }

        $scoredColour = $this->getColour($score);
        if(!$scoredColour) { return; }

        $person = $score->getPerson();
        $currentColour = $this->getCurrent($person);

        if(!$scoredColour->isBetterThan($currentColour)) { return; }

        // award badge
        $this->award($scoredColour, $person);
    }

    /**
     * @param Person $person
     * @return ColourBadge|null
     */
    private function getCurrent(Person $person)
    {
        // get current badges
        $badgeHolderRepository = $this->doctrine->getRepository('AppBundle:BadgeHolder');
        $badges = $badgeHolderRepository->findByIdentAndPerson(self::IDENT, $person->getId());

        $badgeIds = array_map(function (Badge $badge) { return $badge->getId(); }, $badges);

        // find highest ranked badge
        return $this->getHighestWhere(function(ColourBadge $colour) use($badgeIds) {
           return in_array($colour->getId(), $badgeIds);
        });
    }

    /**
     * @param Score $score
     * @return ColourBadge|null
     */
    private function getColour(Score $score)
    {
        return $this->getHighestWhere(function (ColourBadge $colour) use ($score) {
            return $colour->isLowerThan($score);
        });
    }

    /**
     * @param ColourBadge $colour
     * @param Person $person
     */
    private function award(ColourBadge $colour, Person $person)
    {
        // convert ColourBadge to BadgeHolder

        // throw new \Exception("TODO");
    }

    /**
     * @param callable $filter
     * @return ColourBadge|null
     */
    private function getHighestWhere(callable $filter)
    {
        $result = null;

        foreach($this->colours as $colour) {
            if($filter($colour) && $colour->isBetterThan($result)) {
                $result = $colour;
            }
        }

        return $result;
    }
}