<?php

namespace App\Services\Maintenance\Types;

class EngineMaintenance implements MaintenanceInterface
{
    public function handle(array $data): array
    {
        // Business Rule: Engine issues require Head Mechanic approval
        $requestId = 'MR-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return [
            'success' => true,
            'request_id' => $requestId,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'issue_type' => $data['issue_type'] ?? 'engine',
            'description' => $data['description'] ?? '',
            'status' => 'pending_approval',
            'assigned_to' => 'Head Mechanic',
            'priority' => $this->determinePriority($data['description'] ?? ''),
            'estimated_time' => '4-8 hours',
            'message' => 'Engine maintenance request created. Awaiting Head Mechanic approval.'
        ];
    }
    
    private function determinePriority(string $description): string
    {
        $description = strtolower($description);
        
        if (str_contains($description, 'critical') || str_contains($description, 'failure')) {
            return 'critical';
        }
        
        if (str_contains($description, 'noise') || str_contains($description, 'leak')) {
            return 'high';
        }
        
        if (str_contains($description, 'minor')) {
            return 'low';
        }
        
        return 'medium';
    }
}