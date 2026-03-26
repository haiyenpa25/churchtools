<?php

namespace Modules\PptLivestream\DTOs;

class PptGenerationRequestDTO
{
    public function __construct(
        public readonly ?string $outputPath,
        public readonly ?string $templatePath,
        public readonly array $slides,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            outputPath: $data['output_path'] ?? null,
            templatePath: $data['template_path'] ?? null,
            slides: $data['slides'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'output_path' => $this->outputPath,
            'template_path' => $this->templatePath,
            'slides' => $this->slides,
        ];
    }
}
