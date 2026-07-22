<?php

namespace App\Repositories\Contracts;

interface LahanPantauRepositoryInterface
{
    /**
     * Get all lahan pantau with devices count
     */
    public function getAll();

    /**
     * Get lahan pantau by ID with devices
     */
    public function getById(int $id);

    /**
     * Create new lahan pantau
     */
    public function create(array $data);

    /**
     * Update lahan pantau
     */
    public function update(int $id, array $data);

    /**
     * Delete lahan pantau
     */
    public function delete(int $id);

    /**
     * Get lahan pantau with full device details
     */
    public function getWithDevices(int $id);
}
