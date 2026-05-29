<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'assigned_by',
        'bucket_id',
        'status',
        'priority',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the users this task is assigned to
     */
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignees');
    }

    /**
     * Get the user who assigned this task
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the bucket this task belongs to
     */
    public function bucket(): BelongsTo
    {
        return $this->belongsTo(Bucket::class);
    }

    /**
     * Get subtasks for this task
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * Get attachments for this task
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class)->latest();
    }

    /**
     * Get comments for this task
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }
}
