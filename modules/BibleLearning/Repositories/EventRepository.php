<?php

namespace Modules\BibleLearning\Repositories;

use App\Models\BlEvent;
use Illuminate\Database\Eloquent\Collection;
use Modules\BibleLearning\Contracts\EventRepositoryInterface;

class EventRepository implements EventRepositoryInterface
{
    public function getTimelineEvents(): Collection
    {
        return BlEvent::orderBy('order_index', 'asc')->get();
    }
}
