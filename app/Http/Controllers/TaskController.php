<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Requests\TaskFilterRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{

    public function __construct(protected TaskService $taskService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(TaskFilterRequest $request)
    {
        $tasks = $this->taskService->getAll($request->all());

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        $task = $this->taskService->create($request->validated());

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $updatedTask = $this->taskService->update($task, $request->validated());

        return new TaskResource($updatedTask);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->taskService->delete($task);

        return response()->json(null, 204);
    }
}
