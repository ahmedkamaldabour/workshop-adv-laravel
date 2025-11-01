<?php

namespace Tests\Unit\Maintenance;

use App\Services\Maintenance\Factories\ElectricalMaintenanceFactory;
use App\Services\Maintenance\Factories\EngineMaintenanceFactory;
use App\Services\Maintenance\Factories\MaintenanceRequestFactory;
use App\Services\Maintenance\Factories\TiresMaintenanceFactory;
use App\Services\Maintenance\Types\ElectricalMaintenance;
use App\Services\Maintenance\Types\EngineMaintenance;
use App\Services\Maintenance\Types\MaintenanceInterface;
use App\Services\Maintenance\Types\TiresMaintenance;
use PHPUnit\Framework\TestCase;

/**
 * Test-Driven Development for Maintenance Factory Pattern
 *
 * These tests verify the Factory Method pattern implementation
 * for creating different types of maintenance requests.
 */
class MaintenanceFactoryTest extends TestCase
{
    /**
     * Test that EngineMaintenanceFactory creates EngineMaintenance instance
     *
     * @test
     */
    public function it_creates_engine_maintenance_instance()
    {
        // Arrange
        $factory = new EngineMaintenanceFactory();

        // Act
        $maintenance = $factory->createMaintenanceRequest();

        // Assert
        $this->assertInstanceOf(MaintenanceInterface::class, $maintenance);
        $this->assertInstanceOf(EngineMaintenance::class, $maintenance);
    }

    /**
     * Test that TiresMaintenanceFactory creates TiresMaintenance instance
     *
     * @test
     */
    public function it_creates_tires_maintenance_instance()
    {
        // Arrange
        $factory = new TiresMaintenanceFactory();

        // Act
        $maintenance = $factory->createMaintenanceRequest();

        // Assert
        $this->assertInstanceOf(MaintenanceInterface::class, $maintenance);
        $this->assertInstanceOf(TiresMaintenance::class, $maintenance);
    }

    /**
     * Test that ElectricalMaintenanceFactory creates ElectricalMaintenance instance
     *
     * @test
     */
    public function it_creates_electrical_maintenance_instance()
    {
        // Arrange
        $factory = new ElectricalMaintenanceFactory();

        // Act
        $maintenance = $factory->createMaintenanceRequest();

        // Assert
        $this->assertInstanceOf(MaintenanceInterface::class, $maintenance);
        $this->assertInstanceOf(ElectricalMaintenance::class, $maintenance);
    }

    /**
     * Test that factory handleRequest method processes data correctly
     *
     * @test
     */
    public function it_handles_request_through_factory_method()
    {
        // Arrange
        $factory = new EngineMaintenanceFactory();
        $data = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Engine making strange noise'
        ];

        // Act
        $result = $factory->handleRequest($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    /**
     * Test that all concrete factories extend MaintenanceRequestFactory
     *
     * @test
     */
    public function it_ensures_all_factories_extend_base_factory()
    {
        // Arrange & Act
        $engineFactory = new EngineMaintenanceFactory();
        $tiresFactory = new TiresMaintenanceFactory();
        $electricalFactory = new ElectricalMaintenanceFactory();

        // Assert
        $this->assertInstanceOf(MaintenanceRequestFactory::class, $engineFactory);
        $this->assertInstanceOf(MaintenanceRequestFactory::class, $tiresFactory);
        $this->assertInstanceOf(MaintenanceRequestFactory::class, $electricalFactory);
    }

    /**
     * Test that factories produce objects implementing the correct interface
     *
     * @test
     */
    public function it_ensures_products_implement_maintenance_interface()
    {
        // Arrange
        $factories = [
            new EngineMaintenanceFactory(),
            new TiresMaintenanceFactory(),
            new ElectricalMaintenanceFactory(),
        ];

        // Act & Assert
        foreach ($factories as $factory) {
            $product = $factory->createMaintenanceRequest();
            $this->assertInstanceOf(MaintenanceInterface::class, $product);
            $this->assertTrue(method_exists($product, 'handle'));
        }
    }
}

