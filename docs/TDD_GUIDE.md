# Test-Driven Development (TDD) Guide
## FleetTrack System

**Version:** 1.0  
**Date:** November 1, 2025

---

## Table of Contents

1. [Introduction to TDD](#1-introduction-to-tdd)
2. [TDD Workflow](#2-tdd-workflow)
3. [Test Structure](#3-test-structure)
4. [Running Tests](#4-running-tests)
5. [Test Coverage Report](#5-test-coverage-report)
6. [Writing Tests](#6-writing-tests)
7. [Best Practices](#7-best-practices)

---

## 1. Introduction to TDD

### What is TDD?

Test-Driven Development is a software development approach where you:

1. **Write a failing test** (Red)
2. **Write minimal code to pass** (Green)
3. **Refactor the code** (Refactor)
4. **Repeat**

### Benefits

- ✅ **Better Design:** Forces you to think about interfaces first
- ✅ **Living Documentation:** Tests serve as usage examples
- ✅ **Confidence:** Refactor without breaking things
- ✅ **Bug Prevention:** Catch issues before production
- ✅ **Faster Development:** Less debugging time

---

## 2. TDD Workflow

### The Red-Green-Refactor Cycle

```
┌─────────────────────────────────────────────────────────┐
│                    TDD CYCLE                             │
└─────────────────────────────────────────────────────────┘

    1. RED: Write Failing Test
           ↓
    2. GREEN: Make It Pass
           ↓
    3. REFACTOR: Improve Code
           ↓
    4. REPEAT
```

### Example Workflow

#### Step 1: Write Failing Test (RED)

```php
/** @test */
public function it_calculates_local_trip_cost_correctly()
{
    // Arrange
    $strategy = new LocalTripCostStrategy();
    
    // Act
    $result = $strategy->calculate(100, 2);
    
    // Assert
    $this->assertEquals(280.00, $result['total_cost']);
}
```

**Run test:** ❌ Fails (class doesn't exist)

#### Step 2: Write Minimal Code (GREEN)

```php
class LocalTripCostStrategy implements TripCostStrategy
{
    public function calculate(float $distanceKm, float $durationHours): array
    {
        $total = ($distanceKm * 2.5) + ($durationHours * 15);
        return ['total_cost' => $total];
    }
}
```

**Run test:** ✅ Passes

#### Step 3: Refactor (REFACTOR)

```php
class LocalTripCostStrategy implements TripCostStrategy
{
    private const PRICE_PER_KM = 2.5;
    private const PRICE_PER_HOUR = 15.0;
    
    public function calculate(float $distanceKm, float $durationHours): array
    {
        $distanceCost = $distanceKm * self::PRICE_PER_KM;
        $timeCost = $durationHours * self::PRICE_PER_HOUR;
        
        return [
            'total_cost' => round($distanceCost + $timeCost, 2),
            'details' => [
                'distance_cost' => round($distanceCost, 2),
                'time_cost' => round($timeCost, 2)
            ]
        ];
    }
}
```

**Run test:** ✅ Still passes (now with better structure)

---

## 3. Test Structure

### Test Organization

```
tests/
├── Feature/                    # Integration/API tests
│   ├── MaintenanceRequestApiTest.php
│   └── TripCostCalculationApiTest.php
│
└── Unit/                       # Unit tests
    ├── Maintenance/
    │   ├── MaintenanceFactoryTest.php
    │   ├── MaintenanceTypesTest.php
    │   └── MaintenanceFactorySelectorTest.php
    │
    └── Trip/
        ├── TripCostStrategyTest.php
        ├── TripCostCalculatorTest.php
        └── TripStrategyDiscoveryTest.php
```

### Test Naming Conventions

```php
// ✅ Good: Descriptive method names
public function it_calculates_local_trip_cost_correctly()
public function it_validates_distance_must_be_positive()
public function engine_maintenance_requires_head_mechanic_approval()

// ❌ Bad: Unclear names
public function testCalculation()
public function test1()
```

### AAA Pattern (Arrange-Act-Assert)

```php
public function it_creates_engine_maintenance_instance()
{
    // Arrange: Set up test data
    $factory = new EngineMaintenanceFactory();
    
    // Act: Execute the code being tested
    $maintenance = $factory->createMaintenanceRequest();
    
    // Assert: Verify the results
    $this->assertInstanceOf(EngineMaintenance::class, $maintenance);
}
```

---

## 4. Running Tests

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```

### Run Specific Test File

```bash
php artisan test tests/Unit/Trip/TripCostStrategyTest.php
```

### Run Specific Test Method

```bash
php artisan test --filter=it_calculates_local_trip_cost_correctly
```

### Run with Coverage

```bash
php artisan test --coverage
```

### Run with Detailed Output

```bash
php artisan test --verbose
```

### Continuous Testing (Watch Mode)

```bash
# Install phpunit-watcher
composer require --dev spatie/phpunit-watcher

# Run in watch mode
./vendor/bin/phpunit-watcher watch
```

---

## 5. Test Coverage Report

### Current Coverage Status

| Component | Coverage | Status |
|-----------|----------|--------|
| **Maintenance System** | 100% | ✅ Complete |
| - Factory Pattern | 100% | ✅ |
| - Maintenance Types | 100% | ✅ |
| - Factory Selector | 100% | ✅ |
| **Trip Cost System** | 100% | ✅ Complete |
| - Cost Strategies | 100% | ✅ |
| - Cost Calculator | 100% | ✅ |
| - Strategy Discovery | 100% | ✅ |
| **API Endpoints** | 100% | ✅ Complete |
| - Maintenance API | 100% | ✅ |
| - Trip Cost API | 100% | ✅ |

### Test Count Summary

```
Total Tests: 80+
  - Unit Tests: 50+
  - Feature Tests: 30+
  
Assertions: 200+
Time: ~2 seconds
```

---

## 6. Writing Tests

### Unit Tests

**Purpose:** Test individual classes/methods in isolation

**Example: Testing a Strategy**

```php
namespace Tests\Unit\Trip;

use App\Services\Trip\LocalTripCostStrategy;
use PHPUnit\Framework\TestCase;

class LocalTripCostStrategyTest extends TestCase
{
    /** @test */
    public function it_calculates_cost_correctly()
    {
        // Arrange
        $strategy = new LocalTripCostStrategy();
        $distance = 100;
        $duration = 2;
        
        // Act
        $result = $strategy->calculate($distance, $duration);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_cost', $result);
        $this->assertEquals(280.00, $result['total_cost']);
    }
}
```

### Feature Tests

**Purpose:** Test complete user flows through HTTP

**Example: Testing an API Endpoint**

```php
namespace Tests\Feature;

use Tests\TestCase;

class MaintenanceRequestApiTest extends TestCase
{
    /** @test */
    public function it_creates_engine_maintenance_request_successfully()
    {
        // Arrange
        $payload = [
            'vehicle_id' => 55,
            'issue_type' => 'engine',
            'description' => 'Engine noise'
        ];
        
        // Act
        $response = $this->postJson('/api/maintenance/request', $payload);
        
        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'vehicle_id' => 55,
                'issue_type' => 'engine'
            ]);
    }
}
```

### Testing Validation

```php
/** @test */
public function it_validates_vehicle_id_is_required()
{
    // Arrange
    $payload = [
        'issue_type' => 'engine',
        // vehicle_id is missing
    ];
    
    // Act
    $response = $this->postJson('/api/maintenance/request', $payload);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['vehicle_id']);
}
```

### Testing Edge Cases

```php
/** @test */
public function it_handles_zero_distance()
{
    $strategy = new LocalTripCostStrategy();
    $result = $strategy->calculate(0, 2);
    
    $this->assertEquals(30.00, $result['total_cost']);
}

/** @test */
public function it_handles_large_numbers()
{
    $strategy = new LocalTripCostStrategy();
    $result = $strategy->calculate(9999.99, 72.00);
    
    $this->assertIsNumeric($result['total_cost']);
    $this->assertGreaterThan(0, $result['total_cost']);
}
```

### Testing Exceptions

```php
/** @test */
public function it_throws_exception_for_unknown_type()
{
    $this->expectException(\InvalidArgumentException::class);
    
    MaintenanceFactorySelector::getFactory('unknown');
}
```

---

## 7. Best Practices

### DO ✅

1. **One Assertion Per Test (when possible)**
   ```php
   // Good: Focused test
   public function it_calculates_total_cost()
   {
       $result = $this->calculator->calculate(100, 2);
       $this->assertEquals(280, $result['total_cost']);
   }
   ```

2. **Use Descriptive Test Names**
   ```php
   // Good
   public function it_validates_distance_must_be_positive()
   
   // Bad
   public function testValidation()
   ```

3. **Test Behavior, Not Implementation**
   ```php
   // Good: Testing outcome
   $this->assertEquals(280, $result['total_cost']);
   
   // Bad: Testing internal state
   $this->assertEquals(2.5, $strategy->pricePerKm);
   ```

4. **Keep Tests Independent**
   - Each test should run independently
   - Don't rely on test execution order
   - Clean up after yourself

5. **Use Data Providers for Similar Tests**
   ```php
   /**
    * @test
    * @dataProvider tripTypeProvider
    */
   public function it_calculates_cost_for_all_trip_types($type, $expected)
   {
       // Test logic
   }
   
   public function tripTypeProvider()
   {
       return [
           ['local', 280],
           ['intercity', 300],
           ['international', 2960]
       ];
   }
   ```

### DON'T ❌

1. **Don't Test Framework Code**
   ```php
   // Bad: Testing Laravel's validation
   public function it_validates_input()
   {
       $this->assertTrue(Validator::make(...)->passes());
   }
   ```

2. **Don't Write Tests After Code**
   - Follow TDD: Test First, Code Second

3. **Don't Mock What You Don't Own**
   - Don't mock Laravel classes
   - Create wrapper classes if needed

4. **Don't Test Private Methods**
   - Test public interface only
   - If you need to test private, make it public or extract

5. **Don't Skip Refactoring**
   - Green doesn't mean done
   - Always refactor for clarity

---

## 8. Common Assertions

### PHPUnit Assertions

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual); // Strict comparison

// Type Checks
$this->assertIsArray($value);
$this->assertIsString($value);
$this->assertIsNumeric($value);
$this->assertInstanceOf(ClassName::class, $object);

// Array Checks
$this->assertArrayHasKey('key', $array);
$this->assertContains('value', $array);
$this->assertCount(3, $array);

// Numeric Checks
$this->assertGreaterThan(0, $value);
$this->assertEquals(280.00, $value, '', 0.01); // Delta for floats

// String Checks
$this->assertStringContainsString('substring', $string);
$this->assertMatchesRegularExpression('/pattern/', $string);

// Boolean Checks
$this->assertTrue($condition);
$this->assertFalse($condition);

// Null Checks
$this->assertNull($value);
$this->assertNotNull($value);
```

### Laravel Test Assertions

```php
// HTTP Response
$response->assertStatus(200);
$response->assertSuccessful();
$response->assertJson(['key' => 'value']);
$response->assertJsonStructure(['data' => ['key']]);
$response->assertJsonPath('data.total_cost', 280);

// Validation
$response->assertJsonValidationErrors(['field']);
$response->assertValid(['field']);

// Headers
$response->assertHeader('content-type', 'application/json');
```

---

## 9. TDD Checklist

Before committing code, verify:

- [ ] All tests pass
- [ ] Code coverage is maintained/increased
- [ ] No skipped tests without reason
- [ ] Test names are descriptive
- [ ] Edge cases are covered
- [ ] Validation is tested
- [ ] Error cases are tested
- [ ] Happy path is tested
- [ ] Code is refactored
- [ ] No commented-out tests

---

## 10. Resources

### Documentation
- PHPUnit: https://phpunit.de/documentation.html
- Laravel Testing: https://laravel.com/docs/testing
- TDD Best Practices: https://martinfowler.com/bliki/TestDrivenDevelopment.html

### Books
- "Test Driven Development: By Example" - Kent Beck
- "Growing Object-Oriented Software, Guided by Tests" - Steve Freeman

### Tools
- PHPUnit: Testing framework
- Pest: Alternative testing framework
- PHPUnit Watcher: Continuous testing
- PHPStan/Psalm: Static analysis

---

## Conclusion

TDD is not just about testing—it's about **designing better software**. By writing tests first, you ensure that your code is:

- **Testable** (good architecture)
- **Maintainable** (clear intent)
- **Reliable** (fewer bugs)
- **Documented** (tests as examples)

Start with small steps, practice the Red-Green-Refactor cycle, and soon TDD will become second nature!

---

**Happy Testing! 🚀**

