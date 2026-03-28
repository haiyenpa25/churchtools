<?php

namespace Modules\BibleLearning\Services;

class SlidingWindowService
{
    /**
     * Chia văn bản thành các Chunk (Khối) theo nguyên lý Cửa sổ trượt (Sliding Window).
     * Phù hợp để giữ nguyên Business Context giữa các đợt gọi API.
     *
     * @param  string  $text  Toàn bộ văn bản (VD: 1 chương Kinh Thánh)
     * @param  int  $maxWords  Số lượng từ tối đa mỗi khối (VD: 250 từ)
     * @param  float  $overlapRatio  Tỷ lệ chồng lấp (VD: 0.15 = 15%)
     * @return array Danh sách các đoạn văn bản (Chunks)
     */
    public function chunkText(string $text, int $maxWords = 250, float $overlapRatio = 0.20): array
    {
        // Làm sạch văn bản, chuyển các khoảng trắng thừa thành 1 space
        $text = trim(preg_replace('/\s+/', ' ', $text));

        $words = explode(' ', $text);
        $totalWords = count($words);

        $chunks = [];
        $step = max(1, (int) ($maxWords * (1 - $overlapRatio)));

        for ($i = 0; $i < $totalWords; $i += $step) {
            $chunkWords = array_slice($words, $i, $maxWords);
            $chunkText = implode(' ', $chunkWords);

            if (! empty(trim($chunkText))) {
                $chunks[] = $chunkText;
            }

            // Nếu Chunk hiện tại đã chạm đến cuối văn bản, ngắt vòng lặp
            if ($i + $maxWords >= $totalWords) {
                break;
            }
        }

        return $chunks;
    }
}
