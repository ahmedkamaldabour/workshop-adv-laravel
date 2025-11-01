<?php

namespace App\Services\Maintenance\Factories;


use App\Services\Maintenance\Types\MaintenanceInterface;

abstract class MaintenanceRequestFactory
{
    // Factory Method - abstract
    abstract public function createMaintenanceRequest(): MaintenanceInterface ;

    // Template method that uses the factory method
    final public function handleRequest(array $data): array
    {
        return $this->createMaintenanceRequest()->handle($data);
    }
}