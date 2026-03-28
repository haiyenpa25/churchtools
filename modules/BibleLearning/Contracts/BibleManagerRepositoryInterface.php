<?php

namespace Modules\BibleLearning\Contracts;

interface BibleManagerRepositoryInterface
{
    /**
     * Lấy toàn bộ danh sách 66 Sách Kinh Thánh
     */
    public function getAllBooks(): array;

    /**
     * Lấy danh sách Chương của một Sách cụ thể
     */
    public function getChaptersByBook(int $bookId): array;

    /**
     * Lấy danh sách Câu của một Chương cụ thể
     */
    public function getVersesByChapter(int $chapterId): array;

    /**
     * Cập nhật nội dung của một Câu Kinh Thánh
     */
    public function updateVerseContent(int $verseId, string $content): ?array;
}
