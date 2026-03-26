<?php

namespace Modules\BibleLearning\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface
{
    /**
     * Lấy toàn bộ sự kiện sắp xếp theo Order trên Timeline
     */
    public function getTimelineEvents(): Collection;
}
