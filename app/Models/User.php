<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'position_id', 'profile_photo', 'temp_password', 'temp_password_set_at'])]
#[Hidden(['password', 'remember_token', 'temp_password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'temp_password_set_at' => 'datetime',
        ];
    }

    /**
     * Get the position for this user
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Check if user is director (level 1)
     */
    public function isDirector(): bool
    {
        return $this->position?->level === 1 || $this->role === 'director';
    }

    /**
     * Check if user is manager (level 2)
     */
    public function isManager(): bool
    {
        return $this->position?->level === 2 || $this->role === 'manager';
    }

    /**
     * Check if user is staff (level 3)
     */
    public function isStaff(): bool
    {
        return $this->position?->level === 3 || $this->role === 'staff';
    }

    /**
     * Get user level (1, 2, or 3) - for authorization
     */
    public function getLevel(): int
    {
        return $this->position?->level ?? match ($this->role) {
            'director' => 1,
            'manager' => 2,
            'staff' => 3,
            default => 3,
        };
    }

    /**
     * Check if user can create accounts (director or manager)
     */
    public function canCreateAccounts(): bool
    {
        return in_array($this->role, ['director', 'manager']);
    }

    /**
     * Get full public URL for profile photo.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }

        return Storage::url($this->profile_photo);
    }
}
