<?php

namespace Modules\BibleLearning\Repositories;

use App\Models\BlEdge;
use App\Models\BlNode;
use Modules\BibleLearning\Contracts\GraphRepositoryInterface;

class GraphRepository implements GraphRepositoryInterface
{
    public function getNetworkData(): array
    {
        return $this->buildPayload(BlNode::all(), BlEdge::all());
    }

    public function getFilteredData(array $groups): array
    {
        $nodes = BlNode::all();

        if (! empty($groups)) {
            $nodes = $nodes->filter(fn ($n) => in_array($n->group, $groups))->values();
        }

        // Only return edges where BOTH from and to node exist
        $nodeIds = $nodes->pluck('id')->all();
        $edges = BlEdge::whereIn('source_node_id', $nodeIds)
            ->whereIn('target_node_id', $nodeIds)
            ->get();

        return $this->buildPayload($nodes, $edges);
    }

    private function buildPayload($nodes, $edges): array
    {
        $mappedNodes = $nodes->map(function ($node) {
            return [
                'id' => $node->id,
                'label' => $node->label,
                'group' => $node->group,
                'title' => $node->description,
                'order' => $node->id, // Used for Timeline layout ordering
            ];
        });

        $mappedEdges = $edges->map(function ($edge) {
            return [
                'id' => $edge->id,
                'from' => $edge->source_node_id,
                'to' => $edge->target_node_id,
                'label' => $edge->relationship,
                'arrows' => 'to',
            ];
        });

        return [
            'nodes' => $mappedNodes->values()->toArray(),
            'edges' => $mappedEdges->values()->toArray(),
        ];
    }
}
