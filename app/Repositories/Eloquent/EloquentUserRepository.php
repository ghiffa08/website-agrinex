<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail(string $identifier): ?User
    {
        return Cache::remember(
            "user:identifier:{$identifier}",
            300, // 5 minutes
            fn() => User::where('username', $identifier)
                ->orWhere('email', $identifier)
                ->first()
        );
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        return Cache::remember(
            "user:id:{$id}",
            300,
            fn() => User::find($id)
        );
    }

    /**
     * Create new user
     */
    public function create(array $data): User
    {
        $user = User::create($data);
        
        // Invalidate cache
        Cache::forget("user:identifier:{$user->username}");
        Cache::forget("user:identifier:{$user->email}");
        
        return $user;
    }

    /**
     * Update user data
     */
    public function update(User $user, array $data): bool
    {
        $result = $user->update($data);
        
        if ($result) {
            // Invalidate cache
            Cache::forget("user:id:{$user->id}");
            Cache::forget("user:identifier:{$user->username}");
            Cache::forget("user:identifier:{$user->email}");
        }
        
        return $result;
    }

    /**
     * Check if user is active
     */
    public function isActive(User $user): bool
    {
        return (bool) $user->is_active;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(User $user): bool
    {
        return $user->updateLastLogin();
    }
}
