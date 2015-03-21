<?php


namespace AppBundle\Services\Handicap;


interface TargetCalculator {
    function calculate($sigma, $target);
}