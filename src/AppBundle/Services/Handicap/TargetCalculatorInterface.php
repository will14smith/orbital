<?php


namespace AppBundle\Services\Handicap;


interface TargetCalculatorInterface
{
    function calculate($sigma, $target);
}
