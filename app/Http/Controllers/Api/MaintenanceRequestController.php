<?php

namespace App\Http\Controllers\Api;

use App\Services\Maintenance\MaintenanceFactorySelector;
use App\Services\Maintenance\Factories\MaintenanceRequestFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function response;

class MaintenanceRequestController
{
    /**
     * @throws BindingResolutionException
     */
    public function store(Request $request): jsonResponse
    {
        $data = $request->validate([
            'vehicle_id' => 'required|integer',
            'issue_type' => 'required|string|in:engine,tires,electrical',
            'description' => 'nullable|string',
        ]);

        /** @var MaintenanceRequestFactory $factory */
        $factory = MaintenanceFactorySelector::getFactory($data['issue_type']);
        $result = $factory->handleRequest($data);


        return response()->json($result, 201);
    }
}