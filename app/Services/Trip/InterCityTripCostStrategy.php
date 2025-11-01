<?php

namespace App\Services\Trip;

use App\Attributes\TripStrategy;

#[TripStrategy('intercity')]
class InterCityTripCostStrategy implements TripCostStrategy
{
    // Business Rules: fuel cost + vehicle consumption + driver allowance
    private const FUEL_COST_PER_KM = 0.80;
    private const VEHICLE_CONSUMPTION_PER_KM = 1.20;
    private const DRIVER_ALLOWANCE_PER_HOUR = 25.00;

    public function calculate(float $distanceKm, float $durationHours): array
    {
        $fuelCost = $distanceKm * self::FUEL_COST_PER_KM;
        $vehicleConsumption = $distanceKm * self::VEHICLE_CONSUMPTION_PER_KM;
        $driverAllowance = $durationHours * self::DRIVER_ALLOWANCE_PER_HOUR;
        $totalCost = $fuelCost + $vehicleConsumption + $driverAllowance;

        return [
            'total_cost' => round($totalCost, 2),
            'details' => [
                'fuel_cost' => round($fuelCost, 2),
                'vehicle_consumption' => round($vehicleConsumption, 2),
                'driver_allowance' => round($driverAllowance, 2)
            ]
        ];
    }
}