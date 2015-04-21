<?php


namespace AppBundle\Services\Enum;


class HandicapType
{
    const INITIAL = 'initial';
    const UPDATE = 'update';
    const REASSESS = 'reassess';

    public static $choices = [
        HandicapType::INITIAL => 'Initial',
        HandicapType::UPDATE => 'Update',
        HandicapType::REASSESS => 'Reassessment',
    ];
}
