<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TripCostRequest;
use App\Services\Trip\TripCostCalculator;
use App\Services\Trip\TripStrategyDiscovery;
use Illuminate\Http\JsonResponse;

class TripRequestController
{
    public function __construct(
        public TripStrategyDiscovery $resolver
    ) {}

    /**
     * Calculate trip cost based on type, distance, and duration.
     *
     * @param TripCostRequest $request
     * @return JsonResponse
     */
    public function store(TripCostRequest $request): JsonResponse
    {
        // Validation is automatically handled by TripCostRequest
        $data = $request->validated();

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