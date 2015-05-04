<?php

namespace AppBundle\Services\Leagues;


use AppBundle\Entity\LeagueMatch;
use Doctrine\Bundle\DoctrineBundle\Registry;

class LeagueManager
{
    /** @var LeagueAlgorithmInterface[] */
    private $algos = [];
    /** @var Registry */
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

        return $this->algos[$key];
    }

    /**
     * @return array key => name
     */
    public function getAlgorithmNames()
    {
        $algos = [];

        foreach ($this->algos as $key => $algo) {
            /** @var LeagueAlgorithmInterface $algo */
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

        $this->updateMatch($match);

        $em->flush();
    }

    /**
     * @param LeagueMatch $match
     * @throws \Exception
     */
    public function updateMatch(LeagueMatch $match)
    {
        $league = $match->getLeague();
        $winner = $match->getWinner();
        $loser = $match->getLoser();

        $algo = $this->getAlgorithm($league->getAlgoName());

        list($deltaWinner, $deltaLoser) = $algo->score($match);

        $winner->setPoints($winner->getPoints() + $deltaWinner);
        $loser->setPoints($loser->getPoints() + $deltaLoser);
    }
}
