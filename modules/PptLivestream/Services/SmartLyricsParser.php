<?php

namespace Modules\PptLivestream\Services;

class SmartLyricsParser
{
    /**
     * Parse raw lyrics text into structured formatting blocks for the WYSIWYG Editor.
     * Tích hợp Pipeline Tiền xử lý (Preprocessing) mức độ chuyên nghiệp.
     */
    public function parse(string $rawText, bool $isDualLang = false): array
    {
        $blocks = [];

        // BƯỚC 1: TIỀN XỬ LÝ LÀM SẠCH DỮ LIỆU (DATA CLEANING)
        $cleanText = $this->cleanText($rawText);

        // BƯỚC 2: TÁCH SLIDE DỰA TRÊN DÒNG TRẮNG (EXPLICIT SLIDE BREAKS)
        $slides = explode("\n\n", $cleanText);

        foreach ($slides as $slideText) {
            $slideText = trim($slideText);
            if (empty($slideText)) {
                continue;
            }

            $lines = explode("\n", $slideText);
            $type = 'normal';
            $label = '';

            // Nhận diện cấu trúc bài hát
            $firstLine = trim($lines[0]);
            if (preg_match('/^(câu|verse|v)[\s]*\d*:?/i', $firstLine)) {
                $type = 'verse';
                $label = $firstLine;
                array_shift($lines);
            } elseif (preg_match('/^(điệp khúc|chorus|c)[:]?$/i', $firstLine)) {
                $type = 'chorus';
                $label = $firstLine;
                array_shift($lines);
            } elseif (preg_match('/^(bridge|cầu nối|b)[:]?$/i', $firstLine)) {
                $type = 'bridge';
                $label = $firstLine;
                array_shift($lines);
            }

            // BƯỚC 3: SMART WORD WRAPPING (Ngắt dòng thông minh khi quá dài)
            $processedLines = [];
            $MAX_CHARS = 55; // Ngưỡng an toàn để câu không bị PPT ép xuống dòng tự do gây bể layout
            foreach ($lines as $line) {
                $l = trim($line);
                if (empty($l)) {
                    continue;
                }

                if (mb_strlen($l, 'UTF-8') > $MAX_CHARS) {
                    $processedLines = array_merge($processedLines, $this->smartSplitLine($l));
                } else {
                    $processedLines[] = $l;
                }
            }

            // BƯỚC 4: GÁN QUY TẮC ĐƠN NGỮ (Tất cả là Primary)
            $parsedLines = [];
            foreach ($processedLines as $l) {
                $parsedLines[] = ['primary' => $l];
            }

            // BƯỚC 5: AUTO-PAGINATION (Ngắn thì gom chung để thu nhỏ, dài thì cắt 2 dòng/slide)
            if (! empty($parsedLines)) {
                $MAX_CHARS_PER_SLIDE = 130;

                $totalLines = count($parsedLines);
                $totalChars = 0;
                foreach ($parsedLines as $pl) {
                    $totalChars += mb_strlen($pl['primary'], 'UTF-8');
                }

                // Nếu cả đoạn có từ 3-4 dòng nhưng rất ngắn -> Gom chung 1 slide để PPT thu nhỏ chữ
                if ($totalLines <= 4 && $totalChars <= $MAX_CHARS_PER_SLIDE) {
                    $blocks[] = [
                        'id' => uniqid().'_'.mt_rand(1000, 9999),
                        'type' => $type,
                        'label' => $label,
                        'lines' => $parsedLines,
                    ];
                } else {
                    // Nếu dài quá, buộc phải cắt nghiêm ngặt 2 dòng/slide để chữ được to rõ
                    $chunks = array_chunk($parsedLines, 2);
                    foreach ($chunks as $idx => $chunk) {
                        $blocks[] = [
                            'id' => uniqid().'_'.mt_rand(1000, 9999),
                            'type' => $type,
                            'label' => ($idx === 0) ? $label : '',
                            'lines' => $chunk,
                        ];
                    }
                }
            }
        }

        return $blocks;
    }

    /**
     * Dọn dẹp khoảng trắng, ký tự rác và chuẩn hóa dấu câu tiếng Việt.
     */
    private function cleanText(string $text): string
    {
        // 0. Xoá thẻ <title>...</title> (sẽ được xử lý thành slide tiêu đề riêng sau)
        $text = preg_replace('/<title>.*?<\/title>/si', '', $text);

        // 1. Phá bỏ các ký tự dấu xuống dòng dị biệt của Windows/Mac
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // 2. Xóa ký tự tàng hình (Zero-width space, BOM) gây lỗi font
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);

        // 3. Chuẩn hóa khoảng trắng (Xóa 2-3 khoảng trắng thừa ở giữa câu)
        $text = preg_replace('/[ \t]+/', ' ', $text);

        // 4. Chuẩn hóa dấu câu (Grammar Fix): Chữ  , -> Chữ, | Chữ,Chữ -> Chữ, Chữ
        $text = preg_replace('/\s+([,\.\!\?])/u', '$1', $text);
        $text = preg_replace('/([,\.\!\?])([^\s\n])/u', '$1 $2', $text);

        // 5. Viết hoa chữ cái đầu tiên của tay viết ẩu
        $lines = explode("\n", $text);
        foreach ($lines as &$line) {
            $line = trim($line);
            if (! empty($line)) {
                $firstChar = mb_substr($line, 0, 1, 'UTF-8');
                $rest = mb_substr($line, 1, null, 'UTF-8');
                $line = mb_strtoupper($firstChar, 'UTF-8').$rest;
            }
        }

        // Re-join, giữ nguyên các ngắt đoạn (Dòng trắng)
        return implode("\n", $lines);
    }

    /**
     * Thuật toán bẻ 1 câu siêu dài thành 2 câu ở vị trí dấu phẩy/khoảng trắng hợp lý nhất
     * Đảm bảo KHÔNG ngắt chữ giữa chừng, và ưu tiên ngắt theo ý.
     */
    private function smartSplitLine(string $line): array
    {
        $len = mb_strlen($line, 'UTF-8');
        $midPoint = (int) ($len / 2);
        $bestBreakIndex = -1;

        // Ưu tiên 1: Dấu câu gần giữa câu nhất (. , ; ! ?)
        $minDist = $len;
        $punctuations = [',', '.', ';', '!', '?'];
        for ($i = 0; $i < $len - 1; $i++) {
            $char = mb_substr($line, $i, 1, 'UTF-8');
            $nextChar = mb_substr($line, $i + 1, 1, 'UTF-8');
            if (in_array($char, $punctuations) && $nextChar === ' ') {
                $dist = abs($i - $midPoint);
                if ($dist < $minDist && $dist < ($len * 0.35)) { // Chấp nhận sai số 35% từ giữa câu
                    $minDist = $dist;
                    $bestBreakIndex = $i + 1; // Cắt ngay sau dấu phẩy
                }
            }
        }

        // Ưu tiên 2: Khoảng trắng tự nhiên gần giữa câu nhất
        if ($bestBreakIndex === -1) {
            $minDist = $len;
            for ($i = 0; $i < $len; $i++) {
                $char = mb_substr($line, $i, 1, 'UTF-8');
                if ($char === ' ') {
                    $dist = abs($i - $midPoint);
                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $bestBreakIndex = $i; // Cắt ngay tại khoảng trắng
                    }
                }
            }
        }

        if ($bestBreakIndex !== -1 && $bestBreakIndex > 0) {
            $p1 = trim(mb_substr($line, 0, $bestBreakIndex, 'UTF-8'));
            $p2 = trim(mb_substr($line, $bestBreakIndex, null, 'UTF-8'));

            // Đảm bảo phần 2 được viết hoa chữ cái đầu gọn gàng
            if (! empty($p2)) {
                $p2 = mb_strtoupper(mb_substr($p2, 0, 1, 'UTF-8'), 'UTF-8').mb_substr($p2, 1, null, 'UTF-8');
            }

            return [$p1, $p2];
        }

        return [$line];
    }
}
