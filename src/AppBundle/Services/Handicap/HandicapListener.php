<?php


namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Score;
use AppBundle\Services\Events\ScoreEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;

class HandicapListener
{
    /**
     * @var HandicapManager
     */
    private $manager;
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(HandicapManager $manager, Registry $doctrine)
    {

        $this->manager = $manager;
        $this->doctrine = $doctrine;
    }

    public function score_create(ScoreEvent $event)
    {
        $score = $event->getScore();

        if ($this->should_accept($score, false)) {
            $this->manager->updateHandicap($score);
        }
    }

    public function score_update(ScoreEvent $event)
    {
        $score = $event->getScore();

        if ($this->should_accept($score)) {
            $this->manager->updateHandicap($score);
        }
    }

    /**
     * @param Score $score
     * @param bool $full
     *
     * @return bool
     */
    private function should_accept(Score $score, $full = true)
    {
        if (!$score->getComplete()) {
            return false;
        }
        if (!$score->getDateAccepted() || $score->getDateAccepted() > new \DateTime('now')) {
            return false;
        }

        if (!$full) {
            return true;
        }

        $repo = $this->doctrine->getRepository('AppBundle:PersonHandicap');

        if ($repo->exists($score)) {
            return false;
        }

        return true;
    }
}
