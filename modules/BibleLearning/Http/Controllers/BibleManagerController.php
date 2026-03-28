<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\BibleLearning\Services\BibleManagerService;

/**
 * G-A-E-V Layer 4: Thin Controller
 * Tuyệt đối cấm sử dụng Model (DB Queries) ở đây. Tất cả phải chuyển giao về cho Lớp Não (Service).
 */
class BibleManagerController extends Controller
{
    protected BibleManagerService $service;

    public function __construct(BibleManagerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('biblelearning::manager.index');
    }

    public function getBooks(): JsonResponse
    {
        return response()->json($this->service->getAllBooks());
    }

    public function getChapters(Request $request): JsonResponse
    {
        $bookId = $request->input('book_id');
        if (! $bookId) {
            return response()->json(['error' => 'Vui lòng cung cấp book_id'], 400);
        }

        return response()->json($this->service->getChaptersByBook((int) $bookId));
    }

    public function getVerses(Request $request): JsonResponse
    {
        $chapterId = $request->input('chapter_id');
        if (! $chapterId) {
            return response()->json(['error' => 'Vui lòng cung cấp chapter_id'], 400);
        }

        return response()->json($this->service->getVersesByChapter((int) $chapterId));
    }

    public function updateVerse(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $updated = $this->service->updateVerseContent($id, $request->input('content'));
            if (! $updated) {
                return response()->json(['error' => 'Không tìm thấy ID chứa Câu Kinh Thánh tương ứng.'], 404);
            }

            return response()->json(['success' => true, 'verse' => $updated]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
