<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'full_name',
        'role',
        'phone_number',
        'is_active',
        'google_id',
        'avatar',
        'last_login_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Avatar URL Accessor
     * - Returns full URL for external avatars (Google OAuth)
     * - Returns storage URL for local uploads
     * - Returns null or fallback for missing avatar
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }
        
        // Check if it's a full URL (starts with http:// or https://)
        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }
        
        // Check if it's a relative storage path (contains 'avatars/')
        if (Str::contains($this->avatar, 'avatars/')) {
            return asset('storage/' . $this->avatar);
        }
        
        // Return as-is (could be a path from storage without 'avatars/' prefix)
        return asset('storage/' . $this->avatar);
    }

    /**
     * Get the user's initials for avatar fallback
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->full_name ?? $this->username ?? $this->email ?? 'U';
        $parts = explode(' ', $name);
        $initials = '';
        
        foreach ($parts as $part) {
            if (strlen($part) > 0) {
                $initials .= strtoupper(substr($part, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        
        return $initials ?: strtoupper(substr($name, 0, 1));
    }
}
