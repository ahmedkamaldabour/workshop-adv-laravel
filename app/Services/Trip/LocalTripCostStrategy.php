<?php

namespace App\Services\Trip;

use App\Attributes\TripStrategy;
use App\Services\Trip\TripCostStrategy;


#[TripStrategy('local')]
class LocalTripCostStrategy implements TripCostStrategy
{
    private const PRICE_PER_KM = 2.5;
    private const PRICE_PER_HOUR = 15.0;

    public function calculate(float $distanceKm, float $durationHours): array
    {
        $distanceCost = $distanceKm * self::PRICE_PER_KM;
        $timeCost = $durationHours * self::PRICE_PER_HOUR;
        $totalCost = $distanceCost + $timeCost;

        return [
            'total_cost' => round($totalCost, 2),
            'details' => [
                'distance_cost' => round($distanceCost, 2),
                'time_cost' => round($timeCost, 2)
            ]
        ];
    }
}