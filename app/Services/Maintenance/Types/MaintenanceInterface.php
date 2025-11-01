<?php

namespace App\Services\Maintenance\Types;

interface MaintenanceInterface
{
    public function handle(array $data): array;

}