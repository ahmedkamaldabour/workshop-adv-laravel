<?php

namespace App\Services\Maintenance\Types;

class ElectricalMaintenance implements MaintenanceInterface
{
    public function handle(array $data): array
    {
        // Business Rule: Electrical issues are sent to external workshop
        $requestId = 'MR-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        return [
            'success' => true,
            'request_id' => $requestId,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'issue_type' => $data['issue_type'] ?? 'electrical',
            'description' => $data['description'] ?? '',
            'status' => 'pending',
            'assigned_to' => 'External Workshop - ElectroAuto',
            'estimated_time' => '2-4 hours',
            'message' => 'Electrical maintenance request created. Sent to external workshop.'
        ];
    }
}