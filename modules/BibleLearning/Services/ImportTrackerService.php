<?php

namespace Modules\BibleLearning\Services;

use Modules\BibleLearning\Contracts\ImportTrackerRepositoryInterface;

class ImportTrackerService
{
    protected ImportTrackerRepositoryInterface $trackerRepo;

    public function __construct(ImportTrackerRepositoryInterface $trackerRepo)
    {
        $this->trackerRepo = $trackerRepo;
    }

    /**
     * Kiểm tra xem file đã được nạp và chạy qua AI thành công chưa?
     */
    public function isProcessedAndUnchanged(string $category, string $fileName, string $fileHash): bool
    {
        $record = $this->trackerRepo->findByCategoryAndName($category, $fileName);
        if (! $record) {
            return false;
        }

        // Nếu file xử lý thành công VÀ nội dung file không bị thay đổi (Hash MD5 y hệt)
        if ($record->status === 'completed' && $record->file_hash === $fileHash) {
            return true;
        }

        return false;
    }

    /**
     * Đánh dấu file đang được phân tích bởi máy chủ
     */
    public function markAsProcessing(string $category, string $fileName, string $fileHash, int $totalChunks = 0)
    {
        return $this->trackerRepo->updateOrCreate(
            ['category' => $category, 'file_name' => $fileName],
            [
                'file_hash' => $fileHash,
                'status' => 'processing',
                'total_chunks' => $totalChunks,
                'processed_chunks' => 0,
                'nodes_added' => 0,
                'edges_added' => 0,
                'error_log' => null,
            ]
        );
    }

    /**
     * Tăng biến đếm chunk. Nếu hoàn thành -> Mark Completed
     */
    public function incrementChunk(string $category, string $fileName, int $nodesAdded = 0, int $edgesAdded = 0)
    {
        $record = $this->trackerRepo->findByCategoryAndName($category, $fileName);
        if ($record) {
            $record->processed_chunks += 1;
            $record->nodes_added += $nodesAdded;
            $record->edges_added += $edgesAdded;

            if ($record->processed_chunks >= $record->total_chunks && $record->total_chunks > 0) {
                $record->status = 'completed';
            }
            $record->save();
        }
    }

    /**
     * Ghi nhận nạp thành công
     */
    public function markAsCompleted(string $category, string $fileName, string $fileHash, int $nodesAdded = 0, int $edgesAdded = 0)
    {
        return $this->trackerRepo->updateOrCreate(
            ['category' => $category, 'file_name' => $fileName],
            [
                'file_hash' => $fileHash,
                'status' => 'completed',
                'nodes_added' => $nodesAdded,
                'edges_added' => $edgesAdded,
                'error_log' => null,
            ]
        );
    }

    /**
     * Ghi nhận AI gặp lỗi
     */
    public function markAsFailed(string $category, string $fileName, string $errorMsg)
    {
        return $this->trackerRepo->updateOrCreate(
            ['category' => $category, 'file_name' => $fileName],
            [
                'status' => 'failed',
                'error_log' => $errorMsg,
            ]
        );
    }

    /**
     * Lấy trạng thái của toàn bộ File trong 1 Category
     */
    public function getCategoryStatus(string $category)
    {
        return $this->trackerRepo->getByCategory($category);
    }
}
