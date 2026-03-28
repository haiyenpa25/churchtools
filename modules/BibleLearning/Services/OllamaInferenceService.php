<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaInferenceService
{
    /**
     * Gửi yêu cầu phân tích tới Server Ollama Local
     */
    public function extract(string $text, string $context = ''): array
    {
        $prompt = $this->buildPrompt($text, $context);

        try {
            // Timeout lớn (5 phút) vì mô hình Local xử lý trên VRAM 6GB phụ thuộc vào tốc độ Card đồ họa
            $response = Http::timeout(300)->post('http://127.0.0.1:11434/api/generate', [
                'model' => 'qwen2.5', // Model Qwen 2.5 cực mạnh về tiếng Việt
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json', // Tính năng ép kiểu Output JSON của Ollama
            ]);

            if (! $response->successful()) {
                Log::error('Ollama API Error: '.$response->body());

                return [];
            }

            $jsonData = $response->json();
            $generatedText = $jsonData['response'] ?? '[]';

            $entities = json_decode($generatedText, true);

            if (! is_array($entities)) {
                Log::warning("Ollama returned invalid JSON: $generatedText");

                return [];
            }

            return $entities;
        } catch (\Exception $e) {
            Log::error('Ollama API Exception: '.$e->getMessage());

            return [];
        }
    }

    private function buildPrompt(string $text, string $context): string
    {
        return "Bạn là Kỹ sư Dữ liệu (Data Engineer) và Chuyên gia Thần học.\n".
        "Nhiệm vụ của bạn là trích xuất Thực thể (Nodes) và Mối quan hệ (Edges) từ văn bản dưới đây.\n\n".
        "Quy tắc Đầu ra BẮT BUỘC là ĐÚNG ĐỊNH DẠNG JSON MẢNG (Array of JSON Objects):\n".
        "[\n".
        "  { \"type\": \"node\", \"raw_data\": { \"label\": \"Tên\", \"group\": \"nhan_vat|dia_diem|su_kien|khai_niem\", \"description\": \"Mô tả\" } },\n".
        "  { \"type\": \"edge\", \"raw_data\": { \"source_node_id\": \"Tên 1\", \"target_node_id\": \"Tên 2\", \"relationship\": \"Quan hệ\" } }\n".
        "]\n\n".
        "Bối cảnh (Context): $context\n\n".
        "VĂN BẢN (Text):\n".
        $text;
    }
}
