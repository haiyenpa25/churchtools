<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\BibleLearning\Contracts\QuizRepositoryInterface;

class QuizController extends Controller
{
    protected QuizRepositoryInterface $repository;

    public function __construct(QuizRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Container cho màn hình Đấu Trường Gamification
     */
    public function index()
    {
        return view('biblelearning::quiz.index');
    }

    /**
     * API: Lấy bộ đề thi ngẫu nhiên (10 câu)
     */
    public function fetchQuizSession()
    {
        $quizzes = $this->repository->getRandomQuizzes(7); // Lấy 7 câu mỗi lượt chơi
        return response()->json($quizzes);
    }
}
