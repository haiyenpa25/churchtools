<?php

namespace Modules\BibleLearning\Repositories;

use App\Models\BlFlashcard;
use App\Models\BlFlashcardReview;
use Illuminate\Database\Eloquent\Collection;
use Modules\BibleLearning\Contracts\FlashcardRepositoryInterface;

class FlashcardRepository implements FlashcardRepositoryInterface
{
    public function getDueFlashcards(): Collection
    {
        // Trả về các flashcard có review date <= NOW(), hoặc chưa từng review.
        return BlFlashcard::where('status', 'active')
            ->where(function ($query) {
                // Flashcard chưa từng được học (không có trong bảng reviews)
                $query->doesntHave('reviews')
                    // Hoặc Flashcard đã học và tới hạn
                    ->orWhereHas('reviews', function ($subQuery) {
                        $subQuery->where('next_review_date', '<=', now());
                    });
            })
            ->with('reviews') // Load kèm state hiện tại để Vue biết
            ->limit(20) // Mỗi lần chỉ học 20 thẻ để chống ngợp
            ->get();
    }

    public function updateReviewProgress(int $flashcardId, float $easeFactor, int $interval, string $nextReviewDate): void
    {
        BlFlashcardReview::updateOrCreate(
            ['flashcard_id' => $flashcardId],
            [
                'ease_factor' => $easeFactor,
                'interval' => $interval,
                'next_review_date' => $nextReviewDate,
            ]
        );
    }
}
