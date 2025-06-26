<?php

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

it('can get all tasks with filters', function () {
    // Create some test data
    Task::factory()->create(['status' => 'completed']);
    Task::factory()->create(['status' => 'pending']);

    // Instantiate TaskService
    $taskService = new TaskService();

    // Test with filter
    $filters = ['status' => 'completed'];
    $tasks = $taskService->getAll($filters);

    expect($tasks)->toHaveCount(1);
});

it('can get task by id', function () {
    $task = Task::factory()->create();

    $taskService = new TaskService();
    $fetchedTask = $taskService->getById($task->id);

    expect($fetchedTask->id)->toBe($task->id);
});

it('can create a task', function () {
    $data = [
        'title' => 'New Task',
        'description' => 'Task Description',
        'status' => 'pending',
        'priority' => 'medium',
    ];

    $taskService = new TaskService();
    $task = $taskService->create($data);

    expect($task->id)->not()->toBeNull();
    expect($task->title)->toBe('New Task');
});

it('can delete a task', function () {
    $task = Task::factory()->create();

    $taskService = new TaskService();
    $taskService->delete($task);

    expect(Task::find($task->id))->toBeNull();
});