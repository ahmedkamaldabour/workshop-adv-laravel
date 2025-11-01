<?php

namespace App\Services\Trip;


use App\Attributes\TripStrategy;

#[TripStrategy('international')]
class InternationalTripCostStrategy implements TripCostStrategy
{
    private const FUEL_PRICE_PER_KM = 1.5;
    private const CUSTOMS_FEE_BASE = 500;
    private const CUSTOMS_FEE_PER_100KM = 250;
    private const INSURANCE_RATE = 0.15; // 15% of fuel cost
    public function calculate(float $distanceKm, float $durationHours): array
    {
        $fuelCost = $distanceKm * self::FUEL_PRICE_PER_KM;
        $customsFees = self::CUSTOMS_FEE_BASE + (($distanceKm / 100) * self::CUSTOMS_FEE_PER_100KM);
        $insurance = $fuelCost * self::INSURANCE_RATE;
        $totalCost = $fuelCost + $customsFees + $insurance;

        return [
            'total_cost' => round($totalCost, 2),
            'details' => [
                'base_fuel_cost' => round($fuelCost, 2),
                'custom_fees' => round($customsFees, 2),
                'insurance' => round($insurance, 2)
            ]
        ];
    }

}