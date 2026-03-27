<?php

namespace Modules\BibleLearning\Repositories;

use App\Models\BlImportedFile;
use Modules\BibleLearning\Contracts\ImportTrackerRepositoryInterface;

class ImportTrackerRepository implements ImportTrackerRepositoryInterface
{
    public function findByCategoryAndName(string $category, string $fileName)
    {
        return BlImportedFile::where('category', $category)
            ->where('file_name', $fileName)
            ->first();
    }

    public function updateOrCreate(array $attributes, array $values)
    {
        return BlImportedFile::updateOrCreate($attributes, $values);
    }

    public function getByCategory(string $category)
    {
        return BlImportedFile::where('category', $category)->get();
    }
}
