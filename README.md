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

### Testing Setup Notes
- In-memory SQLite database is used during testing for speed and isolation.
- The .env.testing file should include:
    ```bash
    APP_ENV=testing
    APP_KEY=base64:YOUR_KEY_HERE
    DB_CONNECTION=sqlite
    DB_DATABASE=:memory:
- Migrations are automatically run for each test using RefreshDatabase.

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

    | Parameter    | Values                                |
    | ------------ | ------------------------------------- |
    | `status`     | `pending`, `in_progress`, `completed` |
    | `priority`   | `low`, `medium`, `high`               |
    | `start_date` | Format: `YYYY-MM-DD` (for due date)   |
    | `end_date`   | Format: `YYYY-MM-DD` (for due date)   |
    | `page`       | Integer (e.g., `1`, `2`, etc.)        |
    | `per_page`   | Integer (max: 100, default: 10)       |

Example:

    GET /api/v1/tasks?status=completed&priority=high&start_date=2024-01-01&end_date=2024-12-31&per_page=5


##UI Details
  - The user interface is designed to be simple and functional. It allows for basic task management, including creating, updating, deleting, and filtering tasks. Here are some important aspects of the UI:
  - Task List: The UI displays a table listing all tasks, including their ID, title, status, priority, and due date. This allows users to view their tasks quickly.
  - Task Filters: A basic filter form allows users to filter tasks based on status and priority. Pagination is also implemented to handle large numbers of tasks.
  - Task Creation/Editing: A single form is used to both create and update tasks. The form includes fields for title, description, status, priority, and due_date. When a task is edited, the form is populated with the current task's details.
  - Alerts: Success and error messages are displayed as alerts, providing feedback to the user after actions like creating, updating, or deleting tasks.
  
##Justification for Simplicity
  - The UI is intentionally kept simple to focus on the core backend functionality for this project since I am a backend developer applying for a backend role. 
  - I was told I could keep the UI simple for this reason 
   
## API Examples
All API endpoints are versioned under /api/v1.

1. Create Task:
    ```bash
    curl -X POST http://localhost/api/v1/tasks \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d '{
        "title": "Buy groceries",
        "description": "Milk, eggs, bread",
        "status": "pending",
        "priority": "high"
    }'

2. Get All Tasks
    ```bash
    curl http://localhost/api/v1/tasks \
     -H "Accept: application/json"
  
3. Filter Tasks by Status & Priority
    ```bash
    curl "http://localhost/api/v1/tasks?status=pending&priority=high" \
      -H "Accept: application/json"

4. Paginate Results
    ```bash
    curl "http://localhost/api/v1/tasks?page=2&per_page=5" \
      -H "Accept: application/json"

5. Get a Specific Task
   ```bash
   curl http://localhost/api/v1/tasks/1 \
     -H "Accept: application/json"

6. Update a Task
    ```bash
    curl -X PATCH http://localhost/api/v1/tasks/1 \
      -H "Content-Type: application/json" \
      -H "Accept: application/json" \
      -d '{
        "status": "completed",
        "priority": "medium"
    }'

7. Delete a Task
   ```bash
   curl -X DELETE http://localhost/api/v1/tasks/1 \
     -H "Accept: application/json"

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

## Testing

This project uses [Pest](https://pestphp.com/) for unit and feature testing.

To run the tests:
    
    ./vendor/bin/sail test
    # or
    ./vendor/bin/sail pest
    
### Test Coverage
Test cases include:

- Task API: list, create, update, and delete tasks
- TaskService: create, retrieve, filter, and delete tasks
- JSON response structure and status code assertions


## Architectural Decisions

- Form Request Validation: Using TaskStoreRequest, TaskUpdateRequest, and TaskFilterRequest for input validation and request sanitization.
- Service Layer: Encapsulated business logic in TaskService to keep controllers slim and follow the Single Responsibility Principle.
- Query Scopes: Added Eloquent scopes (status, priority, dateRange) for modular filtering.
- Pagination Support: Paginated responses with customizable per_page parameter (default 10).
- Versioned API Routing: All endpoints are versioned under /api/v1 to support future backward-compatible changes.
- API Resources: TaskResource ensures consistent and clean JSON formatting across all responses.

## To Do / In Progress
- Add caching layer for performance (e.g., Redis)
 
## Postman Collection
A Postman collection is included for quick testing of all API endpoints.
- File: TaskManagerAPI.postman_collection.json
- Covers all major endpoints: list, filter, create, update, delete
- No authentication or environment variables required — just import and run

To use:

1. Open Postman
2. Click “Import” > “Upload Files”
3. Select TaskManagerAPI.postman_collection.json from the project root
 
## Notes
- API responses are returned in JSON format only.
- This project uses Laravel Sail for local Docker-based development.