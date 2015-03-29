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
            //TODO input/edit??
        ];

        $this->client->send('arrow_added', $arrow_data);
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