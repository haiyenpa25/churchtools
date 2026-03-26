<?php

namespace Modules\BibleLearning\Services;

use App\Models\BlFlashcardReview;
use Modules\BibleLearning\Contracts\FlashcardRepositoryInterface;

class SpacedRepetitionService
{
    protected FlashcardRepositoryInterface $repository;

    public function __construct(FlashcardRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getDueCardsForStudy(): array
    {
        return $this->repository->getDueFlashcards()->toArray();
    }

    /**
     * Submit điểm đánh giá: 0 (Again), 1 (Hard), 2 (Good), 3 (Easy)
     */
    public function processCardReview(int $flashcardId, int $rating): void
    {
        // Rating:
        // 0: Quên sạch (Again) -> Lập tức lùi về 1 ngày, giảm Ease Factor.
        // 1: Khó (Hard) -> Tăng nhẹ ngày.
        // 2: Tốt (Good) -> Tăng ngày dựa trên Ease Factor hiện tại.
        // 3: Quá dễ (Easy) -> Tăng bạo ngày, cộng thêm Ease Factor.

        // Môi trường 1 User không login, lấy First Record
        $entity = BlFlashcardReview::where('flashcard_id', $flashcardId)->first();

        $easeFactor = $entity ? $entity->ease_factor : 2.5;
        $interval = $entity ? $entity->interval : 0;

        // Thuật toán SM-2 rút gọn
        if ($rating === 0) {
            $interval = 1;
            $easeFactor = max(1.3, $easeFactor - 0.2);
        } else {
            if ($interval === 0) {
                // Thẻ mới tinh, lần đầu trả lời đúng
                $interval = ($rating === 3) ? 4 : 1;
            } elseif ($interval === 1) {
                // Lần 2 trả lời đúng
                $interval = ($rating === 3) ? 6 : 3;
            } else {
                // Lần ôn thứ 3 trở lên
                $interval = round($interval * $easeFactor);
                if ($rating === 1) {
                    $interval = round($interval * 0.8);
                } elseif ($rating === 3) {
                    $interval = round($interval * 1.3);
                }
            }

            if ($rating === 1) {
                $easeFactor = max(1.3, $easeFactor - 0.15);
            } elseif ($rating === 3) {
                $easeFactor += 0.15;
            }
        }

        $nextReview = now()->addDays($interval)->toDateTimeString();

        $this->repository->updateReviewProgress($flashcardId, $easeFactor, $interval, $nextReview);
    }
}
