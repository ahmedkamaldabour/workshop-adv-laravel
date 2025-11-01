<?php

namespace App\Services\Maintenance;

use App\Services\Helpers\FactorySelector;
use App\Services\Maintenance\Factories\ElectricalMaintenanceFactory;
use App\Services\Maintenance\Factories\EngineMaintenanceFactory;
use App\Services\Maintenance\Factories\MaintenanceRequestFactory;
use App\Services\Maintenance\Factories\TiresMaintenanceFactory;

class MaintenanceFactorySelector extends FactorySelector
{
    /**
     * The expected base class for factories returned by this selector.
     */
    public static function expectedBaseClass(): string
    {
        return MaintenanceRequestFactory::class;
    }

    /**
     * Register default maintenance factories.
     */
    public static function bootDefaults(): void
    {
        // Register default factories (class-strings). These will be resolved via app()->make when requested.
        static::registerFactory('engine', EngineMaintenanceFactory::class);
        static::registerFactory('tires', TiresMaintenanceFactory::class);
        static::registerFactory('electrical', ElectricalMaintenanceFactory::class);
    }
}