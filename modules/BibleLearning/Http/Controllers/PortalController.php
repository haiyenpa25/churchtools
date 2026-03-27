<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BlFlashcard;
use App\Models\BlTempEntity;
use Modules\BibleLearning\Contracts\ApprovalRepositoryInterface;
use Modules\BibleLearning\Contracts\FlashcardRepositoryInterface;

class PortalController extends Controller
{
    protected ApprovalRepositoryInterface $approvalRepo;

    protected FlashcardRepositoryInterface $flashcardRepo;

    public function __construct(ApprovalRepositoryInterface $approvalRepo, FlashcardRepositoryInterface $flashcardRepo)
    {
        $this->approvalRepo = $approvalRepo;
        $this->flashcardRepo = $flashcardRepo;
    }

    /**
     * Màn hình chính Dashboard Hub
     */
    public function index()
    {
        return view('biblelearning::portal.index');
    }

    /**
     * API: Lấy số liệu thống kê cho Dashboard
     */
    public function getStats()
    {
        $pendingCount = $this->approvalRepo->getPendingItems()->count();
        $dueCardsCount = $this->flashcardRepo->getDueFlashcards()->count();
        $totalCards = BlFlashcard::count();

        // Tương lai sẽ thêm Event count ở đây
        $totalEvents = BlTempEntity::where('type', 'event')->where('status', 'approved')->count();

        return response()->json([
            'pending_approvals' => $pendingCount,
            'due_flashcards' => $dueCardsCount,
            'total_flashcards' => $totalCards,
            'total_events' => $totalEvents,
        ]);
    }
}
