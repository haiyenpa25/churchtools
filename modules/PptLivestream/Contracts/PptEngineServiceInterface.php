<?php

namespace Modules\PptLivestream\Contracts;

interface PptEngineServiceInterface
{
    /**
     * Tự động generate Pptx từ payload text.
     */
    public function generateFromPayload(array $payload): void;

    /**
     * Bắn chuỗi JSON blocks và template_id để tạo slide.
     * Cho phép cấu hình ghi đè (overrides) các tọa độ, font chữ.
     */
    public function bulkGenerateFromBlocks(string $templateId, array $blocks, array $overrides = []): string;

    /**
     * Trích xuất chữ từ một file PPTX có sẵn.
     */
    public function extractTextFromPpt(string $filePath): string;

    /**
     * Parse pdf/txt into blocks using python.
     */
    public function parseSermonFile(array $source): array;

    /**
     * Analyze template via python script.
     */
    public function analyzeTemplate(string $fullPath): array;

    /**
     * Generate Sermon PPT.
     */
    public function generateSermon(array $payload): array;
}
