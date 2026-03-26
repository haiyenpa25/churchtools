<?php

namespace Modules\PptLivestream\Repositories;

use App\Models\PptTemplate;
use Illuminate\Database\Eloquent\Collection;
use Modules\PptLivestream\Contracts\TemplateRepositoryInterface;

class TemplateRepository implements TemplateRepositoryInterface
{
    public function findById(int $id): ?PptTemplate
    {
        return PptTemplate::find($id);
    }

    public function getAll(): Collection
    {
        return PptTemplate::with('presets')->get();
    }

    public function create(array $data): PptTemplate
    {
        return PptTemplate::create($data);
    }

    public function update(int $id, array $data): PptTemplate
    {
        $template = PptTemplate::findOrFail($id);
        $template->update($data);

        return $template;
    }

    public function delete(int $id): bool
    {
        return (bool) PptTemplate::destroy($id);
    }
}
