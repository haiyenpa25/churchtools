<?php

namespace Modules\BibleLearning\Contracts;

interface GraphRepositoryInterface
{
    /**
     * Lấy toàn bộ Network Graph (Đỉnh và Cạnh) 
     * Định dạng chuẩn cho thư viện D3.js hoặc Vis-Network
     */
    public function getNetworkData(): array;
}
