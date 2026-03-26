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
        $this->apiKey = env('GEMINI_API_KEY', 'AIzaSyBYnWx6PJprTFX6GxWAiQZ8YT8vjcOH1BA'); // Giả lập key default
    }

    public function extract(string $text): array
    {
        $prompt = "Bạn là môt chuyên gia Thần học Cơ Đốc (Tin Lành). Dựa vào văn bản dưới đây, hãy trích xuất các thông tin Đáng Giá và phân loại chúng thành một MẢNG JSON thuần túy (không dùng ```json raw code block). Tối đa 5 đối tượng. Các loại type hợp lệ:
        1. 'flashcard': {question, answer, reference}
        2. 'event': {title, description, era, order_index}
        3. 'quiz': {question, options: {A,B,C,D}, correct_option, explanation, reference}
        4. 'node': {label, group: 'person'|'place'|'event', description}
        5. 'edge': {source_node_id, target_node_id, relationship} (Dành cho edge, source/target có thể là id tạm, nhưng hãy cố map tên label).
        
        Output MUST be EXACTLY a valid JSON Array: [ {\"type\": \"flashcard\", \"title\": \"...\", \"raw_data\": {...}} ]
        
        NỘI DUNG VĂN BẢN:
        " . $text;

        Log::info('Gemini is processing payload length: ' . strlen($prompt));

        try {
            $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if (!$response->successful()) {
                Log::error("Gemini API Error: " . $response->body());
                return [];
            }

            $jsonData = $response->json();
            $generatedText = $jsonData['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
            
            $entities = json_decode($generatedText, true);
            
            if (!is_array($entities)) {
                return [];
            }

            return $entities;
            
        } catch (\Exception $e) {
            Log::error("Gemini API Exception: " . $e->getMessage());
            return [];
        }
    }
}
