<?php

namespace Tests\Unit\Trip;

use App\Services\Trip\TripCostCalculator;
use App\Services\Trip\TripCostStrategy;
use App\Services\Trip\LocalTripCostStrategy;
use App\Services\Trip\InterCityTripCostStrategy;
use App\Services\Trip\InternationalTripCostStrategy;
use PHPUnit\Framework\TestCase;

/**
 * TDD Tests for Trip Cost Calculator (Strategy Context)
 *
 * Tests the context class that uses strategies to calculate costs
 */
class TripCostCalculatorTest extends TestCase
{
    /**
     * Test that calculator can be instantiated with a strategy
     *
     * @test
     */
    public function it_can_be_instantiated_with_strategy()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();

        // Act
        $calculator = new TripCostCalculator($strategy);

        // Assert
        $this->assertInstanceOf(TripCostCalculator::class, $calculator);
        $this->assertInstanceOf(TripCostStrategy::class, $calculator->strategy);
    }

    /**
     * Test that calculator delegates calculation to strategy
     *
     * @test
     */
    public function it_delegates_calculation_to_strategy()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();
        $calculator = new TripCostCalculator($strategy);

        // Act
        $result = $calculator->calculateCost(100, 2);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_cost', $result);
        $this->assertEquals(280.00, $result['total_cost']);
    }

    /**
     * Test that calculator works with different strategies
     *
     * @test
     */
    public function it_works_with_different_strategies()
    {
        // Arrange - Test with all three strategies
        $strategies = [
            'local' => new LocalTripCostStrategy(),
            'intercity' => new InterCityTripCostStrategy(),
            'international' => new InternationalTripCostStrategy(),
        ];

        $distance = 100;
        $duration = 2;

        // Act & Assert
        foreach ($strategies as $type => $strategy) {
            $calculator = new TripCostCalculator($strategy);
            $result = $calculator->calculateCost($distance, $duration);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('total_cost', $result);
            $this->assertGreaterThan(0, $result['total_cost']);
        }
    }

    /**
     * Test that calculator throws exception without strategy
     *
     * @test
     */
    public function it_throws_exception_when_strategy_not_set()
    {
        // This test verifies the contract - strategy must be provided
        // Since PHP 8.2+, the constructor requires the strategy parameter

        // We can test by creating a mock scenario where strategy is null
        $this->expectException(\TypeError::class);

        // Act - try to create without strategy (will fail at compile time)
        new TripCostCalculator(null);
    }

    /**
     * Test calculator returns same result for same inputs
     *
     * @test
     */
    public function it_returns_consistent_results()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();
        $calculator = new TripCostCalculator($strategy);

        // Act
        $result1 = $calculator->calculateCost(150, 3);
        $result2 = $calculator->calculateCost(150, 3);

        // Assert
        $this->assertEquals($result1['total_cost'], $result2['total_cost']);
        $this->assertEquals($result1, $result2);
    }

    /**
     * Test that calculator can switch strategies at runtime
     *
     * @test
     */
    public function it_can_switch_strategies_at_runtime()
    {
        // Arrange
        $localStrategy = new LocalTripCostStrategy();
        $internationalStrategy = new InternationalTripCostStrategy();

        // Act - Calculate with local strategy
        $calculator = new TripCostCalculator($localStrategy);
        $localResult = $calculator->calculateCost(100, 2);

        // Switch to international strategy
        $calculator = new TripCostCalculator($internationalStrategy);
        $internationalResult = $calculator->calculateCost(100, 2);

        // Assert - Results should be different
        $this->assertNotEquals(
            $localResult['total_cost'],
            $internationalResult['total_cost']
        );
    }

    /**
     * Test calculator with mock strategy
     *
     * @test
     */
    public function it_works_with_mock_strategy()
    {
        // Arrange - Create a mock strategy
        $mockStrategy = new class implements TripCostStrategy {
            public function calculate(float $distanceKm, float $durationHours): array
            {
                return [
                    'total_cost' => 999.99,
                    'details' => ['mock' => true]
                ];
            }
        };

        $calculator = new TripCostCalculator($mockStrategy);

        // Act
        $result = $calculator->calculateCost(100, 2);

        // Assert
        $this->assertEquals(999.99, $result['total_cost']);
        $this->assertTrue($result['details']['mock']);
    }

    /**
     * Test calculator handles zero values
     *
     * @test
     */
    public function it_handles_zero_values()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();
        $calculator = new TripCostCalculator($strategy);

        // Act
        $result = $calculator->calculateCost(0, 0);

        // Assert
        $this->assertEquals(0, $result['total_cost']);
    }

    /**
     * Test calculator handles decimal precision
     *
     * @test
     */
    public function it_handles_decimal_precision()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();
        $calculator = new TripCostCalculator($strategy);

        // Act
        $result = $calculator->calculateCost(10.5, 1.5);

        // Assert
        $this->assertIsFloat($result['total_cost']);
        // 10.5 × 2.5 + 1.5 × 15 = 26.25 + 22.5 = 48.75
        $this->assertEquals(48.75, $result['total_cost']);
    }

    /**
     * Test calculator preserves strategy details
     *
     * @test
     */
    public function it_preserves_strategy_details()
    {
        // Arrange
        $strategy = new InternationalTripCostStrategy();
        $calculator = new TripCostCalculator($strategy);

        // Act
        $result = $calculator->calculateCost(800, 10);

        // Assert
        $this->assertArrayHasKey('details', $result);
        $this->assertIsArray($result['details']);
        $this->assertNotEmpty($result['details']);
    }
}

