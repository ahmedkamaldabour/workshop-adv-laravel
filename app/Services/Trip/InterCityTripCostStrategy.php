<?php

namespace App\Services\Trip;

use App\Attributes\TripStrategy;
use App\Services\Trip\TripCostStrategy;


#[TripStrategy('intercity')]
class InterCityTripCostStrategy implements TripCostStrategy
{

    private const FUEL_PRICE_PER_KM = 1.2;
    private const VEHICLE_DEPRECIATION_PER_KM = 0.8;


    public function calculate(float $distanceKm, float $durationHours): array
    {
        $fuelCost = $distanceKm * self::FUEL_PRICE_PER_KM;
        $depreciationCost = $distanceKm * self::VEHICLE_DEPRECIATION_PER_KM;
        $totalCost = $fuelCost + $depreciationCost;

        return [
            'total_cost' => round($totalCost, 2),
            'details' => [
                'fuel_cost' => round($fuelCost, 2),
                'vehicle_depreciation' => round($depreciationCost, 2)
            ]
        ];
    }
}