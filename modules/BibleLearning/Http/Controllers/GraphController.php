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

    // ─── Admin Tool Data Portability ───────────────────────────────────────

    /**
     * API: Reset DB
     */
    public function adminReset()
    {
        \Illuminate\Support\Facades\Artisan::call('bible:reset', ['--force' => true]);
        return response()->json(['message' => 'Reset Database thành công. Knowledge Graph đã được dọn sạch!']);
    }

    /**
     * API: Đóng gói JSON
     */
    public function adminExport()
    {
        \Illuminate\Support\Facades\Artisan::call('bible:export-dump');
        return response()->json(['message' => 'Đóng gói JSON thành công vào thư mục Git database/data/bible_dump!']);
    }

    /**
     * API: Import JSON
     */
    public function adminImport()
    {
        \Illuminate\Support\Facades\Artisan::call('bible:import-dump');
        return response()->json(['message' => 'Đã nạp thành công toàn bộ file JSON vào Database MySQL!']);
    }

    /**
     * API: Kích hoạt ngầm AI Quét Kinh Thánh
     */
    public function adminIngest(Request $request)
    {
        $category = $request->input('category', 'kinh-thanh');
        \Illuminate\Support\Facades\Artisan::call('bible:ingest', ['--category' => $category]);
        return response()->json(['message' => "Đã gửi lệnh quét thư mục [{$category}] tới Queue! Hãy đảm bảo AI Worker đang chạy."]);
    }

    /**
     * API: Lấy Trạng Thái File theo Thư mục
     */
    public function adminGetIngestionStatus(Request $request, \Modules\BibleLearning\Services\ImportTrackerService $trackerService)
    {
        $category = $request->input('category', 'kinh-thanh');
        $path = base_path("tai-lieu/{$category}");
        
        if (!\Illuminate\Support\Facades\File::exists($path)) {
            return response()->json(['error' => 'Category folder not found', 'files' => []], 404);
        }

        $filesOut = [];
        $trackerRecords = $trackerService->getCategoryStatus($category)->keyBy('file_name');

        foreach (\Illuminate\Support\Facades\File::files($path) as $file) {
            if ($file->getExtension() !== 'txt') continue;
            
            $filename = $file->getFilenameWithoutExtension();
            $hash = md5_file($file->getRealPath());
            $record = $trackerRecords->get($filename);

            $status = 'pending';
            if ($record) {
                if ($record->file_hash !== $hash && $record->status === 'completed') {
                    $status = 'changed'; // File was edited after AI ran
                } else {
                    $status = $record->status;
                }
            }

            $filesOut[] = [
                'file_name' => $filename,
                'status' => $status,
                'nodes_added' => $record ? $record->nodes_added : 0,
                'edges_added' => $record ? $record->edges_added : 0,
                'total_chunks' => $record ? $record->total_chunks : 0,
                'processed_chunks' => $record ? $record->processed_chunks : 0,
                'updated_at' => $record ? $record->updated_at->format('Y-m-d H:i') : null,
                'error_log' => $record ? $record->error_log : null
            ];
        }

        // Sort by filename or status
        usort($filesOut, function ($a, $b) {
            return strnatcmp($a['file_name'], $b['file_name']);
        });

        return response()->json(['files' => $filesOut]);
    }

    /**
     * API: Nạp 1 File duy nhất qua Option --book
     */
    public function adminIngestSingleFile(Request $request)
    {
        $category = $request->input('category', 'kinh-thanh');
        $filename = $request->input('filename');
        
        if (!$filename) {
            return response()->json(['error' => 'Missing filename parameter'], 400);
        }

        \Illuminate\Support\Facades\Artisan::call('bible:ingest', [
            '--category' => $category,
            '--book' => $filename
        ]);

        return response()->json(['message' => "Đã gửi tệp $filename vào Hàng đợi AI (Job Queue)!"]);
    }
}
