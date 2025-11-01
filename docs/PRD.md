# Product Requirements Document (PRD)
## FleetTrack - Vehicle Management System

**Version:** 1.0  
**Date:** November 1, 2025  
**Status:** Active Development

---

## 1. Executive Summary

FleetTrack is a comprehensive fleet management system designed to manage delivery vehicle operations, including maintenance tracking and trip cost calculations. This PRD covers two core features essential for operational efficiency.

---

## 2. Feature 1: Vehicle Maintenance Request System

### 2.1 Overview
A system that allows drivers and fleet managers to submit and process maintenance requests for vehicles based on specific issue types.

### 2.2 Business Objectives
- Streamline vehicle maintenance workflow
- Reduce vehicle downtime by 30%
- Ensure proper routing of maintenance requests to appropriate departments
- Maintain audit trail of all maintenance activities

### 2.3 User Stories

**As a Driver**, I want to:
- Submit maintenance requests when I notice vehicle issues
- Specify the type of issue (Engine, Tires, Electrical)
- Provide detailed descriptions of problems
- Receive confirmation of request submission

**As a Fleet Manager**, I want to:
- View all pending maintenance requests
- Approve critical maintenance (especially engine issues)
- Track maintenance costs and timelines

**As a Mechanic**, I want to:
- Receive notifications for relevant maintenance types
- Access vehicle history and issue details

### 2.4 Functional Requirements

#### FR-1: Maintenance Request Submission
- **Priority:** P0 (Critical)
- **Input:**
  - `vehicle_id` (required, integer)
  - `issue_type` (required, enum: engine|tires|electrical)
  - `description` (optional, string, max 1000 chars)
  
- **Validation Rules:**
  - Vehicle must exist in system
  - Issue type must be valid
  - Description sanitized for XSS

#### FR-2: Issue Type Processing Rules

| Issue Type | Process Flow | Notification | External Integration |
|------------|-------------|--------------|---------------------|
| **Engine** | Requires Head Mechanic approval | Internal notification to Head Mechanic | None |
| **Tires** | Auto-routed to warehouse | Warehouse inventory system | Tire warehouse API |
| **Electrical** | External workshop assignment | Email to external partner | External workshop API |

#### FR-3: Response Format
```json
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

### 2.5 Non-Functional Requirements

- **Performance:** API response time < 200ms
- **Availability:** 99.9% uptime
- **Security:** Role-based access control (RBAC)
- **Scalability:** Support 10,000+ vehicles
- **Auditability:** All requests logged with timestamps

### 2.6 Success Metrics

- Request submission success rate > 99%
- Average processing time < 5 minutes
- User satisfaction score > 4.5/5
- Reduction in duplicate requests by 40%

---

## 3. Feature 2: Trip Cost Calculation System

### 3.1 Overview
An automated system that calculates trip costs based on trip type, distance, and duration using different pricing strategies.

### 3.2 Business Objectives
- Provide accurate trip cost estimates for budgeting
- Enable dynamic pricing based on trip characteristics
- Support financial forecasting and reporting
- Ensure transparency in cost breakdown

### 3.3 User Stories

**As a Dispatcher**, I want to:
- Calculate trip costs before assigning drivers
- Compare costs across different trip types
- Generate cost reports for management

**As a Finance Manager**, I want to:
- View detailed cost breakdowns
- Track actual vs. estimated costs
- Analyze cost trends by trip type

**As a Driver**, I want to:
- Understand how my trip compensation is calculated
- View trip details and cost components

### 3.4 Functional Requirements

#### FR-1: Trip Cost Calculation
- **Priority:** P0 (Critical)
- **Input:**
  - `type` (required, enum: local|intercity|international)
  - `distance_km` (required, numeric, min: 0, max: 10000)
  - `duration_hours` (required, numeric, min: 0, max: 72)

#### FR-2: Cost Calculation Rules

**Local Trip:**
- Base rate: $2.50 per km
- Time rate: $15.00 per hour
- Formula: `(distance × 2.5) + (duration × 15)`

**InterCity Trip:**
- Fuel cost: $0.80 per km
- Vehicle consumption: $1.20 per km
- Driver allowance: $25.00 per hour
- Formula: `(distance × 2.0) + (duration × 25)`

**International Trip:**
- Base fuel cost: $1.20 per km
- Customs fees: Fixed $2000 or 20% of fuel cost (whichever higher)
- Insurance: $150 per hour
- Border crossing: $500 (if applicable)
- Formula: `(distance × 1.2) + customs + (duration × 150) + 500`

#### FR-3: Response Format
```json
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
    "duration_hours": 10,
    "calculated_at": "2025-11-01T10:30:00Z"
  }
}
```

### 3.5 Non-Functional Requirements

- **Performance:** Calculation time < 50ms
- **Accuracy:** Cost precision to 2 decimal places
- **Reliability:** 99.99% calculation accuracy
- **Extensibility:** Easy to add new trip types
- **Testability:** 100% unit test coverage

### 3.6 Success Metrics

- Calculation accuracy > 99.9%
- API response time < 100ms
- Cost variance (actual vs. estimated) < 5%
- Zero calculation errors in production

---

## 4. Technical Stack

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Database:** SQLite (Development), MySQL (Production)
- **Testing:** PHPUnit 10.x
- **API:** RESTful JSON API
- **Design Patterns:** Factory Method, Strategy Pattern

---

## 5. API Endpoints

### Maintenance Requests
```
POST /api/maintenance/request
Content-Type: application/json
Authorization: Bearer {token}
```

### Trip Cost Calculation
```
POST /api/trip/request
Content-Type: application/json
Authorization: Bearer {token}
```

---

## 6. Dependencies

- Laravel Framework
- PHPUnit for testing
- Laravel Sanctum (optional for authentication)

---

## 7. Risks & Mitigation

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| External API downtime | High | Medium | Implement retry logic + fallback queue |
| Invalid cost calculations | High | Low | Comprehensive unit testing + validation |
| Database performance | Medium | Low | Indexing + caching strategy |
| Security vulnerabilities | High | Low | Input validation + RBAC + audit logs |

---

## 8. Timeline

- **Week 1:** Architecture + TDD tests
- **Week 2:** Implementation + Integration
- **Week 3:** Testing + Bug fixes
- **Week 4:** Documentation + Deployment

---

## 9. Acceptance Criteria

### Maintenance System
- ✅ All three issue types process correctly
- ✅ Appropriate notifications sent
- ✅ External integrations functional
- ✅ 100% test coverage

### Trip Cost System
- ✅ All three trip types calculate accurately
- ✅ Cost breakdowns match specifications
- ✅ Performance requirements met
- ✅ 100% test coverage

---

## 10. Future Enhancements

- Real-time cost tracking during trips
- AI-based maintenance prediction
- Mobile app integration
- Multi-currency support
- Historical cost analytics dashboard

