<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail(string $identifier): ?User;

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User;

    /**
     * Create new user
     */
    public function create(array $data): User;

    /**
     * Update user data
     */
    public function update(User $user, array $data): bool;

    /**
     * Check if user is active
     */
    public function isActive(User $user): bool;

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(User $user): bool;
}
