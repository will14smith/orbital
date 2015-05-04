<?php

namespace AppBundle\Services\Scoring;

use AppBundle\Services\Events\ScoreArrowEvent;
use SocketIOBundle\Service\Client;

class LiveScoringListener
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

        $arrow_data = [
            'id' => $arrow->getId(),
            'score_id' => $arrow->getScore()->getId(),
            'number' => $arrow->getNumber(),
            'value' => $arrow->getValue(),
        ];

        $this->client->send('arrow_added', $arrow_data, true);
    }

    public function arrowUpdated(ScoreArrowEvent $event)
    {

    }

    public function arrowRemoved(ScoreArrowEvent $event)
    {

    }
}
