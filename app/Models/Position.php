<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'name',
        'code',
        'level',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Get the users for this position
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get positions by level
     */
    public static function getByLevel($level)
    {
        return self::where('level', $level)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if position is director level
     */
    public function isDirector()
    {
        return $this->level === 1;
    }

    /**
     * Check if position is manager level
     */
    public function isManager()
    {
        return $this->level === 2;
    }

    /**
     * Check if position is staff level
     */
    public function isStaff()
    {
        return $this->level === 3;
    }
}
