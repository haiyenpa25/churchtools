<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GraphParserService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', 'AIzaSyBYnWx6PJprTFX6GxWAiQZ8YT8vjcOH1BA');
    }

    /**
     * Parse free-form theological text into graph nodes + edges
     * Returns array: { nodes: [...], edges: [...], summary: '...' }
     */
    public function parseText(string $rawText): array
    {
        $prompt = $this->buildPrompt($rawText);

        try {
            $response = Http::timeout(30)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key='.$this->apiKey,
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 8192,
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

            if (! $response->successful()) {
                Log::error('[GraphParserService] Gemini error: '.$response->body());

                return $this->errorResponse('Gemini API không phản hồi. Kiểm tra API key.');
            }

            $body = $response->json();
            $rawJson = $body['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $parsed = json_decode($rawJson, true);

            if (! is_array($parsed) || empty($parsed['nodes'])) {
                Log::warning('[GraphParserService] Invalid output: '.$rawJson);

                return $this->errorResponse('AI không thể phân tích văn bản này. Hãy thêm nội dung chi tiết hơn.');
            }

            // Assign temporary IDs (offset 50000+) so no collision with seeder
            $baseId = 50000 + (int) (microtime(true) * 10) % 10000;
            $labelToId = [];

            foreach ($parsed['nodes'] as &$node) {
                $node['id'] = $baseId++;
                $node['group'] = $this->sanitizeGroup($node['group'] ?? 'concept');
                $labelToId[$node['label']] = $node['id'];
            }
            unset($node);

            // Resolve edge IDs from label references
            $resolvedEdges = [];
            foreach (($parsed['edges'] ?? []) as $edge) {
                $from = $labelToId[$edge['from'] ?? ''] ?? null;
                $to = $labelToId[$edge['to'] ?? ''] ?? null;
                if ($from && $to) {
                    $resolvedEdges[] = [
                        'from' => $from,
                        'to' => $to,
                        'label' => $edge['relationship'] ?? $edge['label'] ?? 'liên kết với',
                    ];
                }
            }

            return [
                'ok' => true,
                'nodes' => $parsed['nodes'],
                'edges' => $resolvedEdges,
                'summary' => $parsed['summary'] ?? 'Đã phân tích xong văn bản.',
            ];

        } catch (\Exception $e) {
            Log::error('[GraphParserService] Exception: '.$e->getMessage());

            return $this->errorResponse($e->getMessage());
        }
    }

    private function buildPrompt(string $text): string
    {
        return <<<PROMPT
Bạn là chuyên gia Kinh Thánh và Thần học Tin Lành.

Hãy đọc đoạn văn bản bên dưới và trích xuất ra một đồ thị tri thức (knowledge graph) chuẩn xác.

Trả về JSON OBJECT (không phải array, không có markdown) theo cấu trúc sau:
{
  "summary": "Tóm tắt ngắn gọn nội dung văn bản (1-2 câu tiếng Việt)",
  "nodes": [
    {
      "label": "Tên thực thể (tiếng Việt chuẩn Kinh Thánh)",
      "group": "person|place|event|concept",
      "description": "Mô tả chi tiết (1-3 câu), có trích dẫn KT nếu có"
    }
  ],
  "edges": [
    {
      "from": "Label của node nguồn",
      "to": "Label của node đích",
      "relationship": "Động từ liên kết ngắn gọn (VD: Cha của, Ăn năn tại, Tiên tri về)"
    }
  ]
}

Quy tắc:
- Chỉ trả về JSON thuần túy không có dấu ``` hay từ khóa bọc ngoài
- group hợp lệ: person (nhân vật), place (địa danh), event (sự kiện), concept (khái niệm/giáo lý)
- Tối đa 15 nodes, 20 edges
- Ưu tiên các liên kết có ý nghĩa thần học quan trọng
- Tất cả tên cần chuẩn theo tên trong Kinh Thánh Tiếng Việt 1934 (HTTLVN)

VĂN BẢN ĐẦU VÀO:
{$text}
PROMPT;
    }

    private function sanitizeGroup(string $group): string
    {
        $valid = ['person', 'place', 'event', 'concept', 'book_ot', 'book_nt'];

        return in_array($group, $valid) ? $group : 'concept';
    }

    private function errorResponse(string $message): array
    {
        return ['ok' => false, 'error' => $message, 'nodes' => [], 'edges' => []];
    }
}
