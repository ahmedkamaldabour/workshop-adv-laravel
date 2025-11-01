<?php

namespace App\Services\Maintenance\Types;

class TiresMaintenance implements MaintenanceInterface
{

    public function handle(array $data): array
    {
        return [
            'status' => 'ok',
            'type' => 'tires',
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'note' => 'Handled by tires warehouse',
        ];
    }
}