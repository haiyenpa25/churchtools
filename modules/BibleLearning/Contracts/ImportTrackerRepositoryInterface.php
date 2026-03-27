<?php

namespace Modules\BibleLearning\Contracts;

interface ImportTrackerRepositoryInterface
{
    /**
     * Tìm bản ghi theo thư mục và tên file
     */
    public function findByCategoryAndName(string $category, string $fileName);

    /**
     * Cập nhật hoặc tạo mới bản ghi tracking
     */
    public function updateOrCreate(array $attributes, array $values);

    /**
     * Lấy danh sách toàn bộ file đã track trong 1 category
     */
    public function getByCategory(string $category);
}
