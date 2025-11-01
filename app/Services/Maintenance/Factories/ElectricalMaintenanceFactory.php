<?php

namespace App\Services\Maintenance\Factories;

use App\Services\Maintenance\Types\ElectricalMaintenance;
use App\Services\Maintenance\Types\MaintenanceInterface;

class ElectricalMaintenanceFactory extends MaintenanceRequestFactory
{

    public function createMaintenanceRequest(): MaintenanceInterface
    {
        return new ElectricalMaintenance();
    }
}