<?php

namespace AppBundle\Services\Leagues;


class LeagueManager
{
    private $algos = [];

    public function addAlgorithm(LeagueAlgorithm $algorithm) {
        $this->algos[$algorithm->getKey()] = $algorithm;
    }

    /**
     * @param string $name
     *
     * @return LeagueAlgorithm
     * @throws \Exception if algo not found
     */
    public function getAlgorithm($name)
    {
        if (!array_key_exists($name, $this->algos)) {
            throw new \Exception(sprintf('Unable to find league algorithm by name "%s"', $name));
        }

        $class = $this->algos[$name];

        return new $class;
    }

    /**
     * @return array key => name
     */
    public function getAlgorithms()
    {
        $algos = [];

        foreach ($this->algos as $key => $class) {
            /** @var LeagueAlgorithm $algo */
            $algo = new $class;

            $algos[$key] = $algo->getName();
        }

        return $algos;
    }
}