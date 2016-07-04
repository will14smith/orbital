<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="round_target")
 */
class RoundTarget implements \JsonSerializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="targets")
     */
    protected $round;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $scoring_zones;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $distance_value;
    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $distance_unit;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $target_value;
    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $target_unit;

    /**
     * @ORM\Column(type="integer")
     */
    protected $arrow_count;
    /**
     * @ORM\Column(type="integer")
     */
    protected $end_size;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set distance_value.
     *
     * @param string $distanceValue
     *
     * @return RoundTarget
     */
    public function setDistanceValue($distanceValue)
    {
        $this->distance_value = $distanceValue;

        return $this;
    }

    /**
     * Get distance_value.
     *
     * @return string
     */
    public function getDistanceValue()
    {
        return $this->distance_value;
    }

    /**
     * Set distance_unit.
     *
     * @param string $distanceUnit
     *
     * @return RoundTarget
     */
    public function setDistanceUnit($distanceUnit)
    {
        $this->distance_unit = $distanceUnit;

        return $this;
    }

    /**
     * Get distance_unit.
     *
     * @return string
     */
    public function getDistanceUnit()
    {
        return $this->distance_unit;
    }

    /**
     * Set target_value.
     *
     * @param string $targetValue
     *
     * @return RoundTarget
     */
    public function setTargetValue($targetValue)
    {
        $this->target_value = $targetValue;

        return $this;
    }

    /**
     * Get target_value.
     *
     * @return string
     */
    public function getTargetValue()
    {
        return $this->target_value;
    }

    /**
     * Set target_unit.
     *
     * @param string $targetUnit
     *
     * @return RoundTarget
     */
    public function setTargetUnit($targetUnit)
    {
        $this->target_unit = $targetUnit;

        return $this;
    }

    /**
     * Get target_unit.
     *
     * @return string
     */
    public function getTargetUnit()
    {
        return $this->target_unit;
    }

    /**
     * Set arrow_count.
     *
     * @param int $arrowCount
     *
     * @return RoundTarget
     */
    public function setArrowCount($arrowCount)
    {
        $this->arrow_count = $arrowCount;

        return $this;
    }

    /**
     * Get arrow_count.
     *
     * @return int
     */
    public function getArrowCount()
    {
        return $this->arrow_count;
    }

    /**
     * Set end_size.
     *
     * @param int $endSize
     *
     * @return RoundTarget
     */
    public function setEndSize($endSize)
    {
        $this->end_size = $endSize;

        return $this;
    }

    /**
     * Get end_size.
     *
     * @return int
     */
    public function getEndSize()
    {
        return $this->end_size;
    }

    /**
     * Set round.
     *
     * @param \AppBundle\Entity\Round $round
     *
     * @return RoundTarget
     */
    public function setRound(Round $round = null)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round.
     *
     * @return \AppBundle\Entity\Round
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set scoring_zones.
     *
     * @param string $scoringZones
     *
     * @return RoundTarget
     */
    public function setScoringZones($scoringZones)
    {
        $this->scoring_zones = $scoringZones;

        return $this;
    }

    /**
     * Get scoring_zones.
     *
     * @return string
     */
    public function getScoringZones()
    {
        return $this->scoring_zones;
    }

    public function jsonSerialize()
    {
        return [
            'scoring_zones' => $this->getScoringZones(),
            'distance' => [
                'value' => $this->getDistanceValue(),
                'unit' => $this->getDistanceUnit(),
            ],
            'target' => [
                'value' => $this->getTargetValue(),
                'unit' => $this->getTargetUnit(),
            ],
            'arrow_count' => $this->getArrowCount(),
            'end_size' => $this->getEndSize(),
        ];
    }
}
