# Why Strategy Pattern for Trip vs Factory Pattern for Maintenance

## Design Pattern Selection Rationale

---

## 🎯 Quick Answer

**Trip Cost Calculation** → **Strategy Pattern**
- We're selecting different **algorithms** at runtime
- All strategies do the same thing (calculate cost) but differently
- The behavior changes, not the object type

**Maintenance Request** → **Factory Method Pattern**
- We're creating different **types of objects** with different behaviors
- Each maintenance type has unique workflow and requirements
- The object type changes based on issue type

---

## 📊 Detailed Comparison

### Strategy Pattern (Trip Cost)

**Problem:**
Calculate trip costs using **different formulas/algorithms** based on trip type.

**Why Strategy Pattern?**

1. **Same Interface, Different Algorithm**
   ```php
   // All strategies implement the same method
   interface TripCostStrategy {
       public function calculate(float $distance, float $duration): array;
   }
   
   // Local: Simple calculation
   LocalTripCostStrategy: (distance × $2.50) + (duration × $15)
   
   // InterCity: More complex with multiple factors
   InterCityTripCostStrategy: fuel + consumption + driver allowance
   
   // International: Even more complex
   InternationalTripCostStrategy: fuel + customs + insurance + border
   ```

2. **Runtime Algorithm Selection**
   ```php
   // We select the calculation algorithm at runtime
   $strategy = $strategies[$data['type']]; // 'local', 'intercity', 'international'
   $calculator = new TripCostCalculator(app($strategy));
   $result = $calculator->calculateCost($distance, $duration);
   ```

3. **Behavioral Flexibility**
   - The **behavior** (how we calculate) changes
   - The **input/output** structure remains the same
   - Easy to add new calculation strategies without touching existing code

4. **No Object Creation Complexity**
   - We don't need to create different "Trip" objects
   - We just need different calculation algorithms
   - The calculator context switches strategies dynamically

---

### Factory Method Pattern (Maintenance)

**Problem:**
Create **different types of maintenance objects** with completely different workflows.

**Why Factory Pattern?**

1. **Different Object Types with Different Behaviors**
   ```php
   // Each maintenance type is a different object with unique behavior
   
   EngineMaintenance:
   - Requires Head Mechanic approval
   - Has priority levels (critical, high, medium, low)
   - 4-8 hours estimated time
   - Status: 'pending_approval'
   
   TiresMaintenance:
   - Routes to warehouse
   - Checks inventory
   - 1-2 hours estimated time
   - Status: 'pending'
   
   ElectricalMaintenance:
   - Routes to external workshop
   - Different vendor integration
   - 2-4 hours estimated time
   - Status: 'pending'
   ```

2. **Object Creation Based on Type**
   ```php
   // We create DIFFERENT objects, not just different algorithms
   $factory = MaintenanceFactorySelector::getFactory('engine');
   $maintenance = $factory->createMaintenanceRequest(); // Creates EngineMaintenance object
   $result = $maintenance->handle($data); // Different workflow
   ```

3. **Complex Object Creation Logic**
   - Each type has **completely different properties**
   - Each type has **different processing workflows**
   - Each type may need **different dependencies**

4. **Encapsulated Creation Logic**
   - Factory handles the complexity of creating the right object
   - Client doesn't need to know which concrete class to instantiate
   - Easy to add new maintenance types

---

## 🔍 Key Differences

| Aspect | Strategy Pattern (Trip) | Factory Pattern (Maintenance) |
|--------|------------------------|-------------------------------|
| **Purpose** | Select algorithm at runtime | Create objects at runtime |
| **What Changes** | Calculation method | Object type and behavior |
| **Complexity** | Simple algorithm swap | Complex object creation |
| **Output Structure** | Same (cost + details) | Different per type |
| **Use Case** | "HOW to calculate" | "WHAT to create" |
| **Flexibility** | Behavioral | Creational |

---

## 💡 Real-World Analogy

### Strategy Pattern (Trip)
Think of **different routes to the same destination**:
- 🚗 Driving: Distance × gas price
- 🚂 Train: Ticket price + time
- ✈️ Flying: Base fare + duration

**Same goal** (get from A to B), **different methods** (how to calculate cost).

### Factory Pattern (Maintenance)
Think of **different types of repair shops**:
- 🔧 Engine Shop: Specialized mechanics, complex diagnostics
- 🛞 Tire Center: Quick service, inventory management
- ⚡ Electrical Workshop: External specialist, advanced equipment

**Different shops** (objects), **different processes** (workflows), **different outcomes**.

---

## 📈 When to Use Each Pattern

### Use Strategy Pattern When:
✅ You have **multiple algorithms** for the same task
✅ The **input and output** are consistent
✅ You want to **switch behavior at runtime**
✅ The difference is in **"HOW"** something is done
✅ Example: Payment methods, sorting algorithms, compression algorithms

### Use Factory Pattern When:
✅ You need to **create different types of objects**
✅ The **object structure and behavior** differ significantly
✅ You want to **encapsulate object creation**
✅ The difference is in **"WHAT"** is created
✅ Example: Document types, notification channels, vehicle types

---

## 🎨 Code Example: Why Not Factory for Trip?

**If we used Factory for Trip (WRONG approach):**
```php
// This would be unnecessarily complex!
class LocalTrip {
    public function __construct(float $distance, float $duration) {}
    public function calculateCost() { /* calculation */ }
    public function getVehicleType() { return 'car'; }
    public function getRoute() { /* route logic */ }
}

class InternationalTrip {
    public function __construct(float $distance, float $duration) {}
    public function calculateCost() { /* calculation */ }
    public function getVisaRequirements() { /* visa logic */ }
    public function getCustomsForms() { /* customs logic */ }
}
```
❌ Too much complexity for just calculating a cost!
❌ We don't need different "Trip" objects
❌ We only need different calculation algorithms

**Strategy Pattern (CORRECT approach):**
```php
// Simple and clean!
$strategy = new InternationalTripCostStrategy();
$result = $strategy->calculate($distance, $duration);
// Done! Just the calculation we need.
```
✅ Simple and focused
✅ Only the calculation changes
✅ No unnecessary object complexity

---

## 🎨 Code Example: Why Not Strategy for Maintenance?

**If we used Strategy for Maintenance (WRONG approach):**
```php
// This wouldn't capture the complexity!
interface MaintenanceStrategy {
    public function process(array $data): array;
}

class EngineStrategy implements MaintenanceStrategy {
    public function process(array $data): array {
        // But how do we handle approval workflow?
        // How do we manage priority?
        // How do we route to different departments?
        // Too much logic for a simple strategy!
    }
}
```
❌ Doesn't capture the full object complexity
❌ Loses the workflow encapsulation
❌ Hard to extend with new behaviors

**Factory Pattern (CORRECT approach):**
```php
// Each maintenance type is a full object with complete behavior
class EngineMaintenance implements MaintenanceInterface {
    public function handle(array $data): array {
        // Complete workflow
        $this->checkPriority();
        $this->requireApproval();
        $this->notifyHeadMechanic();
        $this->logToDatabase();
        return $this->formatResponse();
    }
}
```
✅ Full object with complete behavior
✅ Encapsulates entire workflow
✅ Easy to add new properties and methods

---

## 🏗️ Architecture Benefits

### Trip Cost (Strategy)
```
TripCostCalculator (Context)
    ↓
[Strategy Interface]
    ↓
    ├── LocalTripCostStrategy (Algorithm 1)
    ├── InterCityTripCostStrategy (Algorithm 2)
    └── InternationalTripCostStrategy (Algorithm 3)
```
- **Lightweight**: Just algorithm selection
- **Flexible**: Easy to swap algorithms
- **Testable**: Test each algorithm independently

### Maintenance (Factory)
```
MaintenanceFactorySelector
    ↓
[Factory Interface]
    ↓
    ├── EngineMaintenanceFactory → EngineMaintenance (Object 1)
    ├── TiresMaintenanceFactory → TiresMaintenance (Object 2)
    └── ElectricalMaintenanceFactory → ElectricalMaintenance (Object 3)
```
- **Encapsulated**: Creation logic hidden
- **Extensible**: Add new types easily
- **Decoupled**: Client doesn't know concrete classes

---

## 🎯 Summary

| Pattern | Trip Cost | Maintenance |
|---------|-----------|-------------|
| **Pattern Used** | Strategy | Factory Method |
| **Reason** | Different algorithms | Different objects |
| **Focus** | Behavior selection | Object creation |
| **Complexity** | Low | High |
| **When to Change** | New calculation formula | New maintenance type |
| **Example Addition** | Add "express" trip calculation | Add "brake" maintenance |

---

## 🚀 Best Practices

### For Trip (Strategy):
1. Keep strategies stateless
2. Focus on calculation logic only
3. Use constants for rates
4. Return consistent structure
5. Easy to test in isolation

### For Maintenance (Factory):
1. Encapsulate full workflows
2. Include all type-specific logic
3. Handle notifications/integrations
4. Maintain object state
5. Test complete behavior

---

## 📚 Further Reading

- **Strategy Pattern**: Gang of Four Design Patterns, pp. 315-323
- **Factory Pattern**: Gang of Four Design Patterns, pp. 107-116
- **When to Use What**: "Head First Design Patterns" Chapter 4 & 5

---

**Remember:** The pattern choice depends on **WHAT changes** in your system:
- If **behavior/algorithm** changes → **Strategy Pattern**
- If **object type/structure** changes → **Factory Pattern**

In our case:
- **Trip**: Only the calculation changes → Strategy ✅
- **Maintenance**: The entire object and workflow changes → Factory ✅

