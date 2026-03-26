<?php

namespace Modules\BibleLearning\Repositories;

use App\Models\BlQuiz;
use Illuminate\Database\Eloquent\Collection;
use Modules\BibleLearning\Contracts\QuizRepositoryInterface;

class QuizRepository implements QuizRepositoryInterface
{
    public function getRandomQuizzes(int $limit = 10): Collection
    {
        return BlQuiz::inRandomOrder()->limit($limit)->get();
    }
}
