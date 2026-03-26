<?php

namespace Modules\BibleLearning\Repositories;

use App\Models\BlTempEntity;
use Illuminate\Database\Eloquent\Collection;
use Modules\BibleLearning\Contracts\ApprovalRepositoryInterface;

class ApprovalRepository implements ApprovalRepositoryInterface
{
    public function getPendingItems(): Collection
    {
        return BlTempEntity::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function findById(int $id): ?BlTempEntity
    {
        return BlTempEntity::find($id);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $entity = $this->findById($id);
        if ($entity) {
            $entity->status = $status;

            return $entity->save();
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $entity = $this->findById($id);
        if ($entity) {
            return $entity->delete();
        }

        return false;
    }
}
