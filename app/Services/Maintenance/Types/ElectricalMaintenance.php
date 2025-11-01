<?php

namespace App\Services\Maintenance\Types;

class ElectricalMaintenance implements MaintenanceInterface
{

    public function handle(array $data): array
    {
        return [
            'status' => 'ok',
            'type' => 'electrical',
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'note' => 'Handled by electrical team',
        ];
    }
}