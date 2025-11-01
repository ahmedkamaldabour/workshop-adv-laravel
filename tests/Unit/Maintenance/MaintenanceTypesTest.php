<?php

namespace Tests\Unit\Maintenance;

use App\Services\Maintenance\Types\ElectricalMaintenance;
use App\Services\Maintenance\Types\EngineMaintenance;
use App\Services\Maintenance\Types\MaintenanceInterface;
use App\Services\Maintenance\Types\TiresMaintenance;
use PHPUnit\Framework\TestCase;

/**
 * TDD Tests for Maintenance Types
 *
 * These tests verify that each maintenance type correctly implements
 * its specific business logic and processing rules.
 */
class MaintenanceTypesTest extends TestCase
{
    /**
     * Test that EngineMaintenance handles request correctly
     *
     * Business Rule: Engine issues require Head Mechanic approval
     *
     * @test
     */
    public function engine_maintenance_requires_head_mechanic_approval()
    {
        // Arrange
        $maintenance = new EngineMaintenance();
        $data = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Engine overheating'
        ];

        // Act
        $result = $maintenance->handle($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('assigned_to', $result);
        $this->assertEquals('pending_approval', $result['status']);
        $this->assertStringContainsString('Head Mechanic', $result['assigned_to']);
    }

    /**
     * Test that TiresMaintenance routes to warehouse
     *
     * Business Rule: Tire issues are handled by warehouse inventory
     *
     * @test
     */
    public function tires_maintenance_routes_to_warehouse()
    {
        // Arrange
        $maintenance = new TiresMaintenance();
        $data = [
            'vehicle_id' => 42,
            'issue_type' => 'tires',
            'description' => 'Front tire puncture'
        ];

        // Act
        $result = $maintenance->handle($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('assigned_to', $result);
        $this->assertStringContainsString('Warehouse', $result['assigned_to']);
        $this->assertEquals('pending', $result['status']);
    }

    /**
     * Test that ElectricalMaintenance routes to external workshop
     *
     * Business Rule: Electrical issues are sent to external workshop
     *
     * @test
     */
    public function electrical_maintenance_routes_to_external_workshop()
    {
        // Arrange
        $maintenance = new ElectricalMaintenance();
        $data = [
            'vehicle_id' => 33,
            'issue_type' => 'electrical',
            'description' => 'Battery not charging'
        ];

        // Act
        $result = $maintenance->handle($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('assigned_to', $result);
        $this->assertStringContainsString('External', $result['assigned_to']);
        $this->assertArrayHasKey('estimated_time', $result);
    }

    /**
     * Test that all maintenance types implement the interface
     *
     * @test
     */
    public function all_maintenance_types_implement_interface()
    {
        // Arrange & Act
        $types = [
            new EngineMaintenance(),
            new TiresMaintenance(),
            new ElectricalMaintenance(),
        ];

        // Assert
        foreach ($types as $type) {
            $this->assertInstanceOf(MaintenanceInterface::class, $type);
        }
    }

    /**
     * Test that maintenance types return consistent structure
     *
     * @test
     */
    public function maintenance_types_return_consistent_structure()
    {
        // Arrange
        $types = [
            new EngineMaintenance(),
            new TiresMaintenance(),
            new ElectricalMaintenance(),
        ];

        $data = [
            'vehicle_id' => 1,
            'issue_type' => 'test',
            'description' => 'Test description'
        ];

        // Act & Assert
        foreach ($types as $type) {
            $result = $type->handle($data);

            // All should return these keys
            $this->assertArrayHasKey('success', $result);
            $this->assertArrayHasKey('vehicle_id', $result);
            $this->assertArrayHasKey('issue_type', $result);
            $this->assertArrayHasKey('status', $result);
            $this->assertArrayHasKey('message', $result);
        }
    }

    /**
     * Test that EngineMaintenance includes priority field
     *
     * @test
     */
    public function engine_maintenance_includes_priority()
    {
        // Arrange
        $maintenance = new EngineMaintenance();
        $data = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Critical engine failure'
        ];

        // Act
        $result = $maintenance->handle($data);

        // Assert
        $this->assertArrayHasKey('priority', $result);
        $this->assertContains($result['priority'], ['low', 'medium', 'high', 'critical']);
    }

    /**
     * Test that TiresMaintenance includes inventory check
     *
     * @test
     */
    public function tires_maintenance_includes_inventory_status()
    {
        // Arrange
        $maintenance = new TiresMaintenance();
        $data = [
            'vehicle_id' => 42,
            'issue_type' => 'tires',
            'description' => 'Need 4 new tires'
        ];

        // Act
        $result = $maintenance->handle($data);

        // Assert
        $this->assertArrayHasKey('inventory_checked', $result);
        $this->assertIsBool($result['inventory_checked']);
    }

    /**
     * Test that ElectricalMaintenance includes estimated time
     *
     * @test
     */
    public function electrical_maintenance_includes_estimated_time()
    {
        // Arrange
        $maintenance = new ElectricalMaintenance();
        $data = [
            'vehicle_id' => 33,
            'issue_type' => 'electrical',
            'description' => 'Complete electrical system failure'
        ];

        // Act
        $result = $maintenance->handle($data);

        // Assert
        $this->assertArrayHasKey('estimated_time', $result);
        $this->assertIsString($result['estimated_time']);
        $this->assertNotEmpty($result['estimated_time']);
    }

    /**
     * Test that maintenance handles empty description gracefully
     *
     * @test
     */
    public function maintenance_handles_empty_description()
    {
        // Arrange
        $maintenance = new EngineMaintenance();
        $data = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => ''
        ];

        // Act
        $result = $maintenance->handle($data);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test that each maintenance type generates unique request ID
     *
     * @test
     */
    public function maintenance_generates_unique_request_id()
    {
        // Arrange
        $maintenance = new EngineMaintenance();
        $data = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Test'
        ];

        // Act
        $result1 = $maintenance->handle($data);
        $result2 = $maintenance->handle($data);

        // Assert
        $this->assertArrayHasKey('request_id', $result1);
        $this->assertArrayHasKey('request_id', $result2);
        $this->assertNotEquals($result1['request_id'], $result2['request_id']);
    }
}

