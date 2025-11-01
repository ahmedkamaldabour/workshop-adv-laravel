<?php

namespace Tests\Unit\Trip;

use App\Services\Trip\LocalTripCostStrategy;
use App\Services\Trip\InterCityTripCostStrategy;
use App\Services\Trip\InternationalTripCostStrategy;
use App\Services\Trip\TripCostStrategy;
use PHPUnit\Framework\TestCase;

/**
 * TDD Tests for Trip Cost Strategies
 *
 * These tests verify that each strategy correctly calculates costs
 * according to business rules defined in the PRD.
 */
class TripCostStrategyTest extends TestCase
{
    /**
     * Test Local Trip Cost Calculation
     *
     * Business Rule: (distance × $2.50) + (duration × $15.00)
     *
     * @test
     */
    public function local_trip_calculates_cost_correctly()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();
        $distance = 100; // km
        $duration = 2;   // hours

        // Expected: (100 × 2.5) + (2 × 15) = 250 + 30 = 280
        $expectedTotal = 280.00;

        // Act
        $result = $strategy->calculate($distance, $duration);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_cost', $result);
        $this->assertArrayHasKey('details', $result);
        $this->assertEquals($expectedTotal, $result['total_cost']);
        $this->assertArrayHasKey('distance_cost', $result['details']);
        $this->assertArrayHasKey('time_cost', $result['details']);
        $this->assertEquals(250.00, $result['details']['distance_cost']);
        $this->assertEquals(30.00, $result['details']['time_cost']);
    }

    /**
     * Test InterCity Trip Cost Calculation
     *
     * Business Rule: (distance × $2.00) + (duration × $25.00)
     * Breakdown: fuel ($0.80/km) + consumption ($1.20/km) + driver ($25/hr)
     *
     * @test
     */
    public function intercity_trip_calculates_cost_correctly()
    {
        // Arrange
        $strategy = new InterCityTripCostStrategy();
        $distance = 200; // km
        $duration = 4;   // hours

        // Expected: (200 × 2.0) + (4 × 25) = 400 + 100 = 500
        $expectedTotal = 500.00;

        // Act
        $result = $strategy->calculate($distance, $duration);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_cost', $result);
        $this->assertArrayHasKey('details', $result);
        $this->assertEquals($expectedTotal, $result['total_cost']);

        // Check detailed breakdown
        $this->assertArrayHasKey('fuel_cost', $result['details']);
        $this->assertArrayHasKey('vehicle_consumption', $result['details']);
        $this->assertArrayHasKey('driver_allowance', $result['details']);
    }

    /**
     * Test International Trip Cost Calculation
     *
     * Business Rule: fuel + max(customs, 20% fuel) + insurance + border
     *
     * @test
     */
    public function international_trip_calculates_cost_correctly()
    {
        // Arrange
        $strategy = new InternationalTripCostStrategy();
        $distance = 800; // km
        $duration = 10;  // hours

        // Expected calculation:
        // fuel = 800 × 1.2 = 960
        // customs = max(2000, 960 × 0.2) = max(2000, 192) = 2000
        // insurance = 10 × 150 = 1500
        // border = 500
        // total = 960 + 2000 + 1500 + 500 = 4960

        // Act
        $result = $strategy->calculate($distance, $duration);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_cost', $result);
        $this->assertArrayHasKey('details', $result);

        // Check all components exist
        $details = $result['details'];
        $this->assertArrayHasKey('base_fuel_cost', $details);
        $this->assertArrayHasKey('custom_fees', $details);
        $this->assertArrayHasKey('insurance', $details);
        $this->assertArrayHasKey('border_crossing', $details);

        // Verify calculations
        $this->assertEquals(960.00, $details['base_fuel_cost']);
        $this->assertEquals(2000.00, $details['custom_fees']); // Fixed minimum
        $this->assertEquals(1500.00, $details['insurance']);
        $this->assertEquals(500.00, $details['border_crossing']);
        $this->assertEquals(4960.00, $result['total_cost']);
    }

    /**
     * Test all strategies implement TripCostStrategy interface
     *
     * @test
     */
    public function all_strategies_implement_interface()
    {
        // Arrange & Act
        $strategies = [
            new LocalTripCostStrategy(),
            new InterCityTripCostStrategy(),
            new InternationalTripCostStrategy(),
        ];

        // Assert
        foreach ($strategies as $strategy) {
            $this->assertInstanceOf(TripCostStrategy::class, $strategy);
            $this->assertTrue(method_exists($strategy, 'calculate'));
        }
    }

    /**
     * Test strategies return consistent structure
     *
     * @test
     */
    public function strategies_return_consistent_structure()
    {
        // Arrange
        $strategies = [
            new LocalTripCostStrategy(),
            new InterCityTripCostStrategy(),
            new InternationalTripCostStrategy(),
        ];

        // Act & Assert
        foreach ($strategies as $strategy) {
            $result = $strategy->calculate(100, 2);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('total_cost', $result);
            $this->assertArrayHasKey('details', $result);
            $this->assertIsNumeric($result['total_cost']);
            $this->assertIsArray($result['details']);
        }
    }

    /**
     * Test local trip with zero distance
     *
     * @test
     */
    public function local_trip_handles_zero_distance()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();

        // Act
        $result = $strategy->calculate(0, 2);

        // Assert
        $this->assertEquals(30.00, $result['total_cost']); // Only time cost
        $this->assertEquals(0, $result['details']['distance_cost']);
        $this->assertEquals(30.00, $result['details']['time_cost']);
    }

    /**
     * Test local trip with zero duration
     *
     * @test
     */
    public function local_trip_handles_zero_duration()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();

        // Act
        $result = $strategy->calculate(100, 0);

        // Assert
        $this->assertEquals(250.00, $result['total_cost']); // Only distance cost
        $this->assertEquals(250.00, $result['details']['distance_cost']);
        $this->assertEquals(0, $result['details']['time_cost']);
    }

    /**
     * Test international trip customs calculation with percentage higher than minimum
     *
     * @test
     */
    public function international_trip_uses_percentage_when_higher_than_minimum()
    {
        // Arrange
        $strategy = new InternationalTripCostStrategy();
        $distance = 20000; // Very long distance
        $duration = 5;

        // fuel = 20000 × 1.2 = 24000
        // customs = max(2000, 24000 × 0.2) = max(2000, 4800) = 4800

        // Act
        $result = $strategy->calculate($distance, $duration);

        // Assert
        $this->assertEquals(24000.00, $result['details']['base_fuel_cost']);
        $this->assertEquals(4800.00, $result['details']['custom_fees']); // Percentage wins
    }

    /**
     * Test cost precision is rounded to 2 decimal places
     *
     * @test
     */
    public function strategies_round_to_two_decimal_places()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();
        $distance = 33.333; // Will create decimal precision issues
        $duration = 1.666;

        // Act
        $result = $strategy->calculate($distance, $duration);

        // Assert
        $this->assertMatchesRegularExpression('/^\d+\.\d{1,2}$/', (string)$result['total_cost']);
        $this->assertEquals(
            round($result['total_cost'], 2),
            $result['total_cost']
        );
    }

    /**
     * Test intercity trip breakdown components
     *
     * @test
     */
    public function intercity_trip_breaks_down_costs_correctly()
    {
        // Arrange
        $strategy = new InterCityTripCostStrategy();
        $distance = 150;
        $duration = 3;

        // Expected:
        // fuel = 150 × 0.8 = 120
        // consumption = 150 × 1.2 = 180
        // driver = 3 × 25 = 75
        // total = 375

        // Act
        $result = $strategy->calculate($distance, $duration);

        // Assert
        $this->assertEquals(120.00, $result['details']['fuel_cost']);
        $this->assertEquals(180.00, $result['details']['vehicle_consumption']);
        $this->assertEquals(75.00, $result['details']['driver_allowance']);
        $this->assertEquals(375.00, $result['total_cost']);
    }

    /**
     * Test that strategies handle large numbers correctly
     *
     * @test
     */
    public function strategies_handle_large_numbers()
    {
        // Arrange
        $strategies = [
            new LocalTripCostStrategy(),
            new InterCityTripCostStrategy(),
            new InternationalTripCostStrategy(),
        ];

        $largeDistance = 9999.99;
        $largeDuration = 72.00;

        // Act & Assert
        foreach ($strategies as $strategy) {
            $result = $strategy->calculate($largeDistance, $largeDuration);

            $this->assertIsNumeric($result['total_cost']);
            $this->assertGreaterThan(0, $result['total_cost']);
            $this->assertTrue(is_finite($result['total_cost']));
        }
    }

    /**
     * Test international trip includes all required fee components
     *
     * @test
     */
    public function international_trip_includes_all_fee_components()
    {
        // Arrange
        $strategy = new InternationalTripCostStrategy();

        // Act
        $result = $strategy->calculate(500, 8);

        // Assert - must have all 4 components
        $requiredKeys = [
            'base_fuel_cost',
            'custom_fees',
            'insurance',
            'border_crossing'
        ];

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $result['details']);
            $this->assertIsNumeric($result['details'][$key]);
            $this->assertGreaterThanOrEqual(0, $result['details'][$key]);
        }
    }
}

