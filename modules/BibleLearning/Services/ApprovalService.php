<?php

namespace Modules\BibleLearning\Services;

use Exception;
use InvalidArgumentException;
use Modules\BibleLearning\Contracts\ApprovalRepositoryInterface;

class ApprovalService
{
    protected ApprovalRepositoryInterface $repository;

    public function __construct(ApprovalRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get a formatted list of pending items for the Vue frontend.
     */
    public function getPendingItemsForVue(): array
    {
        return $this->repository->getPendingItems()->toArray();
    }

    /**
     * Approve a temporary AI extraction.
     *
     * @throws Exception
     */
    public function approveItem(int $id): bool
    {
        $entity = $this->repository->findById($id);
        if (! $entity) {
            throw new InvalidArgumentException('Entity not found.');
        }

        // Logic Auto-Move: Chuyển dữ liệu Flashcard nháp sang Database chính thức sau khi duyệt
        if ($entity->type === 'flashcard' && $entity->raw_data) {
            $data = is_string($entity->raw_data) ? json_decode($entity->raw_data, true) : $entity->raw_data;
            \App\Models\BlFlashcard::create([
                'question' => $data['question'] ?? $entity->title,
                'answer' => $data['answer'] ?? $entity->description,
                'reference' => $data['reference'] ?? null,
                'tags' => $data['tags'] ?? [],
                'status' => 'active',
            ]);
        }
        
        // Logic Auto-Move: Chuyển dữ liệu Sự Kiện (Event) vào bảng bl_events
        if ($entity->type === 'event' && $entity->raw_data) {
            $data = is_string($entity->raw_data) ? json_decode($entity->raw_data, true) : $entity->raw_data;
            \App\Models\BlEvent::create([
                'title' => $data['title'] ?? $entity->title,
                'era' => $data['era'] ?? null,
                'description' => $data['description'] ?? $entity->description,
                'image_url' => $data['image_url'] ?? null,
                'order_index' => $data['order_index'] ?? 0,
            ]);
        }

        // Logic Auto-Move: Chuyển Trắc nghiệm (Quiz) vào bảng bl_quizzes
        if ($entity->type === 'quiz' && $entity->raw_data) {
            $data = is_string($entity->raw_data) ? json_decode($entity->raw_data, true) : $entity->raw_data;
            \App\Models\BlQuiz::create([
                'question' => $data['question'] ?? $entity->title,
                'options' => $data['options'] ?? [],
                'correct_option' => $data['correct_option'] ?? 'A',
                'explanation' => $data['explanation'] ?? null,
                'reference' => $data['reference'] ?? null,
            ]);
        }
        
        // Logic Auto-Move: Đỉnh (Node)
        if ($entity->type === 'node' && $entity->raw_data) {
            $data = is_string($entity->raw_data) ? json_decode($entity->raw_data, true) : $entity->raw_data;
            \App\Models\BlNode::create([
                'label' => $data['label'] ?? $entity->title,
                'group' => $data['group'] ?? 'person',
                'description' => $data['description'] ?? $entity->description,
            ]);
        }

        // Logic Auto-Move: Cạnh lưới (Edge)
        if ($entity->type === 'edge' && $entity->raw_data) {
            $data = is_string($entity->raw_data) ? json_decode($entity->raw_data, true) : $entity->raw_data;
            \App\Models\BlEdge::create([
                'source_node_id' => $data['source_node_id'],
                'target_node_id' => $data['target_node_id'],
                'relationship' => $data['relationship'] ?? 'related_to',
            ]);
        }

        $success = $this->repository->updateStatus($id, 'approved');

        return $success;
    }

    /**
     * Reject and delete a temporary AI extraction.
     *
     * @throws Exception
     */
    public function rejectItem(int $id): bool
    {
        $entity = $this->repository->findById($id);
        if (! $entity) {
            throw new InvalidArgumentException('Entity not found.');
        }

        // Update status or forcefully delete
        // In some systems, we keep rejected logs. Here we delete it from temp.
        return $this->repository->delete($id);
    }
}
