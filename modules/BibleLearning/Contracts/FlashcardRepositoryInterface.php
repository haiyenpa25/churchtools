<?php

namespace Modules\BibleLearning\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface FlashcardRepositoryInterface
{
    /**
     * Lấy danh sách các thẻ Flashcard đã đến hạn ôn tập (next_review_date <= Now)
     */
    public function getDueFlashcards(): Collection;

    /**
     * Khởi tạo hoặc cập nhật bản ghi kiểm tra của một thẻ
     */
    public function updateReviewProgress(int $flashcardId, float $easeFactor, int $interval, string $nextReviewDate): void;
}
