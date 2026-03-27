<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\BibleLearning\Contracts\BibleLearningExtractorContract;

class GeminiExtractionService implements BibleLearningExtractorContract
{
    protected ?string $apiKey;

    public function __construct()
    {
        $path = storage_path('app/bible_gemini_key.txt');
        if (\Illuminate\Support\Facades\File::exists($path)) {
            $key = trim(\Illuminate\Support\Facades\File::get($path));
            if (!empty($key)) {
                $this->apiKey = $key;
                return;
            }
        }
        $this->apiKey = env('GEMINI_API_KEY', 'AIzaSyBYnWx6PJprTFX6GxWAiQZ8YT8vjcOH1BA'); // Giả lập key default
    }

    public function extract(string $text, string $context = ''): array
    {
        $prompt = "Bạn là môt chuyên gia Thần học Cơ Đốc (Tin Lành). Dựa vào văn bản dưới đây, hãy trích xuất các thông tin Đáng Giá và phân loại chúng thành một MẢNG JSON thuần túy (không dùng ```json raw code block). Yêu cầu trích xuất cạn kiệt nhưng KHÔNG BỊA RA THÔNG TIN CHƯA CÓ TRONG VĂN BẢN:
        1. 'node': {label, group, description}
           - group phải thuộc 1 trong 4 loại sau (Bắt buộc): 
             + 'person' (Tên Nhân vật)
             + 'event' (Sự kiện/Biến cố)
             + 'place' (Bản đồ/Địa danh)
             + 'timeline' (Niên đại/Thời khắc)
           - description: Ghi chú ngắn gọn, súc tích (1-2 câu) về node này trong ngữ cảnh đoạn văn.
        2. 'edge': {source_node_id, target_node_id, relationship}
           - source và target phải là tên label của các node vừa tạo.
           - relationship mô tả hành trình hoặc quan hệ (VD: 'đi đến', 'chỉ đạo', 'tham gia', 'xảy ra lúc').
        
        Output MUST be EXACTLY a valid JSON Array: [ {\"type\": \"node\", \"raw_data\": {...}}, {\"type\": \"edge\", \"raw_data\": {...}} ]
        
        NGỮ CẢNH BẮT BUỘC ĐỂ PHÂN BIỆT ĐỒNG ÂM KHÁC NGHĨA:
        ".($context ?: 'Bối cảnh Kinh Thánh chung').'
        
        NỘI DUNG VĂN BẢN (Hãy duyệt từng dòng):
        '.$text;

        Log::info('Gemini is processing payload length: '.strlen($prompt));

        try {
            $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
            $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$this->apiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if (! $response->successful()) {
                $status = $response->status();
                $errorBody = $response->json();
                $errorMsg = $errorBody['error']['message'] ?? 'Lỗi không xác định (' . $status . ')';
                Log::error("Gemini API Error [{$status}]: {$errorMsg}");

                throw new \Exception("Gemini API Error {$status}: {$errorMsg}");
            }

            $jsonData = $response->json();
            $generatedText = $jsonData['candidates'][0]['content']['parts'][0]['text'] ?? '[]';

            $entities = json_decode($generatedText, true);

            if (! is_array($entities)) {
                return [];
            }

            return $entities;

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: '.$e->getMessage());
            
            // Nếu lỗi là 429 hoặc lỗi Timeout, phải ném ra để Job biết đường Retry
            throw $e;
        }
    }
}
