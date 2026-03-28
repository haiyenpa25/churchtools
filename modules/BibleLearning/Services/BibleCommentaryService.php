<?php

namespace Modules\BibleLearning\Services;

class BibleCommentaryService
{
    protected array $files;

    /**
     * Map: book name variants → canonical search key (as appears in commentary header)
     */
    protected array $bookHeaderMap = [
        'Sáng Thế Ký' => 'SÁNG THẾ KÝ',
        'Sang-The-Ky' => 'SÁNG THẾ KÝ',
        'Xuất Ê-díp-tô Ký' => 'XUẤT Ê-DÍP-TÔ KÝ',
        'Lê-vi Ký' => 'LÊ-VI KÝ',
        'Dân Số Ký' => 'DÂN SỐ KÝ',
        'Phục Truyền Luật Lệ Ký' => 'PHỤC TRUYỀN',
        'Giô-suê' => 'GIÔ-SUÊ',
        'Các Quan Xét' => 'CÁC QUAN XÉT',
        'Ru-tơ' => 'RU-TƠ',
        'I Sa-mu-ên' => 'I SA-MU-ÊN',
        'II Sa-mu-ên' => 'II SA-MU-ÊN',
        'I Các Vua' => 'I CÁC VUA',
        'II Các Vua' => 'II CÁC VUA',
        'I Sử Ký' => 'I SỬ KÝ',
        'II Sử Ký' => 'II SỬ KÝ',
        'Esdra' => 'ESDRA',
        'Nê-hê-mi' => 'NÊ-HÊ-MI',
        'Gióp' => 'GIÓP',
        'Thi Thiên' => 'THI THIÊN',
        'Châm Ngôn' => 'CHÂM NGÔN',
        'Truyền Đạo' => 'TRUYỀN ĐẠO',
        'Nhã Ca' => 'NHÃ CA',
        'Ca Thương' => 'CA THƯƠNG',
        'Ê-sai' => 'Ê-SAI',
        'Giê-rê-mi' => 'GIÊ-RÊ-MI',
        'Ê-xê-chi-ên' => 'Ê-XÊ-CHI-ÊN',
        'Đa-ni-ên' => 'ĐA-NI-ÊN',
        'Hô-sê' => 'HÔ-SÊ',
        'Giô-ên' => 'GIÔ-ÊN',
        'A-mốt' => 'A-MỐT',
        'Giô-na' => 'GIÔ-NA',
        'Mi-chê' => 'MI-CHÊ',
        'Xa-cha-ri' => 'XA-CHA-RI',
        'Ma-la-chi' => 'MA-LA-CHI',
        'Ma-thi-ơ' => 'MA-THI-Ơ',
        'Mác' => 'MÁC',
        'Lu-ca' => 'LU-CA',
        'Giăng' => 'GIĂNG',
        'Công Vụ' => 'CÔNG VỤ',
        'Rô-ma' => 'RÔ-MA',
        'I Cô-rinh-tô' => 'I CÔ-RINH-TÔ',
        'II Cô-rinh-tô' => 'II CÔ-RINH-TÔ',
        'Ga-la-ti' => 'GA-LA-TI',
        'Ê-phê-sô' => 'Ê-PHÊ-SÔ',
        'Phi-líp' => 'PHI-LÍP',
        'Hê-bơ-rơ' => 'HÊ-BƠ-RƠ',
        'Khải Huyền' => 'KHẢI HUYỀN',
    ];

    public function __construct()
    {
        $basePath = base_path('tai-lieu/kinh-thanh-giai-nghia');
        $this->files = [
            $basePath.DIRECTORY_SEPARATOR.'Giai-nghia-kt1.txt',
            $basePath.DIRECTORY_SEPARATOR.'Giai-nghia-kt2.txt',
            $basePath.DIRECTORY_SEPARATOR.'Giai-nghia-kt3.txt',
        ];
    }

    public function getCommentary(string $bookName): array
    {
        // 1. Tìm sách Kinh Thánh từ DB (Hỗ trợ Like Search siêu tốc)
        $book = \App\Models\BibleBook::where('name', 'LIKE', '%' . $bookName . '%')->first();

        if (!$book) {
            // Thử bằng Regex Mapping nếu bị sai lệch tên gọi
            $mappedName = $this->resolveHeaderKey($bookName);
            if ($mappedName) {
                // Thử tìm lại bằng map
                $book = \App\Models\BibleBook::where('name', 'LIKE', '%' . $mappedName . '%')->first();
            }
        }

        if (!$book) {
            return ['ok' => false, 'error' => "Không tìm thấy sách: {$bookName} trong CSDL"];
        }

        // 2. Lấy toàn bộ các đoạn giải nghĩa của Sách này
        $commentaries = \App\Models\BibleCommentary::where('bible_book_id', $book->id)->orderBy('id')->get();

        if ($commentaries->isEmpty()) {
            return ['ok' => false, 'error' => "Không có giải nghĩa cho sách: {$bookName}"];
        }

        // 3. Nối các nội dung giải nghĩa thành 1 tệp HTML/Text thuần để Frontend đọc
        $content = "";
        foreach ($commentaries as $c) {
            $content .= "\n\n========================================\n";
            $content .= "📖 [" . $c->reference_string . "] - " . $c->title . "\n";
            $content .= "========================================\n\n";
            $content .= $c->content;
        }

        return [
            'ok' => true,
            'book' => $book->name,
            'header' => mb_strtoupper($book->name),
            'content' => trim($content),
            'excerpt' => mb_substr(trim($content), 0, 800) . '...',
            'source' => 'Giải Nghĩa Kinh Thánh Wiersbe (Database Engine)',
            'char_count' => mb_strlen($content),
            // Trả về luôn mảng raw_data gốc nguyên sinh để user gọi API có JSON luôn!
            'raw_json_data' => $commentaries->map(function ($c) {
                return [
                    'id' => $c->id,
                    'reference' => $c->reference_string,
                    'title' => $c->title,
                    'structure' => $c->raw_data,
                ];
            })->toArray()
        ];
    }

    public function getCommentaryPage(string $bookName, int $page = 1, int $pageSize = 3000): array
    {
        $result = $this->getCommentary($bookName);
        if (! $result['ok']) {
            return $result;
        }

        $content = $result['content'];
        $totalChars = mb_strlen($content);
        $totalPages = (int) ceil($totalChars / $pageSize);
        
        // Fix: nếu Page = -1, trả về Toàn bộ Nội Dung thay vì cắt chuỗi!
        if ($page === -1) {
            return array_merge($result, [
                'page' => 1,
                'total_pages' => 1,
                'page_content' => $content,
                'has_more' => false,
            ]);
        }

        $start = ($page - 1) * $pageSize;
        $slice = mb_substr($content, $start, $pageSize);

        return array_merge($result, [
            'page' => $page,
            'total_pages' => $totalPages,
            'page_content' => $slice,
            'has_more' => $page < $totalPages,
        ]);
    }

    public function getImportFormatGuide(): array
    {
        return [
            'status' => 'DEPRECATED',
            'note' => 'Hệ thống đã nâng cấp lên Database. Vui lòng sử dụng Seeder: php artisan db:seed --class=BibleCommentarySeeder',
        ];
    }

    public function listAvailableBooks(): array
    {
        // Lấy danh sách tên sách từ bảng bible_books mà có chứa ít nhất 1 bài giải nghĩa
        $books = \App\Models\BibleBook::whereHas('commentaries')->pluck('name')->toArray();
        return $books;
    }

    protected function resolveHeaderKey(string $bookName): ?string
    {
        if (isset($this->bookHeaderMap[$bookName])) {
            return $this->bookHeaderMap[$bookName];
        }

        $bookLower = mb_strtolower($bookName);
        foreach ($this->bookHeaderMap as $name => $header) {
            if (mb_strtolower($name) === $bookLower) {
                return $header;
            }
        }

        return null;
    }

}
