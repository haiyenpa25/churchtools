<?php

namespace Modules\BibleLearning\Repositories;

use App\Models\BibleBook;
use App\Models\BibleChapter;
use App\Models\BibleVerse;
use Modules\BibleLearning\Contracts\BibleManagerRepositoryInterface;

class BibleManagerRepository implements BibleManagerRepositoryInterface
{
    public function getAllBooks(): array
    {
        // G-A-E-V Rule: Sắp xếp chuẩn theo thứ tự Cựu-Tân Ước
        return BibleBook::orderBy('book_number', 'asc')->get()->toArray();
    }

    public function getChaptersByBook(int $bookId): array
    {
        return BibleChapter::where('bible_book_id', $bookId)
            ->orderBy('chapter_number', 'asc')
            ->get()
            ->toArray();
    }

    public function getVersesByChapter(int $chapterId): array
    {
        return BibleVerse::where('bible_chapter_id', $chapterId)
            ->orderBy('verse_number', 'asc')
            ->get()
            ->toArray();
    }

    public function updateVerseContent(int $verseId, string $content): ?array
    {
        $verse = BibleVerse::find($verseId);
        if (! $verse) {
            return null;
        }

        $verse->content = $content;
        $verse->save();

        return $verse->toArray();
    }
}
