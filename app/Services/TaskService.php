<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    public function getAll(array $filters = [])
    {
        $perPage = $filters['per_page'] ?? 10;

        return Task::query()
            ->when(isset($filters['status']), fn($q) => $q->status($filters['status']))
            ->when(isset($filters['priority']), fn($q) => $q->priority($filters['priority']))
            ->when(isset($filters['start_date']) || isset($filters['end_date']), function ($q) use ($filters) {
            $q->dateRange($filters['start_date'] ?? null, $filters['end_date'] ?? null);
        })
            ->paginate($perPage);
    }

    public function getById(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}