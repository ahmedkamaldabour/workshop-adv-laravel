<?php

namespace App\Services\Trip;


class TripCostCalculator
{
    public function __construct(public TripCostStrategy $strategy)
    {
    }

    public function calculateCost(float $distanceKm, float $durationHours): array
    {
        if (!isset($this->strategy)) {
            throw new \RuntimeException('Strategy not set');
        }

        return $this->strategy->calculate($distanceKm, $durationHours);
    }
}