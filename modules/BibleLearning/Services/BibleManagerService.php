<?php

namespace Modules\BibleLearning\Services;

use Modules\BibleLearning\Contracts\BibleManagerRepositoryInterface;

class BibleManagerService
{
    protected BibleManagerRepositoryInterface $repository;

    public function __construct(BibleManagerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllBooks(): array
    {
        return $this->repository->getAllBooks();
    }

    public function getChaptersByBook(int $bookId): array
    {
        return $this->repository->getChaptersByBook($bookId);
    }

    public function getVersesByChapter(int $chapterId): array
    {
        return $this->repository->getVersesByChapter($chapterId);
    }

    public function updateVerseContent(int $verseId, string $content): ?array
    {
        // G-A-E-V Rule: Bão não Logic Format và Validation phải nằm trọn ở tầng Service
        $content = trim($content);
        if (empty($content)) {
            throw new \Exception('Nội dung Câu Kinh Thánh không được để rỗng hoặc chỉ chứa khoảng trắng.');
        }

        return $this->repository->updateVerseContent($verseId, $content);
    }
}
