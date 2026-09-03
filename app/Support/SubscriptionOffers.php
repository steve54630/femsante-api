<?php

namespace App\Support;

/**
 * Source unique des tarifs premium côté serveur — miroir de SubscriptionOffers côté Android.
 * Le client ne fait plus que demander un palier ("days") ; c'est le serveur qui décide du prix.
 */
class SubscriptionOffers
{
    /** @var array<string, float> Palier ("jours" ou "A vie") => prix de base en euros. */
    private const PRICES = [
        '30' => 9.90,
        '90' => 19.90,
        '365' => 69.90,
        'A vie' => 250.00,
    ];

    /** Paliers éligibles aux codes de réduction. */
    private const REDUCTION_ELIGIBLE_DAYS = ['365', 'A vie'];

    public static function isValidDays(string $days): bool
    {
        return array_key_exists($days, self::PRICES);
    }

    public static function basePrice(string $days): ?float
    {
        return self::PRICES[$days] ?? null;
    }

    public static function isReductionEligible(string $days): bool
    {
        return in_array($days, self::REDUCTION_ELIGIBLE_DAYS, true);
    }
}
