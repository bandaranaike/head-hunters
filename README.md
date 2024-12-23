# Head-Hunters API - Money in Pipeline Report

## Overview
This project is a Laravel 11 API designed to calculate and report the total pipeline commission in USD for Head-Hunters' recruitment business. It runs within a Dockerized environment for consistency and ease of deployment.

## Key Features
- **API Endpoint**: Calculates the total pipeline commission.
- **Database-Driven**: Utilizes a relational database with preloaded currency data (680 currencies).
- **Eloquent Relationships**: Simplifies data handling using Laravel's ORM.
- **Docker Support**: Ensures a consistent development and production environment.
- **Automated Testing**: Validates functionality using Laravel's testing suite.

## Getting Started

### Prerequisites
1. **Docker**: Ensure Docker is installed on your machine.
2. **Git**: Install Git to clone the repository.
3. **Composer**: Required for managing Laravel dependencies.
4. **Postman or Curl**: For API testing (optional).

### Installation
1. Clone the project repository:
   ```bash
   git clone https://github.com/bandaranaike/head-hunters.git
   cd head-hunters
   ```

2. Start Docker containers:
   ```bash
   docker-compose up -d
   ```

3. Install Laravel dependencies:
   ```bash
   docker-compose exec app composer install
   ```

4. Set up the `.env` file:
    - Copy the example file:
      ```bash
      cp .env.example .env
      ```
    - Update the database and application configurations as needed.

5. Run migrations to set up the database:
   ```bash
   docker-compose exec app php artisan migrate
   ```

6. Seed the database (ensure 680 currencies are preloaded):
   ```bash
   docker-compose exec app php artisan db:seed
   ```

### API Endpoint
- **Endpoint**: `http://localhost/api/report/money-in-pipeline`
- **Method**: `GET`
- **Expected Output**:
  ```json
  {
    "total_pipeline_commission_usd": 0
  }
  ```

### Running the Application
1. Ensure Docker containers are running:
   ```bash
   docker-compose up -d
   ```
2. Access the API endpoint:
    - Using Postman or Curl:
      ```bash
      curl http://localhost/api/report/money-in-pipeline
      ```

### Running Tests
1. Execute all tests:
   ```bash
   docker-compose exec app php artisan test
   ```

### Test Details
- The test for the Money in Pipeline Report ensures:
    - Accurate calculation of pipeline commission in USD.
    - Proper integration of preloaded currency data.
    - Validation of business logic with real and mock data.

### Key Files
- **Controller**: `app/Http/Controllers/ReportController.php`
- **Test**: `tests/Feature/MoneyInPipelineTest.php`
- **Models**: Located in `app/Models`.
- **Docker Configuration**: `docker-compose.yml`
- **Migrations**: Located in `database/migrations`.

## Support
For issues or questions, feel free to raise a GitHub issue or contact the repository maintainer.

---

### Quick Start Summary
1. Clone the repo.
2. Run `docker-compose up -d`.
3. Run migrations and seed the database.
4. Access `http://localhost/api/report/money-in-pipeline` to fetch the pipeline report.
5. Test the functionality with `php artisan test`.

Enjoy building with Head-Hunters API!

