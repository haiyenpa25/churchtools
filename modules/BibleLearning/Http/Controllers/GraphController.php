<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BibleLearning\Contracts\GraphRepositoryInterface;
use Modules\BibleLearning\Services\GraphParserService;
use Modules\BibleLearning\Services\BibleTextService;
use Modules\BibleLearning\Services\BibleCommentaryService;

class GraphController extends Controller
{
    protected GraphRepositoryInterface $repository;
    protected GraphParserService $parser;
    protected BibleTextService $bibleText;
    protected BibleCommentaryService $commentary;

    public function __construct(
        GraphRepositoryInterface $repository,
        GraphParserService $parser,
        BibleTextService $bibleText,
        BibleCommentaryService $commentary
    ) {
        $this->repository = $repository;
        $this->parser     = $parser;
        $this->bibleText  = $bibleText;
        $this->commentary = $commentary;
    }

    /**
     * View container cho Mạng Lưới Đồ Thị
     */
    public function index()
    {
        return view('biblelearning::graph.index');
    }

    /**
     * API: Cấp phát mảng JSON chứa Nodes và Edges
     */
    public function fetchGraph()
    {
        return response()->json($this->repository->getNetworkData());
    }

    /**
     * API: Lấy danh sách node láng giềng (neighbors) cho Detail Panel
     * GET /api/graph/neighbors/{nodeId}
     */
    public function fetchNeighbors(int $nodeId)
    {
        $data      = $this->repository->getNetworkData();
        $nodeMap   = collect($data['nodes'])->keyBy('id')->toArray();
        $neighbors = [];

        foreach ($data['edges'] as $edge) {
            $neighborId = null;
            $relation   = $edge['label'] ?? '';

            if ($edge['from'] === $nodeId) {
                $neighborId = $edge['to'];
            } elseif ($edge['to'] === $nodeId) {
                $neighborId = $edge['from'];
                $relation   = '← ' . $relation;
            }

            if ($neighborId && isset($nodeMap[$neighborId])) {
                $n = $nodeMap[$neighborId];
                $neighbors[] = [
                    'id'           => $n['id'],
                    'label'        => $n['label'],
                    'group'        => $n['group'],
                    'title'        => $n['title'] ?? '',
                    'relationship' => $relation,
                ];
            }
        }

        $grouped = [];
        foreach ($neighbors as $nb) {
            $grouped[$nb['group']][] = $nb;
        }

        return response()->json([
            'node'      => $nodeMap[$nodeId] ?? null,
            'neighbors' => $grouped,
            'total'     => count($neighbors),
        ]);
    }

    /**
     * API: Nhận văn bản tự do, trả về nodes + edges do AI tạo ra
     * POST /api/graph/parse-text
     */
    public function parseText(Request $request)
    {
        $request->validate(['text' => 'required|string|min:10|max:5000']);

        $result = $this->parser->parseText($request->input('text'));

        if (! ($result['ok'] ?? false)) {
            return response()->json(['error' => $result['error'] ?? 'Unknown error'], 422);
        }

        return response()->json($result);
    }

    // ─── Bible Text Local ──────────────────────────────────────────────────

    /**
     * API: Đọc câu Kinh Thánh từ file local
     * GET /api/bible/text?book=Ma-thi-ơ&chapter=1
     */
    public function getBibleText(Request $request)
    {
        $request->validate([
            'book'    => 'required|string|max:100',
            'chapter' => 'required|integer|min:1|max:150',
        ]);

        $result = $this->bibleText->getChapter(
            $request->input('book'),
            (int) $request->input('chapter')
        );

        if (! $result['ok']) {
            return response()->json(['error' => $result['error']], 404);
        }

        return response()->json($result);
    }

    /**
     * API: Đọc giải nghĩa Kinh Thánh từ file local (pagination support)
     * GET /api/bible/commentary?book=Sáng Thế Ký&page=1
     */
    public function getBibleCommentary(Request $request)
    {
        $request->validate([
            'book' => 'required|string|max:100',
            'page' => 'integer|min:1|max:50',
        ]);

        $result = $this->commentary->getCommentaryPage(
            $request->input('book'),
            (int) $request->input('page', 1),
            3000
        );

        if (! $result['ok']) {
            return response()->json(['error' => $result['error']], 404);
        }

        return response()->json($result);
    }

    /**
     * API: Lấy format guide để import dữ liệu Kinh Thánh
     * GET /api/bible/import-guide
     * Tính năng hỗ trợ: định nghĩa format chuẩn để import sau này
     */
    public function getBibleImportGuide()
    {
        return response()->json([
            'bible_text'  => $this->bibleText->getImportFormatGuide(),
            'commentary'  => $this->commentary->getImportFormatGuide(),
            'book_list'   => $this->bibleText->getBookList(),
        ]);
    }

    /**
     * API: Danh sách sách có trong file giải nghĩa
     * GET /api/bible/commentary-books
     */
    public function getCommentaryBooks()
    {
        return response()->json([
            'books' => $this->commentary->listAvailableBooks(),
        ]);
    }
}
