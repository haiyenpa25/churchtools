<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaInferenceService
{
    /**
     * Gửi yêu cầu phân tích tới Server Ollama Local
     */
    public function extract(string $text, string $context = '', string $model = 'gemma3:4b'): array
    {
        $prompt = $this->buildPrompt($text, $context);

        try {
            // Timeout lớn (5 phút) vì mô hình Local xử lý trên VRAM 6GB phụ thuộc vào tốc độ Card đồ họa
            $response = Http::timeout(300)->post('http://127.0.0.1:11434/api/generate', [
                'model' => $model, // Model tuỳ chỉnh từ Tham số Command/UI
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

            // Xử lý việc Qwen lười biếng trả về Object đơn lẻ thay vì Mảng
            if (is_array($entities) && isset($entities['type'])) {
                $entities = [$entities]; // Bọc lại cho đúng chuẩn Pipeline
            }

            // Nếu trả về object bọc ngoài như {"entities": [...]}
            if (is_array($entities) && isset($entities['entities'])) {
                $entities = $entities['entities'];
            }

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
        "ĐÂY LÀ QUY TẮC SỐNG CÒN:\n".
        "1. KẾT QUẢ PHẢI LÀ MỘT MẢNG JSON (Bắt đầu bằng [ và kết thúc bằng ]).\n".
        "2. TUYỆT ĐỐI KHÔNG trả về một Object đơn lẻ.\n\n".
        "Ví dụ chuẩn:\n".
        "[\n".
        "  { \"type\": \"node\", \"raw_data\": { \"label\": \"Đức Chúa Trời\", \"group\": \"nhan_vat\", \"description\": \"Đấng Tạo Hóa\" } },\n".
        "  { \"type\": \"node\", \"raw_data\": { \"label\": \"Trời Đất\", \"group\": \"dia_diem\", \"description\": \"Thế giới được tạo dựng\" } },\n".
        "  { \"type\": \"edge\", \"raw_data\": { \"source_node_id\": \"Đức Chúa Trời\", \"target_node_id\": \"Trời Đất\", \"relationship\": \"Dựng nên\" } }\n".
        "]\n\n".
        "Bối cảnh (Context): $context\n\n".
        "VĂN BẢN (Text):\n".
        $text;
    }
}
