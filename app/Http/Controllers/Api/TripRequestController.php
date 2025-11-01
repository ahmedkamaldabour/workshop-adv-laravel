<?php

namespace App\Http\Controllers\Api;

use App\Services\Trip\TripCostCalculator;
use App\Services\Trip\TripStrategyDiscovery;
use Illuminate\Http\Request;
use function collect;

class TripRequestController
{

    public function __construct(
        public TripStrategyDiscovery $resolver
    ) {}


    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|in:international,intercity,local',
            'distance_km' => 'required|numeric|min:0',
            'duration_hours' => 'required|numeric|min:0'
        ]);

        $strategies = $this->resolver->getStrategies();
        $strategyClass = $strategies[$data['type']] ?? null;

        if (!$strategyClass) {
            return response()->json([
                'message' => 'No strategy found for the given trip type',
            ], 400);
        }

        $calculator = new TripCostCalculator(app($strategyClass));
        $costDetails = $calculator->calculateCost($data['distance_km'], $data['duration_hours']);


        return response()->json([
            'message' => 'Trip cost calculated successfully',
            'data' => $costDetails
        ], 200);
    }
}