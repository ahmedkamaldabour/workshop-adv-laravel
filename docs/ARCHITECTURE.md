# FleetTrack - System Architecture Documentation

**Version:** 1.0  
**Last Updated:** November 1, 2025

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Architecture Patterns](#2-architecture-patterns)
3. [Feature 1: Maintenance Request System](#3-feature-1-maintenance-request-system)
4. [Feature 2: Trip Cost Calculation System](#4-feature-2-trip-cost-calculation-system)
5. [Database Design](#5-database-design)
6. [API Design](#6-api-design)
7. [Security Architecture](#7-security-architecture)
8. [Testing Strategy](#8-testing-strategy)

---

## 1. System Overview

### 1.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     FleetTrack System                        │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐         ┌──────────────┐                  │
│  │   Frontend   │────────▶│  API Layer   │                  │
│  │  (Future)    │  HTTP   │  (Laravel)   │                  │
│  └──────────────┘         └──────┬───────┘                  │
│                                   │                          │
│                          ┌────────▼────────┐                 │
│                          │   Controllers   │                 │
│                          └────────┬────────┘                 │
│                                   │                          │
│            ┌──────────────────────┼──────────────────────┐   │
│            │                      │                      │   │
│   ┌────────▼────────┐    ┌───────▼────────┐   ┌────────▼───┐
│   │  Maintenance    │    │  Trip Cost     │   │   Other    │
│   │  Service Layer  │    │  Service Layer │   │  Services  │
│   └────────┬────────┘    └───────┬────────┘   └────────────┘
│            │                     │                           │
│   ┌────────▼────────┐    ┌───────▼────────┐                │
│   │ Factory Pattern │    │Strategy Pattern│                 │
│   └────────┬────────┘    └───────┬────────┘                │
│            │                     │                           │
│   ┌────────▼─────────────────────▼────────┐                │
│   │          Database Layer                │                 │
│   │      (SQLite/MySQL + Models)           │                 │
│   └────────────────────────────────────────┘                │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Technology Stack

- **Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** SQLite (Dev), MySQL (Prod)
- **Testing:** PHPUnit 10.x
- **Design Patterns:** Factory Method, Strategy, Service Layer

---

## 2. Architecture Patterns

### 2.1 Design Patterns Used

#### Factory Method Pattern (Maintenance System)
**Purpose:** Create different maintenance handlers without coupling the code to specific classes.

**Benefits:**
- Single Responsibility Principle
- Open/Closed Principle
- Easy to add new maintenance types
- Testability

#### Strategy Pattern (Trip Cost Calculation)
**Purpose:** Define a family of algorithms (cost calculations) and make them interchangeable.

**Benefits:**
- Runtime algorithm selection
- Clean separation of concerns
- Easy to add new trip types
- Unit testing each strategy independently

### 2.2 Architectural Principles

1. **SOLID Principles**
   - Single Responsibility: Each class has one reason to change
   - Open/Closed: Open for extension, closed for modification
   - Liskov Substitution: Subtypes are substitutable
   - Interface Segregation: Specific interfaces
   - Dependency Inversion: Depend on abstractions

2. **Clean Architecture**
   - Controllers (thin, routing only)
   - Services (business logic)
   - Models (data layer)
   - Clear separation of concerns

---

## 3. Feature 1: Maintenance Request System

### 3.1 Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Maintenance Request Flow                   │
└─────────────────────────────────────────────────────────────┘

Client Request
     │
     ▼
┌─────────────────────────┐
│ MaintenanceRequest      │  (HTTP Layer)
│ Controller              │  - Validates input
└────────┬────────────────┘  - Returns JSON
         │
         ▼
┌─────────────────────────┐
│ MaintenanceFactory      │  (Factory Selector)
│ Selector                │  - Maps issue_type → Factory
└────────┬────────────────┘  - Returns appropriate factory
         │
         ├─────────────┬─────────────┬─────────────┐
         ▼             ▼             ▼             ▼
┌─────────────┐ ┌──────────────┐ ┌────────────────┐
│   Engine    │ │    Tires     │ │  Electrical    │ (Concrete Factories)
│   Factory   │ │   Factory    │ │    Factory     │
└──────┬──────┘ └──────┬───────┘ └────────┬───────┘
       │               │                   │
       ▼               ▼                   ▼
┌─────────────┐ ┌──────────────┐ ┌────────────────┐
│   Engine    │ │    Tires     │ │  Electrical    │ (Maintenance Types)
│ Maintenance │ │ Maintenance  │ │  Maintenance   │
└──────┬──────┘ └──────┬───────┘ └────────┬───────┘
       │               │                   │
       └───────────────┴───────────────────┘
                       │
                       ▼
              ┌────────────────┐
              │ handle($data)  │  (Common Interface)
              └────────┬───────┘
                       │
       ┌───────────────┼───────────────┐
       ▼               ▼               ▼
┌─────────────┐ ┌──────────────┐ ┌────────────────┐
│ Log Request │ │   Notify     │ │  External API  │
│ To Database │ │   Parties    │ │  Integration   │
└─────────────┘ └──────────────┘ └────────────────┘
```

### 3.2 Class Structure

```php
// Abstract Factory
abstract class MaintenanceRequestFactory
{
    abstract public function createMaintenanceRequest(): MaintenanceInterface;
    final public function handleRequest(array $data): array;
}

// Concrete Factories
class EngineMaintenanceFactory extends MaintenanceRequestFactory
class TiresMaintenanceFactory extends MaintenanceRequestFactory
class ElectricalMaintenanceFactory extends MaintenanceRequestFactory

// Product Interface
interface MaintenanceInterface
{
    public function handle(array $data): array;
}

// Concrete Products
class EngineMaintenance implements MaintenanceInterface
class TiresMaintenance implements MaintenanceInterface
class ElectricalMaintenance implements MaintenanceInterface

// Factory Selector (Registry)
class MaintenanceFactorySelector extends FactorySelector
{
    // Maps: 'engine' => EngineMaintenanceFactory::class
    // Resolves via Laravel's container
}
```

### 3.3 Data Flow

1. **Request Arrives:** POST /api/maintenance/request
2. **Validation:** Controller validates input
3. **Factory Selection:** Selector chooses factory based on issue_type
4. **Factory Creates Product:** Factory instantiates appropriate maintenance handler
5. **Handler Processes:** Maintenance type executes specific logic
6. **Response:** JSON response returned to client

### 3.4 Extensibility

Adding a new maintenance type (e.g., "brakes"):

1. Create `BrakesMaintenance.php` implementing `MaintenanceInterface`
2. Create `BrakesMaintenanceFactory.php` extending `MaintenanceRequestFactory`
3. Register in `MaintenanceFactorySelector::bootDefaults()`
4. Add validation rule in controller
5. Write tests

**No existing code needs modification** ✅

---

## 4. Feature 2: Trip Cost Calculation System

### 4.1 Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                  Trip Cost Calculation Flow                   │
└─────────────────────────────────────────────────────────────┘

Client Request
     │
     ▼
┌─────────────────────────┐
│ TripRequest             │  (HTTP Layer)
│ Controller              │  - Validates input
└────────┬────────────────┘  - Returns JSON
         │
         ▼
┌─────────────────────────┐
│ TripStrategy            │  (Strategy Resolver)
│ Discovery               │  - Maps type → Strategy
└────────┬────────────────┘  - Uses PHP 8 Attributes
         │
         ├─────────────┬─────────────┬─────────────┐
         ▼             ▼             ▼             ▼
┌─────────────┐ ┌──────────────┐ ┌────────────────┐
│    Local    │ │  InterCity   │ │ International  │ (Strategies)
│   Strategy  │ │   Strategy   │ │    Strategy    │
└──────┬──────┘ └──────┬───────┘ └────────┬───────┘
       │               │                   │
       └───────────────┴───────────────────┘
                       │
                       ▼
              ┌────────────────────┐
              │ TripCostCalculator │  (Context)
              │  - setStrategy()   │
              │  - calculate()     │
              └────────┬───────────┘
                       │
                       ▼
              ┌────────────────────┐
              │  Cost Calculation  │
              │  - distance × rate │
              │  - duration × rate │
              │  - additional fees │
              └────────┬───────────┘
                       │
                       ▼
                 JSON Response
```

### 4.2 Class Structure

```php
// Strategy Interface
interface TripCostStrategy
{
    public function calculate(float $distanceKm, float $durationHours): array;
}

// Concrete Strategies
#[TripStrategy('local')]
class LocalTripCostStrategy implements TripCostStrategy

#[TripStrategy('intercity')]
class InterCityTripCostStrategy implements TripCostStrategy

#[TripStrategy('international')]
class InternationalTripCostStrategy implements TripCostStrategy

// Context
class TripCostCalculator
{
    public function __construct(public TripCostStrategy $strategy);
    public function calculateCost(float $distance, float $duration): array;
}

// Strategy Discovery (uses PHP 8 Attributes)
class TripStrategyDiscovery
{
    public function getStrategies(): array; // Returns type => class mapping
}
```

### 4.3 Strategy Selection

Uses PHP 8 Attributes for automatic discovery:

```php
#[TripStrategy('local')]
class LocalTripCostStrategy implements TripCostStrategy
{
    // Cost calculation logic
}
```

The `TripStrategyDiscovery` class scans for `#[TripStrategy]` attributes and builds a registry.

### 4.4 Cost Calculation Formulas

**Local Trip:**
```
total = (distance × $2.50) + (duration × $15.00)
```

**InterCity Trip:**
```
fuel = distance × $0.80
consumption = distance × $1.20
driver = duration × $25.00
total = fuel + consumption + driver
```

**International Trip:**
```
fuel = distance × $1.20
customs = max($2000, fuel × 0.20)
insurance = duration × $150.00
border = $500
total = fuel + customs + insurance + border
```

### 4.5 Extensibility

Adding a new trip type (e.g., "express"):

1. Create `ExpressTripCostStrategy.php` implementing `TripCostStrategy`
2. Add `#[TripStrategy('express')]` attribute
3. Implement `calculate()` method
4. Add validation rule in controller
5. Write tests

**Automatic discovery via attributes** ✅

---

## 5. Database Design

### 5.1 Entity Relationship Diagram

```
┌─────────────────────┐
│      vehicles       │
├─────────────────────┤
│ id (PK)             │
│ registration_number │
│ model               │
│ year                │
│ status              │
│ created_at          │
│ updated_at          │
└──────────┬──────────┘
           │
           │ 1:N
           │
┌──────────▼──────────────┐         ┌─────────────────────┐
│ maintenance_requests    │    N:1  │     mechanics       │
├─────────────────────────┤─────────┤ id (PK)             │
│ id (PK)                 │         │ name                │
│ vehicle_id (FK)         │         │ specialization      │
│ issue_type              │         │ contact             │
│ description             │         └─────────────────────┘
│ status                  │
│ assigned_to (FK)        │
│ estimated_time          │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────────┐
│        trips        │
├─────────────────────┤
│ id (PK)             │
│ vehicle_id (FK)     │
│ driver_id (FK)      │
│ type                │
│ distance_km         │
│ duration_hours      │
│ calculated_cost     │
│ actual_cost         │
│ cost_details (JSON) │
│ status              │
│ started_at          │
│ completed_at        │
│ created_at          │
│ updated_at          │
└─────────────────────┘
```

### 5.2 Table Schemas

#### maintenance_requests

```sql
CREATE TABLE maintenance_requests (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    issue_type ENUM('engine', 'tires', 'electrical') NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'in_progress', 'completed', 'rejected') DEFAULT 'pending',
    assigned_to BIGINT UNSIGNED NULL,
    estimated_time VARCHAR(50),
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_status (status),
    INDEX idx_issue_type (issue_type),
    INDEX idx_created_at (created_at)
);
```

#### trips

```sql
CREATE TABLE trips (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    driver_id BIGINT UNSIGNED NOT NULL,
    type ENUM('local', 'intercity', 'international') NOT NULL,
    distance_km DECIMAL(10, 2) NOT NULL,
    duration_hours DECIMAL(10, 2) NOT NULL,
    calculated_cost DECIMAL(10, 2) NOT NULL,
    actual_cost DECIMAL(10, 2) NULL,
    cost_details JSON,
    status ENUM('planned', 'in_progress', 'completed', 'cancelled') DEFAULT 'planned',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

---

## 6. API Design

### 6.1 RESTful Endpoints

#### Maintenance Requests

**Create Maintenance Request**
```http
POST /api/maintenance/request
Content-Type: application/json

Request:
{
  "vehicle_id": 55,
  "issue_type": "electrical",
  "description": "Car won't start at all."
}

Response: 201 Created
{
  "success": true,
  "request_id": "MR-2025-001234",
  "vehicle_id": 55,
  "issue_type": "electrical",
  "status": "pending",
  "assigned_to": "External Workshop - ElectroAuto",
  "estimated_time": "2-4 hours",
  "message": "Maintenance request created successfully"
}
```

#### Trip Cost Calculation

**Calculate Trip Cost**
```http
POST /api/trip/request
Content-Type: application/json

Request:
{
  "type": "international",
  "distance_km": 800,
  "duration_hours": 10
}

Response: 200 OK
{
  "message": "Trip cost calculated successfully",
  "data": {
    "total_cost": 4500.75,
    "trip_type": "international",
    "details": {
      "base_fuel_cost": 1000.00,
      "custom_fees": 2000.00,
      "insurance": 1500.00,
      "border_crossing": 500.00
    },
    "distance_km": 800,
    "duration_hours": 10
  }
}
```

### 6.2 Error Responses

```json
{
  "message": "Validation failed",
  "errors": {
    "issue_type": ["The selected issue type is invalid."],
    "vehicle_id": ["The vehicle id field is required."]
  }
}
```

---

## 7. Security Architecture

### 7.1 Input Validation
- Laravel Form Request Validation
- Sanitization of user inputs
- Type checking (string, integer, numeric)
- Range validation (min/max values)

### 7.2 Authentication (Optional)
- Laravel Sanctum for API tokens
- JWT tokens for stateless auth
- Role-based access control (RBAC)

### 7.3 Authorization
- Drivers can only create requests
- Managers can approve/reject
- Mechanics can view assigned requests

### 7.4 Data Security
- SQL injection prevention (Eloquent ORM)
- XSS protection (output escaping)
- CSRF tokens for state-changing operations
- Rate limiting on API endpoints

---

## 8. Testing Strategy

### 8.1 Test Pyramid

```
         ┌─────────────┐
         │     E2E     │  (10%)
         │   Tests     │
         └─────────────┘
       ┌─────────────────┐
       │  Integration    │  (20%)
       │     Tests       │
       └─────────────────┘
     ┌───────────────────────┐
     │     Unit Tests        │  (70%)
     │  (TDD Approach)       │
     └───────────────────────┘
```

### 8.2 TDD Workflow

1. **Write Failing Test** (Red)
2. **Write Minimum Code** (Green)
3. **Refactor** (Refactor)
4. **Repeat**

### 8.3 Test Coverage Goals

- **Unit Tests:** 100% coverage of business logic
- **Integration Tests:** API endpoint coverage
- **Feature Tests:** End-to-end user flows

### 8.4 Test Categories

**Unit Tests:**
- Factory pattern functionality
- Strategy pattern calculations
- Validation logic
- Business rules

**Integration Tests:**
- Controller → Service → Database
- External API mocking
- Notification systems

**Feature Tests:**
- Complete request/response cycles
- Error handling
- Edge cases

---

## 9. Deployment Architecture

### 9.1 Environments

- **Local:** SQLite, Debug mode
- **Staging:** MySQL, Error logging
- **Production:** MySQL cluster, Monitoring

### 9.2 CI/CD Pipeline

```
Code Push → Tests → Build → Deploy
    ↓         ↓       ↓       ↓
  GitHub   PHPUnit  Docker  Server
```

---

## 10. Monitoring & Observability

- **Logging:** Laravel Log (Monolog)
- **Error Tracking:** Sentry (optional)
- **Performance:** New Relic / Datadog
- **Metrics:** Request count, response times, error rates

---

## 11. Future Enhancements

1. **Event-Driven Architecture**
   - Event: MaintenanceRequestCreated
   - Listeners: SendNotification, LogActivity

2. **Queue System**
   - Async processing for notifications
   - Background jobs for external APIs

3. **Caching Layer**
   - Redis for trip cost calculations
   - Cache frequently accessed data

4. **Microservices**
   - Separate maintenance service
   - Separate trip calculation service

---

## Conclusion

This architecture provides a solid foundation for the FleetTrack system with:
- ✅ Clean separation of concerns
- ✅ Extensible design patterns
- ✅ Testable components
- ✅ Scalable structure
- ✅ Maintainable codebase

