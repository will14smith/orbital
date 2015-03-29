<?php

namespace AppBundle\Services;

use AppBundle\Services\Events\ScoreArrowEvent;
use Nc\Bundle\ElephantIOBundle\Service\Client;

class ScoringListener
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function arrowAdded(ScoreArrowEvent $event)
    {
        $arrow = $event->getArrow();

        $this->client->send('arrow_added', [
            'score' => $arrow->getScore()->getId(),
            'arrow' => $arrow->getNumber()
        ]);
    }

    public function arrowUpdated(ScoreArrowEvent $event)
    {
        $arrow = $event->getArrow();

        $this->client->send('arrow_updated', [
            'score' => $arrow->getScore()->getId(),
            'arrow' => $arrow->getNumber()
        ]);
    }

    public function arrowRemoved(ScoreArrowEvent $event)
    {
        $arrow = $event->getArrow();

        $this->client->send('arrow_removed', [
            'score' => $arrow->getScore()->getId(),
            'arrow' => $arrow->getNumber()
        ]);
    }
}