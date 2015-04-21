<?php

namespace AppBundle\Services\Leagues;


use AppBundle\Entity\LeagueMatch;
use Doctrine\Bundle\DoctrineBundle\Registry;

class LeagueManager
{
    private $algos = [];
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {

        $this->doctrine = $doctrine;
    }

    public function addAlgorithm(LeagueAlgorithmInterface $algorithm)
    {
        $key = $algorithm->getKey();

        if (array_key_exists($key, $this->algos)) {
            throw new \Exception(sprintf('Duplicate league algorithm "%s" detected.', $key));
        }

        $this->algos[$key] = $algorithm;
    }

    /**
     * @param string $key
     *
     * @return LeagueAlgorithmInterface
     * @throws \Exception if algo not found
     */
    public function getAlgorithm($key)
    {
        if (!array_key_exists($key, $this->algos)) {
            throw new \Exception(sprintf('Unable to find league algorithm by name "%s"', $key));
        }

        $class = $this->algos[$key];

        return new $class;
    }

    /**
     * @return array key => name
     */
    public function getAlgorithms()
    {
        $algos = [];

        foreach ($this->algos as $key => $class) {
            /** @var LeagueAlgorithmInterface $algo */
            $algo = new $class;

            $algos[$key] = $algo->getName();
        }

        return $algos;
    }

    /**
     * Update round points
     *
     * @param LeagueMatch $match
     */
    public function handleMatch(LeagueMatch $match)
    {
        $em = $this->doctrine->getManager();

        $league = $match->getLeague();
        $winner = $match->getWinner();
        $loser = $match->getLoser();

        $algo = $this->getAlgorithm($league->getAlgoName());

        list($dp_w, $dp_l) = $algo->score($match);

        if ($dp_w != 0) {
            $winner->setPoints($winner->getPoints() + $dp_w);
        }
        if ($dp_l != 0) {
            $loser->setPoints($loser->getPoints() + $dp_l);
        }

        $em->flush();
    }
}
