<?php

namespace App\Services\Maintenance\Factories;

use App\Services\Maintenance\Types\EngineMaintenance;
use App\Services\Maintenance\Types\MaintenanceInterface;

class EngineMaintenanceFactory extends MaintenanceRequestFactory
{

    public function createMaintenanceRequest(): MaintenanceInterface
    {
        return new EngineMaintenance();
    }
}