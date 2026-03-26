<?php

namespace Modules\BibleLearning\Contracts;

use App\Models\BlTempEntity;
use Illuminate\Database\Eloquent\Collection;

interface ApprovalRepositoryInterface
{
    /**
     * Get all pending approval entities.
     */
    public function getPendingItems(): Collection;

    /**
     * Find a specific entity by ID.
     */
    public function findById(int $id): ?BlTempEntity;

    /**
     * Update the status of a specific entity.
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Delete an entity from the temp table.
     */
    public function delete(int $id): bool;
}
