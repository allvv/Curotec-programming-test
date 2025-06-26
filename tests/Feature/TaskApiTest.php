<?php

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

it('can fetch all tasks', function () {
    Task::factory()->count(5)->create();

    $response = $this->json('GET', '/api/v1/tasks');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'title', 'status', 'priority', 'due_date']
        ]
    ]);
});

it('can create a task via API', function () {
    $data = [
        'title' => 'New Task',
        'description' => 'Task Description',
        'status' => 'pending',
        'priority' => 'medium',
    ];

    $response = $this->json('POST', '/api/v1/tasks', $data);
    $response->assertStatus(201);
    $response->assertJsonFragment(['title' => 'New Task']);
});

it('can update a task via API', function () {
    $task = Task::factory()->create();
    $data = ['title' => 'Updated Task'];

    $response = $this->json('PATCH', "/api/v1/tasks/{$task->id}", $data);
    $response->assertStatus(200);
    $response->assertJsonFragment(['title' => 'Updated Task']);
});

it('can delete a task via API', function () {
    $task = Task::factory()->create();

    $response = $this->json('DELETE', "/api/v1/tasks/{$task->id}");
    $response->assertStatus(204);
    expect(Task::find($task->id))->toBeNull();
});