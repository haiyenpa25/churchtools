<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Log;

class BibleCommentaryService
{
    protected array $files;

    /**
     * Map: book name variants → canonical search key (as appears in commentary header)
     */
    protected array $bookHeaderMap = [
        'Sáng Thế Ký'            => 'SÁNG THẾ KÝ',
        'Sang-The-Ky'             => 'SÁNG THẾ KÝ',
        'Xuất Ê-díp-tô Ký'       => 'XUẤT Ê-DÍP-TÔ KÝ',
        'Lê-vi Ký'                => 'LÊ-VI KÝ',
        'Dân Số Ký'               => 'DÂN SỐ KÝ',
        'Phục Truyền Luật Lệ Ký' => 'PHỤC TRUYỀN',
        'Giô-suê'                 => 'GIÔ-SUÊ',
        'Các Quan Xét'            => 'CÁC QUAN XÉT',
        'Ru-tơ'                   => 'RU-TƠ',
        'I Sa-mu-ên'              => 'I SA-MU-ÊN',
        'II Sa-mu-ên'             => 'II SA-MU-ÊN',
        'I Các Vua'               => 'I CÁC VUA',
        'II Các Vua'              => 'II CÁC VUA',
        'I Sử Ký'                 => 'I SỬ KÝ',
        'II Sử Ký'                => 'II SỬ KÝ',
        'Esdra'                   => 'ESDRA',
        'Nê-hê-mi'                => 'NÊ-HÊ-MI',
        'Gióp'                    => 'GIÓP',
        'Thi Thiên'               => 'THI THIÊN',
        'Châm Ngôn'               => 'CHÂM NGÔN',
        'Truyền Đạo'              => 'TRUYỀN ĐẠO',
        'Nhã Ca'                  => 'NHÃ CA',
        'Ca Thương'               => 'CA THƯƠNG',
        'Ê-sai'                   => 'Ê-SAI',
        'Giê-rê-mi'               => 'GIÊ-RÊ-MI',
        'Ê-xê-chi-ên'             => 'Ê-XÊ-CHI-ÊN',
        'Đa-ni-ên'                => 'ĐA-NI-ÊN',
        'Hô-sê'                   => 'HÔ-SÊ',
        'Giô-ên'                  => 'GIÔ-ÊN',
        'A-mốt'                   => 'A-MỐT',
        'Giô-na'                  => 'GIÔ-NA',
        'Mi-chê'                  => 'MI-CHÊ',
        'Xa-cha-ri'               => 'XA-CHA-RI',
        'Ma-la-chi'               => 'MA-LA-CHI',
        'Ma-thi-ơ'                => 'MA-THI-Ơ',
        'Mác'                     => 'MÁC',
        'Lu-ca'                   => 'LU-CA',
        'Giăng'                   => 'GIĂNG',
        'Công Vụ'                 => 'CÔNG VỤ',
        'Rô-ma'                   => 'RÔ-MA',
        'I Cô-rinh-tô'            => 'I CÔ-RINH-TÔ',
        'II Cô-rinh-tô'           => 'II CÔ-RINH-TÔ',
        'Ga-la-ti'                => 'GA-LA-TI',
        'Ê-phê-sô'                => 'Ê-PHÊ-SÔ',
        'Phi-líp'                 => 'PHI-LÍP',
        'Hê-bơ-rơ'               => 'HÊ-BƠ-RƠ',
        'Khải Huyền'              => 'KHẢI HUYỀN',
    ];

    public function __construct()
    {
        $basePath    = base_path('tai-lieu/kinh-thanh-giai-nghia');
        $this->files = [
            $basePath . DIRECTORY_SEPARATOR . 'Giai-nghia-kt1.txt',
            $basePath . DIRECTORY_SEPARATOR . 'Giai-nghia-kt2.txt',
            $basePath . DIRECTORY_SEPARATOR . 'Giai-nghia-kt3.txt',
        ];
    }

    /**
     * Get commentary section for a given book name
     *
     * @return array{ok: bool, book: string, header: string, content: string, excerpt: string}
     */
    public function getCommentary(string $bookName): array
    {
        $headerKey = $this->resolveHeaderKey($bookName);

        if (! $headerKey) {
            return ['ok' => false, 'error' => "Không có giải nghĩa cho sách: {$bookName}"];
        }

        foreach ($this->files as $filePath) {
            if (! file_exists($filePath)) {
                continue;
            }

            $result = $this->searchInFile($filePath, $headerKey);
            if ($result !== null) {
                return [
                    'ok'      => true,
                    'book'    => $bookName,
                    'header'  => $headerKey,
                    'content' => $result['content'],
                    'excerpt' => mb_substr($result['content'], 0, 800) . '...',
                    'source'  => 'Giải Nghĩa của Warren W. Wiersbe',
                    'char_count' => mb_strlen($result['content']),
                ];
            }
        }

        return ['ok' => false, 'error' => "Không tìm thấy giải nghĩa cho: {$bookName}"];
    }

    /**
     * Get a paginated portion of commentary (for long books)
     */
    public function getCommentaryPage(string $bookName, int $page = 1, int $pageSize = 3000): array
    {
        $result = $this->getCommentary($bookName);
        if (! $result['ok']) {
            return $result;
        }

        $content     = $result['content'];
        $totalChars  = mb_strlen($content);
        $totalPages  = (int) ceil($totalChars / $pageSize);
        $start       = ($page - 1) * $pageSize;
        $slice       = mb_substr($content, $start, $pageSize);

        return array_merge($result, [
            'page'        => $page,
            'total_pages' => $totalPages,
            'page_content'=> $slice,
            'has_more'    => $page < $totalPages,
        ]);
    }

    /**
     * Get import format guide for commentary files
     */
    public function getImportFormatGuide(): array
    {
        return [
            'format'    => 'Large TXT files (one or multiple)',
            'separator' => '--------------------',
            'structure' => [
                'book_header' => "--------------------\nTÊN SÁCH (UPPERCASE)\nĐược viết bởi: Author\n...",
                'content'     => 'Free-form text paragraphs, scripture refs like (Sa 1:1)',
                'next_book'   => "--------------------\nSÁCH KẾ TIẾP",
            ],
            'example'   => "--------------------\nSÁNG THẾ KÝ\nĐược viết bởi:\nNguyễn Thiên Ý\n...\n",
            'files'     => array_map(fn($f) => basename($f), $this->files),
            'note'      => 'Books are separated by the "--------------------" divider followed by BOOK NAME in uppercase',
        ];
    }

    /**
     * List all books found in commentary files
     */
    public function listAvailableBooks(): array
    {
        $found = [];
        foreach ($this->files as $filePath) {
            if (! file_exists($filePath)) {
                continue;
            }
            $handle = fopen($filePath, 'r');
            if (! $handle) {
                continue;
            }
            $prevLine = '';
            while (($line = fgets($handle)) !== false) {
                $line = rtrim($line);
                if ($prevLine === '--------------------' && ! empty(trim($line))) {
                    $found[] = trim($line);
                }
                $prevLine = $line;
            }
            fclose($handle);
        }
        return array_unique($found);
    }

    /**
     * Search for a book section within a file.
     * Returns ['content' => '...'] or null if not found.
     */
    protected function searchInFile(string $filePath, string $headerKey): ?array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return null;
        }

        $inSection  = false;
        $prevLine   = '';
        $content    = '';
        $headerLower = mb_strtolower($headerKey);

        while (($line = fgets($handle)) !== false) {
            $trimmed = rtrim($line);

            if ($inSection) {
                // Next section starts
                if ($trimmed === '--------------------') {
                    break;
                }
                $content .= $trimmed . "\n";
            } else {
                // Detect section start: "--------------------" followed by book name
                if ($prevLine === '--------------------') {
                    $lineLower = mb_strtolower(trim($trimmed));
                    if ($lineLower === $headerLower ||
                        str_contains($lineLower, $headerLower) ||
                        str_contains($headerLower, $lineLower)) {
                        $inSection = true;
                        $content   = $trimmed . "\n"; // include header
                    }
                }
            }

            $prevLine = $trimmed;
        }

        fclose($handle);

        return $inSection && ! empty(trim($content)) ? ['content' => $content] : null;
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
