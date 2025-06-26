# Task Manager API

A RESTful task management API built with Laravel 11. Designed as part of a technical assessment to demonstrate clean architecture, use of Laravel features, and best development practices. Includes filtering, pagination, versioned endpoints, and request validation.

---

## Project Setup

### Requirements

- Docker
- Docker Compose
- PHP ≥ 8.2 (via Sail)
- Composer

### Setup Instructions

1. Clone the repo:
   ```bash
   git clone https://github.com/allvv/Curotec-programming-test.git
   cd Curotec-programming-test

2. Install dependencies and setup Laravel Sail:
    ```bash
    composer install
    cp .env.example .env
    php artisan key:generate

3. Update the variables in the .env file:

    Set the following variables to match your local environment:
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=task_manager
    DB_USERNAME=sail
    DB_PASSWORD=password     

4. Start Docker and run Sail:
    ```bash
    ./vendor/bin/sail up -d
 
5. Run migrations and seeders:
    ```bash
    ./vendor/bin/sail artisan migrate --seed

## API Usage
All API endpoints are versioned under /api/v1.
    
Task Endpoints:

    | Method | Endpoint           | Description        |
    | ------ | ------------------ | ------------------ |
    | GET    | /api/v1/tasks      | List tasks         |
    | POST   | /api/v1/tasks      | Create task        |
    | GET    | /api/v1/tasks/{id} | Show specific task |
    | PATCH  | /api/v1/tasks/{id} | Update task        |
    | DELETE | /api/v1/tasks/{id} | Delete task        |


## Filtering & Pagination
You can filter tasks using the following query parameters:

- status → pending, in_progress, completed
- priority → low, medium, high
- start_date / end_date → for due date range
- per_page → number of items per page (max 100)

Example:

    GET /api/v1/tasks?status=completed&priority=high&start_date=2024-01-01&end_date=2024-12-31&per_page=5

   
## Audit Trail

The application includes an audit trail system for tracking changes to tasks. This feature uses Laravel's model observers and a polymorphic relationship to log activity.

- All task creation, updates and deletes are automatically logged.
- Each log includes the type of action (`created`, `updated`, `deleted`) and the `before` and `after` state of the task (for updates and deletes).
- Logs are stored in a dedicated `activity_logs` table using a polymorphic relationship.
- The `user_id` field is nullable, as authentication is not implemented in this project.
- Logs are not included in task API responses by default. They can be queried directly from the database or exposed through a dedicated endpoint if needed.
    
## Error & Exception Handling
- All API routes are configured to return JSON responses only, including for error cases such as 404 Not Found, 422 Validation Errors, and 500 Internal Server Errors.
- Laravel’s default exception handler (App\Exceptions\Handler) has been customized to ensure clean, consistent API responses.
- Errors are logged internally using Laravel’s logging system (default: storage/logs/laravel.log).
- Error messages shown to the client are sanitized in production for security. Stack traces and debug details are only shown if APP_DEBUG=true in your .env.    
    
## Architectural Decisions

- Form Request Validation: Using TaskStoreRequest, TaskUpdateRequest, and TaskFilterRequest for input validation and request sanitization.
- Service Layer: Encapsulated business logic in TaskService to keep controllers slim and follow the Single Responsibility Principle.
- Query Scopes: Added Eloquent scopes (status, priority, dateRange) for modular filtering.
- Pagination Support: Paginated responses with customizable per_page parameter (default 10).
- Versioned API Routing: All endpoints are versioned under /api/v1 to support future backward-compatible changes.
- API Resources: TaskResource ensures consistent and clean JSON formatting across all responses.

## To Do / In Progress
- Implement Audit Trail via polymorphic model ActivityLog
- Add Model Observers for logging task changes
- Implement proper error and exception handling 
- Add caching layer for performance (e.g., Redis)
- Add frontend UI (minimal) with Bootstrap or jQuery
- Handle global API exception formatting (JSON only)
- Include Postman Collection for testing endpoints
 
 
## Notes
- API responses are returned in JSON format only.
- This project uses Laravel Sail for local Docker-based development.