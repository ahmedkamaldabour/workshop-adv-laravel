<?php

namespace App\Services\Maintenance\Types;


class EngineMaintenance implements MaintenanceInterface
{
    public function handle(array $data): array
    {
        return [
            'status' => 'ok',
            'type' => 'engine',
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'note' => 'Handled by engine specialists',
        ];
    }
}