<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Create a notification for a user.
     */
    public static function notify(int $userId, string $type, string $title, string $body, array $data = [], ?int $actorId = null): static
    {
        return static::create([
            'user_id' => $userId,
            'actor_id' => $actorId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => empty($data) ? null : $data,
        ]);
    }
}
