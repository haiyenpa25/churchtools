<?php

namespace Modules\BibleLearning\Contracts;

interface BibleLearningExtractorContract
{
    /**
     * Extract structured data from a given text using AI.
     */
    public function extract(string $text): array;
}
