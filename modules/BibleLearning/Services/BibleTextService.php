<?php

namespace Modules\BibleLearning\Services;

use Illuminate\Support\Facades\Log;

class BibleTextService
{
    /**
     * Base path to bible text files
     * Format: {basePath}/{BookName}_{Chapter}.txt
     */
    protected string $basePath;

    /**
     * Map: Vietnamese/Graph book name → file prefix
     */
    protected array $bookMap = [
        // Cựu Ước
        'Sáng Thế Ký'            => 'Sang_The_Ky',
        'Sang-The-Ky'             => 'Sang_The_Ky',
        'Xuất Ê-díp-tô Ký'       => 'Xuat_Ai_Cap',
        'Lê-vi Ký'                => 'Le_Ky',
        'Dân Số Ký'               => 'Dan_So_Ky',
        'Phục Truyền Luật Lệ Ký' => 'Phuc_Truyen_Luat_Le',
        'Giô-suê'                 => 'Giosue',
        'Các Quan Xét'            => 'Cac_Quan_Xet',
        'Ru-tơ'                   => 'Rut',
        'I Sa-mu-ên'              => 'I_Samuelson',
        'II Sa-mu-ên'             => 'II_Samuelson',
        'I Các Vua'               => 'I_Cac_Vua',
        'II Các Vua'              => 'II_Cac_Vua',
        'I Sử Ký'                 => 'I_Su_Ky',
        'II Sử Ký'                => 'II_Su_Ky',
        'Esdra'                   => 'Esdra',
        'Nê-hê-mi'                => 'Nêhemi',
        'Ê-xơ-tê'                 => 'Esthe',
        'Gióp'                    => 'Giop',
        'Thi Thiên'               => 'Thi_Thien',
        'Châm Ngôn'               => 'Cham_Ngon',
        'Truyền Đạo'              => 'Truyen_Dao',
        'Nhã Ca'                  => 'Nha_Ca',
        'Ca Thương'               => 'Ca_Thuong',
        'Ê-sai'                   => 'Isaia',
        'Giê-rê-mi'               => 'Jeremi',
        'Ê-xê-chi-ên'             => 'Ezekiel',
        'Đa-ni-ên'                => 'Daniel',
        'Hô-sê'                   => 'Osea',
        'Giô-ên'                  => 'Joel',
        'A-mốt'                   => 'Amos',
        'Ô-ba-đia'                => 'Obadia',
        'Giô-na'                  => 'Giona',
        'Mi-chê'                  => 'Michee',
        'Na-hum'                  => 'Nahum',
        'Ha-ba-cúc'               => 'Habacuc',
        'Sô-phô-ni'               => 'Sophonia',
        'A-ghê'                   => 'Agge',
        'Xa-cha-ri'               => 'Zacaria',
        'Ma-la-chi'               => 'Malachi',

        // Tân Ước
        'Ma-thi-ơ'                => 'Mathio',
        'Mác'                     => 'Mac',
        'Lu-ca'                   => 'Luc',
        'Giăng'                   => 'Giang',
        'Công Vụ'                  => 'Cong_Vu',
        'Rô-ma'                   => 'Ro_ma',
        'I Cô-rinh-tô'            => 'I_Co_rintô',
        'II Cô-rinh-tô'           => 'II_Co_rintô',
        'Ga-la-ti'                => 'Gala_ti',
        'Ê-phê-sô'                => 'Êfe_sô',
        'Phi-líp'                 => 'Philíp',
        'Cô-lô-se'                => 'Côlôsê',
        'I Tê-sa-lô-ni-ca'        => 'I_Têsalônica',
        'II Tê-sa-lô-ni-ca'       => 'II_Têsalônica',
        'I Ti-mô-thê'             => 'I_Timôthê',
        'II Ti-mô-thê'            => 'II_Timôthê',
        'Tít'                     => 'Tít',
        'Phi-lê-môn'              => 'Philêmôn',
        'Hê-bơ-rơ'               => 'Hêbơrơ',
        'Gia-cơ'                  => 'Giacơ',
        'I Phi-e-rơ'              => 'I_Pêtrơ',
        'II Phi-e-rơ'             => 'II_Pêtrơ',
        'I Giăng'                 => 'I_Giăng',
        'II Giăng'                => 'II_Giăng',
        'III Giăng'               => 'III_Giăng_1',
        'Giu-đê'                  => 'Giuđe',
        'Khải Huyền'              => 'Khải_Huyền',
    ];

    public function __construct()
    {
        $this->basePath = base_path('trinh-chieu/kinh thanh');
    }

    /**
     * Get all verses of a chapter
     *
     * @return array{ok: bool, title: string, verses: array, book: string, chapter: int}
     */
    public function getChapter(string $bookName, int $chapter): array
    {
        $filePrefix = $this->resolveFilePrefix($bookName);

        if (! $filePrefix) {
            return ['ok' => false, 'error' => "Không tìm thấy sách: {$bookName}"];
        }

        $filePath = $this->basePath . DIRECTORY_SEPARATOR . "{$filePrefix}_{$chapter}.txt";

        if (! file_exists($filePath)) {
            return ['ok' => false, 'error' => "Không tìm thấy chương {$chapter} của sách {$bookName}"];
        }

        $lines  = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $title  = trim($lines[0] ?? "{$bookName} {$chapter}");
        $verses = [];

        foreach (array_slice($lines, 1) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            // Match: {number} {text}
            if (preg_match('/^(\d+)\s+(.+)$/', $line, $m)) {
                $verses[] = ['verse' => (int) $m[1], 'text' => trim($m[2])];
            }
        }

        return [
            'ok'      => true,
            'title'   => $title,
            'book'    => $bookName,
            'chapter' => $chapter,
            'verses'  => $verses,
        ];
    }

    /**
     * Get all available chapters count for a book
     */
    public function getBookChapters(string $bookName): array
    {
        $filePrefix = $this->resolveFilePrefix($bookName);
        if (! $filePrefix) {
            return [];
        }

        $chapters = [];
        $i = 1;
        while (file_exists($this->basePath . DIRECTORY_SEPARATOR . "{$filePrefix}_{$i}.txt")) {
            $chapters[] = $i;
            $i++;
        }
        return $chapters;
    }

    /**
     * Parse a scripture reference like "Ma-thi-ơ 5:3-12" or "Sa 1:1"
     * Returns the chapter and verse range if found
     */
    public function parseReference(string $reference): ?array
    {
        // Pattern: bookName chapter:verse or chapter:verse1-verse2
        if (preg_match('/(.+?)\s+(\d+):(\d+)(?:-(\d+))?/', $reference, $m)) {
            return [
                'book'       => trim($m[1]),
                'chapter'    => (int) $m[2],
                'verseStart' => (int) $m[3],
                'verseEnd'   => isset($m[4]) ? (int) $m[4] : (int) $m[3],
            ];
        }
        return null;
    }

    /**
     * Get available book list (for import format guide)
     */
    public function getBookList(): array
    {
        return array_keys($this->bookMap);
    }

    /**
     * Get import format guide
     * Describes the expected format for bible text files
     */
    public function getImportFormatGuide(): array
    {
        return [
            'format'      => 'TXT files per chapter',
            'naming'      => '{BookPrefix}_{ChapterNumber}.txt',
            'structure'   => [
                'line_1'  => 'Book chapter title (e.g. "Ma-thi-ơ 1")',
                'line_2+' => '{verseNumber} {verse text}',
            ],
            'example'     => [
                'filename'  => 'Mathio_1.txt',
                'content'   => "Ma-thi-ơ 1\n1 Gia phổ Đức Chúa Jêsus Christ...\n2 Áp-ra-ham sanh Y-sác...",
            ],
            'book_prefixes' => $this->bookMap,
            'total_files' => count(glob($this->basePath . DIRECTORY_SEPARATOR . '*.txt') ?: []),
        ];
    }

    /**
     * Resolve book name → file prefix, fuzzy match included
     */
    protected function resolveFilePrefix(string $bookName): ?string
    {
        // Direct match
        if (isset($this->bookMap[$bookName])) {
            return $this->bookMap[$bookName];
        }

        // Case-insensitive fuzzy match
        $bookLower = mb_strtolower($bookName);
        foreach ($this->bookMap as $name => $prefix) {
            if (mb_strtolower($name) === $bookLower) {
                return $prefix;
            }
        }

        // Partial match
        foreach ($this->bookMap as $name => $prefix) {
            if (str_contains(mb_strtolower($name), $bookLower) ||
                str_contains($bookLower, mb_strtolower($name))) {
                return $prefix;
            }
        }

        return null;
    }
}
