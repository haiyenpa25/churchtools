<?php

namespace Modules\BibleLearning\Services;

use App\Models\BlNode;
use App\Models\BlEdge;
use Illuminate\Support\Facades\Log;

class EntityResolutionService
{
    /**
     * Đối chiếu và Upsert Nút (Node)
     */
    public function resolveNode(string $name, string $group, ?string $description = null, array $metadata = []): ?BlNode
    {
        // 1. Chuẩn hóa tên (Alias Normalization)
        $normalizedName = $this->normalizeAlias($name);
        
        if (empty($normalizedName)) {
            return null;
        }

        // 2. Upsert vào Database (Entity Resolution lõi)
        // Nếu đã có label + group giống nhau thì không tạo mới, chỉ lấy ID
        $node = BlNode::firstOrCreate(
            ['label' => $normalizedName, 'group' => $group],
            ['description' => $description, 'metadata' => empty($metadata) ? null : $metadata]
        );

        // Nếu node đã tồn tại, ta có thể update metadata nếu cần (tạm thời không ghi đè để bảo toàn data gốc)
        // Nếu muốn update: $node->update(['metadata' => array_merge($node->metadata ?? [], $metadata)]);
        
        return $node;
    }

    /**
     * Đối chiếu và Upsert Cạnh (Edge / Relationship)
     */
    public function createRelationship(BlNode $source, BlNode $target, string $relationship, array $metadata = []): ?BlEdge
    {
        if ($source->id === $target->id) {
            return null; // Không map node vào chính nó
        }

        // Tạo cạnh duy nhất (tránh duplicate 2 node có cùng 1 relationship)
        $edge = BlEdge::firstOrCreate(
            [
                'source_node_id' => $source->id,
                'target_node_id' => $target->id,
                'relationship'   => $relationship
            ],
            [
                'metadata' => empty($metadata) ? null : $metadata
            ]
        );

        return $edge;
    }

    /**
     * Sử dụng config dictionary để map danh xưng
     */
    private function normalizeAlias(string $name): string
    {
        $name = trim($name);
        $lowerName = mb_strtolower($name, 'UTF-8');
        
        // Load từ điển từ config/bible_aliases.php
        $aliases = config('bible_aliases', []);

        if (array_key_exists($lowerName, $aliases)) {
            return $aliases[$lowerName];
        }

        // Viết hoa chữ cái đầu nếu không có trong từ điển
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }
}
