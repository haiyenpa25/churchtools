<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\BibleLearning\Services\ApprovalService;

class ApprovalController extends Controller
{
    protected ApprovalService $service;

    public function __construct(ApprovalService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('biblelearning::approval.index');
    }

    public function fetchPending()
    {
        return response()->json($this->service->getPendingItemsForVue());
    }

    public function approve(Request $request, int $id)
    {
        try {
            $this->service->approveItem($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function reject(Request $request, int $id)
    {
        try {
            $this->service->rejectItem($id);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
