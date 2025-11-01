# Task factory

## ⚙️ السيناريو العام

إنت شغال في نظام اسمه **FleetTrack**
نظام لإدارة **أسطول سيارات التوصيل** (Drivers, Deliveries, Maintenance, Tracking…).
* * *

## 🚚 Feature 1️⃣: Vehicle Maintenance Request System

### القصة:

في النظام، لما أي سيارة يحصل فيها مشكلة، السائق بيعمل **طلب صيانة (Maintenance Request)**.
لكن أنواع الصيانة مختلفة:

*   **Engine Issue**
*   **Tires Issue**
*   **Electrical Issue**

وكل نوع ليه **process مختلفة للتعامل**:

*   Engine → لازم موافقة من Head Mechanic
*   Tires → يتعامل مع مستودع الإطارات
*   Electrical → يتبعت لورشة كهرباء خارجية عن الشركة
* * *

### 🎯 المطلوب:

نعمل API:

```css
POST /api/maintenance/request
Body:
{
  "vehicle_id": 55,
  "issue_type": "electrical",
  "description": "Car won’t start at all."
}
```

### 🧠 المطلوب منك:

*   تستخدم Factory Method علشان **تولّد الكائن المناسب** حسب `issue_type`
*   كل كائن (`EngineMaintenance`, `TiresMaintenance`, `ElectricalMaintenance`)

ينفّذ نفس الـ Interface لكن بطريقته.

📦 كل نوع ممكن يعمل حاجات مختلفة:

*   يسجّل في جدول معين
*   يبعث Notification داخلية
*   يتواصل مع API خارجي (ورشة الكهرباء مثلاً)
* * *

## 🧾 Feature 2️⃣: Trip Cost Calculation System

### القصة:

الـ FleetTrack عنده أنواع رحلات:

*   **Local Trip** داخل المدينة
*   **InterCity Trip** بين مدينتين
*   **International Trip** (خارج الدولة)

وكل نوع ليه **قواعد حساب تكلفة** مختلفة:

*   Local → السعر بالكم + وقت الرحلة
*   InterCity → تكلفة وقود + استهلاك السيارة
*   International → فيه رسوم جمرك + تأمين خاص
* * *

### 🎯 المطلوب:

API:

```css
POST /api/trips/calculate
Body:
{
  "type": "international",
  "distance_km": 800,
  "duration_hours": 10
}
```

النظام يحسب التكلفة النهائية ويرجعها JSON:

```json
{
  "total_cost": 4500.75,
  "details": {
     "base_fuel_cost": 1000,
     "custom_fees": 2000,
     "insurance": 1500
  }
}
```

* * *

⚙️ General Scenario

You are working on a system called FleetTrack,
a system for managing a fleet of delivery cars (Drivers, Deliveries, Maintenance, Tracking…).

🚚 Feature 1️⃣: Vehicle Maintenance Request System

Story:

In the system, when any car has an issue, the driver creates a maintenance request.
But maintenance types are different:

Engine Issue
Tires Issue
Electrical Issue

And each type has a different process to handle it:

Engine → requires approval from the Head Mechanic
Tires → handled by the tires warehouse
Electrical → sent to an external electrical workshop

🎯 Required:

Create an API:

POST /api/maintenance/request
Body:
{
"vehicle\_id": 55,
"issue\_type": "electrical",
"description": "Car won’t start at all."
}

🧠 Required from you:

Use Factory Method to generate the appropriate object based on issue\_type
Each object (EngineMaintenance, TiresMaintenance, ElectricalMaintenance)
implements the same Interface but in its own way.

📦 Each type can do different things:

Record in a specific table
Send internal notification
Communicate with an external API (e.g., electrical workshop)

🧾 Feature 2️⃣: Trip Cost Calculation System

Story:

FleetTrack has different trip types:

Local Trip (inside the city)
InterCity Trip (between two cities)
International Trip (outside the country)

And each type has different cost calculation rules:

Local → price per km + trip duration
InterCity → fuel cost + vehicle consumption
International → customs fees + special insurance

🎯 Required:

API:

POST /api/trips/calculate
Body:
{
"type": "international",
"distance\_km": 800,
"duration\_hours": 10
}

The system calculates the final cost and returns it as JSON:

{
"total\_cost": 4500.75,
"details": {
"base\_fuel\_cost": 1000,
"custom\_fees": 2000,
"insurance": 1500
}
}