<?php

namespace Tests\Unit\Maintenance;

use App\Services\Maintenance\Factories\ElectricalMaintenanceFactory;
use App\Services\Maintenance\Factories\EngineMaintenanceFactory;
use App\Services\Maintenance\Factories\MaintenanceRequestFactory;
use App\Services\Maintenance\Factories\TiresMaintenanceFactory;
use App\Services\Maintenance\MaintenanceFactorySelector;
use PHPUnit\Framework\TestCase;

/**
 * TDD Tests for Maintenance Factory Selector
 *
 * Tests the registry pattern that maps issue types to their factories
 */
class MaintenanceFactorySelectorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Boot the defaults before each test
        MaintenanceFactorySelector::bootDefaults();
    }

    /**
     * Test that factory selector returns correct factory for engine
     *
     * @test
     */
    public function it_returns_engine_factory_for_engine_type()
    {
        // Act
        $factory = MaintenanceFactorySelector::getFactory('engine');

        // Assert
        $this->assertInstanceOf(EngineMaintenanceFactory::class, $factory);
        $this->assertInstanceOf(MaintenanceRequestFactory::class, $factory);
    }

    /**
     * Test that factory selector returns correct factory for tires
     *
     * @test
     */
    public function it_returns_tires_factory_for_tires_type()
    {
        // Act
        $factory = MaintenanceFactorySelector::getFactory('tires');

        // Assert
        $this->assertInstanceOf(TiresMaintenanceFactory::class, $factory);
        $this->assertInstanceOf(MaintenanceRequestFactory::class, $factory);
    }

    /**
     * Test that factory selector returns correct factory for electrical
     *
     * @test
     */
    public function it_returns_electrical_factory_for_electrical_type()
    {
        // Act
        $factory = MaintenanceFactorySelector::getFactory('electrical');

        // Assert
        $this->assertInstanceOf(ElectricalMaintenanceFactory::class, $factory);
        $this->assertInstanceOf(MaintenanceRequestFactory::class, $factory);
    }

    /**
     * Test that factory selector throws exception for unknown type
     *
     * @test
     */
    public function it_throws_exception_for_unknown_type()
    {
        // Expect
        $this->expectException(\InvalidArgumentException::class);

        // Act
        MaintenanceFactorySelector::getFactory('unknown');
    }

    /**
     * Test that factory selector returns all available types
     *
     * @test
     */
    public function it_returns_all_available_maintenance_types()
    {
        // Act
        $types = MaintenanceFactorySelector::getAvailableTypes();

        // Assert
        $this->assertIsArray($types);
        $this->assertContains('engine', $types);
        $this->assertContains('tires', $types);
        $this->assertContains('electrical', $types);
        $this->assertCount(3, $types);
    }

    /**
     * Test that factory selector allows registering custom factories
     *
     * @test
     */
    public function it_allows_registering_custom_factories()
    {
        // Arrange
        $customFactory = new class extends MaintenanceRequestFactory {
            public function createMaintenanceRequest(): \App\Services\Maintenance\Types\MaintenanceInterface
            {
                return new \App\Services\Maintenance\Types\EngineMaintenance();
            }
        };

        // Act
        MaintenanceFactorySelector::registerFactory('custom', get_class($customFactory));
        $types = MaintenanceFactorySelector::getAvailableTypes();

        // Assert
        $this->assertContains('custom', $types);
    }

    /**
     * Test that bootDefaults registers all default factories
     *
     * @test
     */
    public function boot_defaults_registers_all_standard_factories()
    {
        // Act
        MaintenanceFactorySelector::bootDefaults();
        $types = MaintenanceFactorySelector::getAvailableTypes();

        // Assert
        $this->assertGreaterThanOrEqual(3, count($types));
        $this->assertContains('engine', $types);
        $this->assertContains('tires', $types);
        $this->assertContains('electrical', $types);
    }

    /**
     * Test that factory selector validates factory class
     *
     * @test
     */
    public function it_validates_registered_factory_extends_base_class()
    {
        // Arrange - try to register an invalid class
        $invalidClass = \stdClass::class;

        // Expect
        $this->expectException(\InvalidArgumentException::class);

        // Act
        MaintenanceFactorySelector::registerFactory('invalid', $invalidClass);
    }
}

