<?php

namespace AppBundle\Services\Enum;

class BadgeState
{
    const UNCONFIRMED = 'unconfirmed';
    const CONFIRMED = 'confirmed';
    const MADE = 'made';
    const DELIVERED = 'delivered';

    public static $choices = [
        self::UNCONFIRMED => 'Unconfirmed',
        self::CONFIRMED => 'Confirmed',
        self::MADE => 'Made',
        self::DELIVERED => 'Delivered',
    ];
}
