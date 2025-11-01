<?php

namespace App\Services\Maintenance\Factories;

use App\Services\Maintenance\Types\MaintenanceInterface;
use App\Services\Maintenance\Types\TiresMaintenance;

class TiresMaintenanceFactory extends MaintenanceRequestFactory
{

    public function createMaintenanceRequest(): MaintenanceInterface
    {
        return new TiresMaintenance();
    }
}