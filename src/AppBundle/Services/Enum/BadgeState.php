<?php


namespace AppBundle\Services\Enum;


class BadgeState {
    const UNCONFIRMED = 'unconfirmed';
    const CONFIRMED = 'confirmed';
    const MADE = 'made';
    const DELIVERED = 'delivered';

    public static $choices = [
        BadgeState::UNCONFIRMED => 'Unconfirmed',
        BadgeState::CONFIRMED => 'Confirmed',
        BadgeState::MADE => 'Made',
        BadgeState::DELIVERED => 'Delivered',
    ];
}