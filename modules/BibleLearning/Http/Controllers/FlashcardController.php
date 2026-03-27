<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BlTempEntity;
use Exception;
use Illuminate\Http\Request;
use Modules\BibleLearning\Contracts\BibleLearningExtractorContract;
use Modules\BibleLearning\Services\RagScraperService;
use Modules\BibleLearning\Services\SpacedRepetitionService;

class FlashcardController extends Controller
{
    protected SpacedRepetitionService $srsService;

    protected RagScraperService $scraperService;

    protected BibleLearningExtractorContract $aiExtractor;

    public function __construct(
        SpacedRepetitionService $srsService,
        RagScraperService $scraperService,
        BibleLearningExtractorContract $aiExtractor
    ) {
        $this->srsService = $srsService;
        $this->scraperService = $scraperService;
        $this->aiExtractor = $aiExtractor;
    }

    /**
     * View container cho Vue lật thẻ
     */
    public function study()
    {
        return view('biblelearning::study.index');
    }

    /**
     * API: Lấy thẻ tới hạn
     */
    public function getDueCards()
    {
        return response()->json($this->srsService->getDueCardsForStudy());
    }

    /**
     * API: Nhận kết quả đánh giá (0, 1, 2, 3)
     */
    public function submitReview(Request $request, int $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:0|max:3',
        ]);

        try {
            $this->srsService->processCardReview($id, $request->input('rating'));

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * API: Kích hoạt Bot Scraper đi lấy Raw Data Kinh Thánh
     */
    public function crawlHTTLVN(Request $request)
    {
        $url = $request->input('url', 'https://kinhthanh.httlvn.org/?v=VI1934');

        try {
            // Chạy RAG Crawler bóc dữ liệu đa nguồn từ các trang chính thống
            $rawText = $this->scraperService->scrapeArticle($url);

            // GỌI NÓNG API GEMINI ĐỂ BÓC TÁCH THÀNH JSON OBJECTS
            $extractedEntities = $this->aiExtractor->extract($rawText);

            $insertedCount = 0;
            // LƯU CÁC KẾT QUẢ VÀO BẢNG NHÁP (APPROVAL CENTER)
            if (is_array($extractedEntities) && count($extractedEntities) > 0) {
                foreach ($extractedEntities as $entity) {
                    if (isset($entity['type']) && isset($entity['raw_data'])) {
                        BlTempEntity::create([
                            'type' => $entity['type'], // flashcard, event, quiz, node, edge
                            'title' => $entity['title'] ?? ('[Auto] '.strtoupper($entity['type']).' Trích xuất '.date('Y-m-d')),
                            'raw_data' => is_string($entity['raw_data']) ? $entity['raw_data'] : json_encode($entity['raw_data']),
                            'status' => 'pending',
                        ]);
                        $insertedCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'scraped_words' => str_word_count($rawText),
                'extracted_count' => $insertedCount,
                'message' => "Gemini đã quét Web và nhả ra $insertedCount siêu thực thể chờ Duyệt!",
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
