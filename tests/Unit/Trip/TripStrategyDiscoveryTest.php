<?php

namespace Tests\Unit\Trip;

use App\Services\Trip\TripStrategyDiscovery;
use App\Services\Trip\LocalTripCostStrategy;
use App\Services\Trip\InterCityTripCostStrategy;
use App\Services\Trip\InternationalTripCostStrategy;
use PHPUnit\Framework\TestCase;

/**
 * TDD Tests for Trip Strategy Discovery
 *
 * Tests the automatic discovery of strategies using PHP 8 Attributes
 */
class TripStrategyDiscoveryTest extends TestCase
{
    /**
     * Test that discovery finds all registered strategies
     *
     * @test
     */
    public function it_discovers_all_registered_strategies()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();

        // Act
        $strategies = $discovery->getStrategies();

        // Assert
        $this->assertIsArray($strategies);
        $this->assertArrayHasKey('local', $strategies);
        $this->assertArrayHasKey('intercity', $strategies);
        $this->assertArrayHasKey('international', $strategies);
    }

    /**
     * Test that discovery maps types to correct strategy classes
     *
     * @test
     */
    public function it_maps_types_to_correct_strategy_classes()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();

        // Act
        $strategies = $discovery->getStrategies();

        // Assert
        $this->assertEquals(LocalTripCostStrategy::class, $strategies['local']);
        $this->assertEquals(InterCityTripCostStrategy::class, $strategies['intercity']);
        $this->assertEquals(InternationalTripCostStrategy::class, $strategies['international']);
    }

    /**
     * Test that discovery returns at least 3 strategies
     *
     * @test
     */
    public function it_returns_minimum_required_strategies()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();

        // Act
        $strategies = $discovery->getStrategies();

        // Assert
        $this->assertGreaterThanOrEqual(3, count($strategies));
    }

    /**
     * Test that all discovered strategies are valid classes
     *
     * @test
     */
    public function all_discovered_strategies_are_valid_classes()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();

        // Act
        $strategies = $discovery->getStrategies();

        // Assert
        foreach ($strategies as $type => $className) {
            $this->assertTrue(
                class_exists($className),
                "Strategy class {$className} for type {$type} does not exist"
            );
        }
    }

    /**
     * Test that discovered strategies implement TripCostStrategy interface
     *
     * @test
     */
    public function all_discovered_strategies_implement_interface()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();

        // Act
        $strategies = $discovery->getStrategies();

        // Assert
        foreach ($strategies as $type => $className) {
            $reflection = new \ReflectionClass($className);
            $this->assertTrue(
                $reflection->implementsInterface(\App\Services\Trip\TripCostStrategy::class),
                "Strategy {$className} does not implement TripCostStrategy interface"
            );
        }
    }

    /**
     * Test that discovery can get strategy by type
     *
     * @test
     */
    public function it_can_get_strategy_class_by_type()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();
        $strategies = $discovery->getStrategies();

        // Act & Assert
        $this->assertNotNull($strategies['local'] ?? null);
        $this->assertNotNull($strategies['intercity'] ?? null);
        $this->assertNotNull($strategies['international'] ?? null);
    }

    /**
     * Test that discovery returns empty or throws for unknown type
     *
     * @test
     */
    public function it_returns_null_for_unknown_strategy_type()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();
        $strategies = $discovery->getStrategies();

        // Act
        $unknownStrategy = $strategies['unknown_type'] ?? null;

        // Assert
        $this->assertNull($unknownStrategy);
    }

    /**
     * Test discovery is case-sensitive for strategy types
     *
     * @test
     */
    public function it_is_case_sensitive_for_strategy_types()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();
        $strategies = $discovery->getStrategies();

        // Act & Assert
        $this->assertArrayHasKey('local', $strategies);
        $this->assertArrayNotHasKey('LOCAL', $strategies);
        $this->assertArrayNotHasKey('Local', $strategies);
    }

    /**
     * Test that discovery can instantiate all strategies
     *
     * @test
     */
    public function it_can_instantiate_all_discovered_strategies()
    {
        // Arrange
        $discovery = new TripStrategyDiscovery();
        $strategies = $discovery->getStrategies();

        // Act & Assert
        foreach ($strategies as $type => $className) {
            $instance = app($className);
            $this->assertInstanceOf(
                \App\Services\Trip\TripCostStrategy::class,
                $instance,
                "Cannot instantiate strategy {$className}"
            );
        }
    }
}

