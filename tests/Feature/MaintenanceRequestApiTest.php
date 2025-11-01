<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature Tests for Maintenance Request API
 *
 * These tests verify end-to-end functionality of the maintenance request system
 * including HTTP requests, validation, and responses.
 */
class MaintenanceRequestApiTest extends TestCase
{
    /**
     * Test successful engine maintenance request
     *
     * @test
     */
    public function it_creates_engine_maintenance_request_successfully()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Engine making strange noise'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'vehicle_id',
                'issue_type',
                'status',
                'message'
            ])
            ->assertJson([
                'success' => true,
                'vehicle_id' => 55,
                'issue_type' => 'engine'
            ]);
    }

    /**
     * Test successful tires maintenance request
     *
     * @test
     */
    public function it_creates_tires_maintenance_request_successfully()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 42,
            'issue_type' => 'tires',
            'description' => 'Front tire puncture'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'vehicle_id' => 42,
                'issue_type' => 'tires'
            ]);
    }

    /**
     * Test successful electrical maintenance request
     *
     * @test
     */
    public function it_creates_electrical_maintenance_request_successfully()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 33,
            'issue_type' => 'electrical',
            'description' => 'Battery not charging'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'vehicle_id' => 33,
                'issue_type' => 'electrical'
            ])
            ->assertJsonPath('assigned_to', function ($value) {
                return str_contains($value, 'External');
            });
    }

    /**
     * Test validation fails when vehicle_id is missing
     *
     * @test
     */
    public function it_validates_vehicle_id_is_required()
    {
        // Arrange
        $payload = [
            'issue_type' => 'engine',
            'description' => 'Test'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    /**
     * Test validation fails when issue_type is missing
     *
     * @test
     */
    public function it_validates_issue_type_is_required()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'description' => 'Test'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['issue_type']);
    }

    /**
     * Test validation fails for invalid issue_type
     *
     * @test
     */
    public function it_validates_issue_type_must_be_valid()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'invalid_type',
            'description' => 'Test'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['issue_type']);
    }

    /**
     * Test validation fails when vehicle_id is not integer
     *
     * @test
     */
    public function it_validates_vehicle_id_must_be_integer()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 'not-a-number',
            'issue_type' => 'engine',
            'description' => 'Test'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    /**
     * Test description is optional
     *
     * @test
     */
    public function it_allows_empty_description()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'engine'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true
            ]);
    }

    /**
     * Test that each issue type returns appropriate status
     *
     * @test
     */
    public function it_returns_different_status_for_different_issue_types()
    {
        // Engine requires approval
        $engineResponse = $this->postJson('/api/maintenance/request', [
            'vehicle_id' => 1,
            'issue_type' => 'engine',
            'description' => 'Test'
        ]);

        // Tires is pending
        $tiresResponse = $this->postJson('/api/maintenance/request', [
            'vehicle_id' => 2,
            'issue_type' => 'tires',
            'description' => 'Test'
        ]);

        // Assert different statuses
        $engineResponse->assertStatus(201);
        $tiresResponse->assertStatus(201);

        // They should have different status values
        $this->assertNotEquals(
            $engineResponse->json('status'),
            $tiresResponse->json('status')
        );
    }

    /**
     * Test API returns request_id for tracking
     *
     * @test
     */
    public function it_returns_unique_request_id()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Test'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure(['request_id']);

        $requestId = $response->json('request_id');
        $this->assertNotEmpty($requestId);
    }

    /**
     * Test API returns estimated time
     *
     * @test
     */
    public function it_returns_estimated_time_for_completion()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'electrical',
            'description' => 'Test'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure(['estimated_time']);
    }

    /**
     * Test multiple requests generate unique IDs
     *
     * @test
     */
    public function it_generates_unique_request_ids_for_multiple_requests()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Test'
        ];

        // Act
        $response1 = $this->postJson('/api/maintenance/request', $payload);
        $response2 = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $this->assertNotEquals(
            $response1->json('request_id'),
            $response2->json('request_id')
        );
    }

    /**
     * Test API handles large description text
     *
     * @test
     */
    public function it_handles_large_description_text()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => str_repeat('This is a long description. ', 50)
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertStatus(201);
    }

    /**
     * Test API content type is JSON
     *
     * @test
     */
    public function it_returns_json_content_type()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Test'
        ];

        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);

        // Assert
        $response->assertHeader('content-type', 'application/json');
    }
}

