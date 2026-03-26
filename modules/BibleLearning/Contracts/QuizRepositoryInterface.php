<?php

namespace Modules\BibleLearning\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface QuizRepositoryInterface
{
    /**
     * Lấy ngẫu nhiên N câu hỏi trắc nghiệm để thả vào Đấu Trường
     */
    public function getRandomQuizzes(int $limit = 10): Collection;
}
