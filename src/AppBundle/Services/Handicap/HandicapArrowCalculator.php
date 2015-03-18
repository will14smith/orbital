<?php


namespace AppBundle\Services\Handicap;


interface HandicapArrowCalculator {
    function calculate($sigma, $target);
}