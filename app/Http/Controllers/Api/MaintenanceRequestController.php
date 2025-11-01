<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MaintenanceRequest;
use App\Services\Maintenance\MaintenanceFactorySelector;
use App\Services\Maintenance\Factories\MaintenanceRequestFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use function response;

class MaintenanceRequestController
{
    /**
     * Store a new maintenance request.
     *
     * @param MaintenanceRequest $request
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function store(MaintenanceRequest $request): JsonResponse
    {
        // Validation is automatically handled by MaintenanceRequest
        $data = $request->validated();

        /** @var MaintenanceRequestFactory $factory */
        $factory = MaintenanceFactorySelector::getFactory($data['issue_type']);
        $result = $factory->handleRequest($data);


        return response()->json($result, 201);
    }
}