# Technical Solution Design for Head-Hunter Inc.

## High-Level Design

### System Overview
The proposed solution for Head-Hunter Inc. software system. It will consist of the following components:

1. **Frontend**:
   - Clients pages for vacancy application submissions.
   - Admin interface for managing clients, vacancies, and reports.

2. **Backend**:
   - REST API to handle business logic, data persistence, and integrations.
   - Modules for vacancy management, application handling, commission calculations, and invoice generation.

3. **Database**:
   - Centralized relational database for structured storage of clients, roles, vacancies, applications, commissions, and invoices.

4. **Third-Party Integration**:
   - Integration with Free Currency Rates API for real-time currency conversions.
   - https://github.com/fawazahmed0/exchange-api

5. **Testing Framework**:
   - Test coverage using unit, integration, and end-to-end tests.

### Architecture
- **Design Pattern**: Microservices Architecture with separation of concerns.
- **Tech Stack**:
  - Backend: Laravel (PHP).
  - Database: MySQL.
  - Frontend: React.js (Next.js) or Vue.js.
  - Deployment: Dockerized environment (with CI/CD pipelines).

---

## API Specification for 3rd Party Integration

### Endpoint: `POST /api/v1/vacancies`

#### Description
This API allows 3rd party HRM systems to create and manage vacancies for Head-Hunter Inc.

#### Request Headers
- **Content-Type**: `application/json`
- **Authorization**: `Bearer <API_TOKEN>`

#### Request Body
```json
{
  "client_id": 123,
  "role_id": 456,
  "positions": 5,
  "remuneration": 75000,
  "currency_code": "USD",
  "description": "Software Developer position",
  "status": "open"
}
```

- **client_id**: (integer) ID of the client.
- **role_id**: (integer) ID of the role.
- **positions**: (integer) Number of vacant positions.
- **remuneration**: (float) Remuneration per position.
- **currency_code**: (string) ISO 4217 currency code.
- **description**: (string) Description of the vacancy.
- **status**: (string) Status of the vacancy (e.g., `open`, `closed`).

#### Response
- **201 Created**
```json
{
  "vacancy_id": 789,
  "message": "Vacancy created successfully."
}
```

- **400 Bad Request**
```json
{
  "error": "Invalid client_id or role_id."
}
```

- **401 Unauthorized**
```json
{
  "error": "Invalid API token."
}
```

---

## Technical Solution Design Document

### Components Description

#### 1. **Frontend**
- **Client-Specific Pages**:
  - Built using React.js/Vue.js to provide a responsive interface for job applicants.
  - Features include form validation and document upload functionality.
- **Admin Dashboard**:
  - Provides capabilities for managing clients, vacancies, and reports.

#### 2. **Backend**
- **Framework**: Laravel or Node.js with modular controllers and services.
- **Modules**:
  - Vacancy Management: CRUD operations for vacancies.
  - Application Handling: Capture and manage job applications.
  - Currency Conversion: Fetch rates and convert using Free Currency Rates API.
  - Reports: Generate commission and pipeline reports.

#### 3. **Database Design**
- Relational database with optimized indexing for frequent queries.
- Entity Relationships:
  - `clients` to `vacancies`: One-to-Many.
  - `vacancies` to `applications`: One-to-Many.
  - `vacancies` to `commissions`: One-to-One.

#### 4. **Testing**
- Unit Tests:
  - Test all controllers, services, and models.
- Integration Tests:
  - Validate API endpoints with mock data.
- End-to-End Tests:
  - Simulate user interactions on the frontend.

---

## Money in the Pipeline Report (PoC Implementation)

### Approach
1. **Pipeline Report Calculation**:
   - Fetch all vacancies with their respective applications.
   - Calculate the average asking remuneration for each vacancy.
   - Multiply the average by the number of positions and apply a 10% commission rate.
   - Convert the result to USD using the latest currency conversion rate.

2. **Database Query Example**:
```sql
SELECT
    v.id AS vacancy_id,
    v.positions,
    AVG(a.asking_remuneration) AS avg_remuneration,
    c.rate_to_usd
FROM
    vacancies v
JOIN
    applications a ON v.id = a.vacancy_id
JOIN
    currencies c ON v.currency_code = c.currency_code
WHERE
    v.status = 'open'
GROUP BY
    v.id;
```

3. **Commission Calculation Formula**:
```
Total Commission (USD) = Positions * AVG(Asking Remuneration) * 0.1 * Rate to USD
```

### Implementation (Code Snippet)
```php
public function calculatePipelineCommission()
{
    $vacancies = DB::table('vacancies as v')
        ->join('applications as a', 'v.id', '=', 'a.vacancy_id')
        ->join('currencies as c', 'v.currency_code', '=', 'c.currency_code')
        ->select('v.id', 'v.positions', DB::raw('AVG(a.asking_remuneration) as avg_remuneration'), 'c.rate_to_usd')
        ->where('v.status', 'open')
        ->groupBy('v.id')
        ->get();

    $pipeline = $vacancies->map(function ($vacancy) {
        return $vacancy->positions * $vacancy->avg_remuneration * 0.1 * $vacancy->rate_to_usd;
    });

    return response()->json(['total_pipeline_commission_usd' => $pipeline->sum()]);
}
```

---

## Conclusion
The proposed solution ensures adherence to best practices and scalability while meeting all requirements for Head-Hunter Inc. The API and database design align with the need for modularity, simplicity, and future extensibility.

