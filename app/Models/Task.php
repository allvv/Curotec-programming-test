<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeDateRange($query, $start, $end)
    {
        if ($start && $end) {
            return $query->whereBetween('due_date', [$start, $end]);
        }
        if ($start) {
            return $query->where('due_date', '>=', $start);
        }
        if ($end) {
            return $query->where('due_date', '<=', $end);
        }
        return $query;
    }
}
