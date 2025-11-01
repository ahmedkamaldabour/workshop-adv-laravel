<?php

namespace App\Services\Trip;

use App\Attributes\TripStrategy;

#[TripStrategy('international')]
class InternationalTripCostStrategy implements TripCostStrategy
{
    // Business Rules: fuel + max(customs_min, fuel*20%) + insurance + border_crossing
    private const FUEL_COST_PER_KM = 1.20;
    private const CUSTOMS_FEE_MINIMUM = 2000.00;
    private const CUSTOMS_FEE_PERCENTAGE = 0.20; // 20% of fuel cost
    private const INSURANCE_PER_HOUR = 150.00;
    private const BORDER_CROSSING_FEE = 500.00;

    public function calculate(float $distanceKm, float $durationHours): array
    {
        $baseFuelCost = $distanceKm * self::FUEL_COST_PER_KM;

        // Customs: whichever is higher - fixed minimum or percentage of fuel
        $customsFees = max(
            self::CUSTOMS_FEE_MINIMUM,
            $baseFuelCost * self::CUSTOMS_FEE_PERCENTAGE
        );

        $insurance = $durationHours * self::INSURANCE_PER_HOUR;
        $borderCrossing = self::BORDER_CROSSING_FEE;

        $totalCost = $baseFuelCost + $customsFees + $insurance + $borderCrossing;

        return [
            'total_cost' => round($totalCost, 2),
            'details' => [
                'base_fuel_cost' => round($baseFuelCost, 2),
                'custom_fees' => round($customsFees, 2),
                'insurance' => round($insurance, 2),
                'border_crossing' => round($borderCrossing, 2)
            ]
        ];
    }

}