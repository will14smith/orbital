<?php

namespace SocketIOBundle\Service;


use ElephantIO\Client as Elephant;

/**
 * Client to intereact with elephant.io client
 */
class Client
{
    /** @var Elephant */
    protected $elephantIO;

    /** @var bool */
    private $closed = true;

    public function getElephantIO()
    {
        return $this->elephantIO;
    }

    public function __construct(Elephant $elephantIO)
    {
        $this->elephantIO = $elephantIO;
    }

    /**
     * @param string $eventName event name
     * @param mixed $data data to send must be serializable
     * @param bool $keepAlive
     */
    public function send($eventName, $data, $keepAlive = false)
    {
        $this->closed = false;
        $this->elephantIO->initialize();
        $this->elephantIO->emit($eventName, $data);
        if (!$keepAlive) {
            $this->closed = true;
            $this->elephantIO->close();
        }
    }

    public function close()
    {
        $this->closed = true;
        $this->elephantIO->close();
    }
}
