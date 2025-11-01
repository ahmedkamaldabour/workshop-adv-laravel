<?php

namespace App\Services\Trip;


use Attribute;

interface TripCostStrategy
{
    public function calculate(float $distanceKm, float $durationHours): array;

}