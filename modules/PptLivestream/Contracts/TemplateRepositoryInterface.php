<?php

namespace Modules\PptLivestream\Contracts;

use App\Models\PptTemplate;
use Illuminate\Database\Eloquent\Collection;

interface TemplateRepositoryInterface
{
    /**
     * Tìm template theo ID hoặc file path
     */
    public function findById(int $id): ?PptTemplate;

    /**
     * Lấy toàn bộ danh sách mẫu
     */
    public function getAll(): Collection;

    public function create(array $data): PptTemplate;

    public function update(int $id, array $data): PptTemplate;

    public function delete(int $id): bool;
}
