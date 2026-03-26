<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\BibleLearning\Contracts\EventRepositoryInterface;

class EventController extends Controller
{
    protected EventRepositoryInterface $repository;

    public function __construct(EventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * View container cho Dòng Timeline Lịch sử 3D
     */
    public function index()
    {
        return view('biblelearning::timeline.index');
    }

    /**
     * API: Lấy list các mốc thời gian
     */
    public function getEvents()
    {
        return response()->json($this->repository->getTimelineEvents());
    }
}
