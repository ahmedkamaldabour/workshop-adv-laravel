<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Tests for Trip Cost Calculation API
 *
 * These tests verify end-to-end functionality of the trip cost calculation system
 * including HTTP requests, validation, and cost calculations.
 */
class TripCostCalculationApiTest extends TestCase
{
    /**
     * Test successful local trip cost calculation
     *
     * @test
     */
    public function it_calculates_local_trip_cost_successfully()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 100,
            'duration_hours' => 2
        ];

        // Expected: (100 × 2.5) + (2 × 15) = 280

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_cost',
                    'details'
                ]
            ])
            ->assertJson([
                'data' => [
                    'total_cost' => 280.00
                ]
            ]);
    }

    /**
     * Test successful intercity trip cost calculation
     *
     * @test
     */
    public function it_calculates_intercity_trip_cost_successfully()
    {
        // Arrange
        $payload = [
            'type' => 'intercity',
            'distance_km' => 200,
            'duration_hours' => 4
        ];

        // Expected: (200 × 2.0) + (4 × 25) = 500

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Trip cost calculated successfully',
                'data' => [
                    'total_cost' => 500.00
                ]
            ]);
    }

    /**
     * Test successful international trip cost calculation
     *
     * @test
     */
    public function it_calculates_international_trip_cost_successfully()
    {
        // Arrange
        $payload = [
            'type' => 'international',
            'distance_km' => 800,
            'duration_hours' => 10
        ];

        // Expected: 960 (fuel) + 2000 (customs) + 1500 (insurance) + 500 (border) = 4960

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_cost',
                    'details' => [
                        'base_fuel_cost',
                        'custom_fees',
                        'insurance',
                        'border_crossing'
                    ]
                ]
            ])
            ->assertJson([
                'data' => [
                    'total_cost' => 4960.00
                ]
            ]);
    }

    /**
     * Test validation fails when type is missing
     *
     * @test
     */
    public function it_validates_type_is_required()
    {
        // Arrange
        $payload = [
            'distance_km' => 100,
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test validation fails when distance is missing
     *
     * @test
     */
    public function it_validates_distance_is_required()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['distance_km']);
    }

    /**
     * Test validation fails when duration is missing
     *
     * @test
     */
    public function it_validates_duration_is_required()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 100
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_hours']);
    }

    /**
     * Test validation fails for invalid trip type
     *
     * @test
     */
    public function it_validates_type_must_be_valid()
    {
        // Arrange
        $payload = [
            'type' => 'invalid_type',
            'distance_km' => 100,
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test validation fails for negative distance
     *
     * @test
     */
    public function it_validates_distance_must_be_positive()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => -100,
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['distance_km']);
    }

    /**
     * Test validation fails for negative duration
     *
     * @test
     */
    public function it_validates_duration_must_be_positive()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 100,
            'duration_hours' => -2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_hours']);
    }

    /**
     * Test distance can be zero
     *
     * @test
     */
    public function it_allows_zero_distance()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 0,
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_cost' => 30.00 // Only time cost: 2 × 15
                ]
            ]);
    }

    /**
     * Test duration can be zero
     *
     * @test
     */
    public function it_allows_zero_duration()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 100,
            'duration_hours' => 0
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_cost' => 250.00 // Only distance cost: 100 × 2.5
                ]
            ]);
    }

    /**
     * Test decimal values are handled correctly
     *
     * @test
     */
    public function it_handles_decimal_values()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 10.5,
            'duration_hours' => 1.5
        ];

        // Expected: (10.5 × 2.5) + (1.5 × 15) = 26.25 + 22.5 = 48.75

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_cost' => 48.75
                ]
            ]);
    }

    /**
     * Test that cost details are included in response
     *
     * @test
     */
    public function it_includes_cost_breakdown_in_response()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 100,
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_cost',
                    'details'
                ]
            ]);

        $details = $response->json('data.details');
        $this->assertIsArray($details);
        $this->assertNotEmpty($details);
    }

    /**
     * Test international trip includes all fee components
     *
     * @test
     */
    public function it_includes_all_international_trip_fees()
    {
        // Arrange
        $payload = [
            'type' => 'international',
            'distance_km' => 500,
            'duration_hours' => 8
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'details' => [
                        'base_fuel_cost',
                        'custom_fees',
                        'insurance',
                        'border_crossing'
                    ]
                ]
            ]);

        // Verify all components are numeric and positive
        $details = $response->json('data.details');
        foreach ($details as $key => $value) {
            $this->assertIsNumeric($value);
            $this->assertGreaterThanOrEqual(0, $value);
        }
    }

    /**
     * Test intercity trip includes proper breakdown
     *
     * @test
     */
    public function it_includes_intercity_trip_breakdown()
    {
        // Arrange
        $payload = [
            'type' => 'intercity',
            'distance_km' => 150,
            'duration_hours' => 3
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'details' => [
                        'fuel_cost',
                        'vehicle_consumption',
                        'driver_allowance'
                    ]
                ]
            ]);
    }

    /**
     * Test that different trip types return different costs
     *
     * @test
     */
    public function different_trip_types_return_different_costs()
    {
        // Arrange - same distance and duration for all
        $distance = 100;
        $duration = 2;

        // Act
        $localResponse = $this->postJson('/api/trip/request', [
            'type' => 'local',
            'distance_km' => $distance,
            'duration_hours' => $duration
        ]);

        $intercityResponse = $this->postJson('/api/trip/request', [
            'type' => 'intercity',
            'distance_km' => $distance,
            'duration_hours' => $duration
        ]);

        $internationalResponse = $this->postJson('/api/trip/request', [
            'type' => 'international',
            'distance_km' => $distance,
            'duration_hours' => $duration
        ]);

        // Assert - all should have different costs
        $localCost = $localResponse->json('data.total_cost');
        $intercityCost = $intercityResponse->json('data.total_cost');
        $internationalCost = $internationalResponse->json('data.total_cost');

        $this->assertNotEquals($localCost, $intercityCost);
        $this->assertNotEquals($localCost, $internationalCost);
        $this->assertNotEquals($intercityCost, $internationalCost);
    }

    /**
     * Test large distance values are handled
     *
     * @test
     */
    public function it_handles_large_distance_values()
    {
        // Arrange
        $payload = [
            'type' => 'international',
            'distance_km' => 9999.99,
            'duration_hours' => 50
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200);
        $totalCost = $response->json('data.total_cost');
        $this->assertIsNumeric($totalCost);
        $this->assertGreaterThan(0, $totalCost);
    }

    /**
     * Test API returns JSON content type
     *
     * @test
     */
    public function it_returns_json_content_type()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 100,
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertHeader('content-type', 'application/json');
    }

    /**
     * Test cost precision is maintained (2 decimal places)
     *
     * @test
     */
    public function it_maintains_cost_precision()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 33.333,
            'duration_hours' => 1.666
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(200);
        $totalCost = $response->json('data.total_cost');

        // Verify it's a properly formatted decimal (max 2 decimal places)
        $this->assertMatchesRegularExpression('/^\d+\.\d{1,2}$/', (string)$totalCost);
    }

    /**
     * Test validation for non-numeric distance
     *
     * @test
     */
    public function it_validates_distance_must_be_numeric()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 'not-a-number',
            'duration_hours' => 2
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['distance_km']);
    }

    /**
     * Test validation for non-numeric duration
     *
     * @test
     */
    public function it_validates_duration_must_be_numeric()
    {
        // Arrange
        $payload = [
            'type' => 'local',
            'distance_km' => 100,
            'duration_hours' => 'not-a-number'
        ];

        // Act
        $response = $this->postJson('/api/trip/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_hours']);
    }
}

