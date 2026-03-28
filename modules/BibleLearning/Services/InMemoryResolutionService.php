<?php

namespace Modules\BibleLearning\Services;

class InMemoryResolutionService
{
    private array $nodeMap = [];

    private array $edgeMap = [];

    // Bảng Ánh Xạ Nhân Vật Đa Tầng (Multi-level Contextual Aliasing)
    private array $aliasMap = [
        'global' => [
            'si-môn' => 'Phi-e-rơ',
            'sê-pha' => 'Phi-e-rơ',
            'áp-ram' => 'Áp-ra-ham',
            'sa-rai' => 'Sa-ra',
            'sau-lơ' => 'Phao-lô',
            'nhã-ca' => 'Sa-lô-môn',
        ],
        '01_Sang-the-ky' => [
            'giô-sép' => 'Giô-sép (Con Gia-cốp)',
        ],
        '40_Ma-thi-o' => [
            'giô-sép' => 'Giô-sép (Chồng Ma-ri)',
            'ma-ri' => 'Ma-ri (Mẹ Chúa Giê-xu)',
        ],
    ];

    public function resolveAlias(string $name, string $bookName): string
    {
        $key = mb_strtolower(trim($name), 'UTF-8');

        // 1. Dò tìm Alias đặc thù theo Bối cảnh Sách (Local Context)
        if (isset($this->aliasMap[$bookName][$key])) {
            return $this->aliasMap[$bookName][$key];
        }

        // 2. Dò tìm Alias Toàn cầu (Global Context)
        if (isset($this->aliasMap['global'][$key])) {
            return $this->aliasMap['global'][$key];
        }

        return trim($name);
    }

    public function upsertNode(string $name, string $bookName, string $group, string $desc, array $metadata = []): ?string
    {
        $resolvedName = $this->resolveAlias($name, $bookName);
        $key = mb_strtolower($resolvedName, 'UTF-8');

        if (empty($key)) {
            return null;
        }

        // Tốc độ tìm kiếm O(1) bằng Hash Map (Mảng trong PHP)
        if (! isset($this->nodeMap[$key])) {
            $this->nodeMap[$key] = [
                // Giả lập 1 ID duy nhất để nối Edge sau này
                'id' => md5($key.$group),
                'label' => $resolvedName,
                'group' => $group,
                'description' => $desc,
                'metadata' => [
                    'mentions' => [],
                ],
            ];
        } else {
            // Nối Description nếu cái cũ bị rỗng
            if (empty($this->nodeMap[$key]['description']) && ! empty($desc)) {
                $this->nodeMap[$key]['description'] = $desc;
            }
        }

        // Chồng lấp (Gộp) danh sách các câu Kinh Thánh nhắc tới nhân vật
        if (! empty($metadata['source_verse'])) {
            $this->nodeMap[$key]['metadata']['mentions'][] = $metadata['source_verse'];
            $this->nodeMap[$key]['metadata']['mentions'] = array_unique($this->nodeMap[$key]['metadata']['mentions']);
        }

        return $key;
    }

    public function upsertEdge(string $sourceKey, string $targetKey, string $relationship, array $metadata = [])
    {
        if (! isset($this->nodeMap[$sourceKey]) || ! isset($this->nodeMap[$targetKey])) {
            return; // Tránh nối vào Node vô hình (Ghost Node)
        }

        $sourceNode = $this->nodeMap[$sourceKey];
        $targetNode = $this->nodeMap[$targetKey];

        // Mã băm (Hash) để định danh 1 Mối quan hệ duy nhất trên Đồ thị
        $edgeHash = md5($sourceNode['id'].$targetNode['id'].$relationship);

        if (! isset($this->edgeMap[$edgeHash])) {
            $this->edgeMap[$edgeHash] = [
                'source' => $sourceNode['id'],
                'target' => $targetNode['id'],
                'relationship' => $relationship,
                'metadata' => [
                    'source_verses' => [],
                ],
            ];
        }

        if (! empty($metadata['source_verse'])) {
            $this->edgeMap[$edgeHash]['metadata']['source_verses'][] = $metadata['source_verse'];
            $this->edgeMap[$edgeHash]['metadata']['source_verses'] = array_unique($this->edgeMap[$edgeHash]['metadata']['source_verses']);
        }
    }

    /**
     * Dọn dẹp sạch sẽ RAM O(1) để chuyển sang Sách (Book) mới
     */
    public function flushMemory(): void
    {
        $this->nodeMap = [];
        $this->edgeMap = [];
    }

    /**
     * Đồng bộ hoá Dữ Liệu Cũ vào RAM (Hydrate) để ngăn mất Data Mắt Xích khi bỏ qua (Skip) File.
     */
    public function hydrateMemoryFromJson(string $bookName): void
    {
        $fileName = 'ollama_graph_'.$bookName.'.json';
        $fullPath = database_path('data/bible_dump/'.$fileName);

        if (! file_exists($fullPath)) {
            return;
        }

        $content = file_get_contents($fullPath);
        $data = json_decode($content, true);

        if (! $data || ! isset($data['nodes']) || ! isset($data['edges'])) {
            return;
        }

        // Tái tạo lại NodeMap
        foreach ($data['nodes'] as $node) {
            $key = mb_strtolower($this->resolveAlias($node['label'], $bookName), 'UTF-8');
            $this->nodeMap[$key] = $node;
        }

        // Tái tạo lại EdgeMap
        foreach ($data['edges'] as $edge) {
            $edgeHash = md5($edge['source'].$edge['target'].$edge['relationship']);
            $this->edgeMap[$edgeHash] = $edge;
        }
    }

    public function getNodes(): array
    {
        return array_values($this->nodeMap);
    }

    public function getEdges(): array
    {
        return array_values($this->edgeMap);
    }
}
