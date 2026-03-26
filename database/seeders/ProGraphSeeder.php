<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlNode;
use App\Models\BlEdge;

class ProGraphSeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        BlNode::truncate();
        BlEdge::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // TẠO NHỮNG ĐỈNH (NODES) ĐẦU TIÊN THEO SÁNG THẾ KÝ
        $nodes = [
            1 => ['label' => 'Đức Chúa Trời', 'group' => 'concept', 'description' => 'Đấng Tạo Hóa, Ba Ngôi'],
            2 => ['label' => 'Vườn Ê-đen', 'group' => 'place', 'description' => 'Nơi con người đầu tiên sinh sống'],
            3 => ['label' => 'A-đam', 'group' => 'person', 'description' => 'Người đàn ông đầu tiên'],
            4 => ['label' => 'Ê-va', 'group' => 'person', 'description' => 'Người nữ đầu tiên'],
            5 => ['label' => 'Sự Sáng Tạo', 'group' => 'event', 'description' => 'Đức Chúa Trời sáng tạo vạn vật trong 6 ngày'],
            6 => ['label' => 'Ca-in', 'group' => 'person', 'description' => 'Kẻ sát nhân đầu tiên'],
            7 => ['label' => 'A-bên', 'group' => 'person', 'description' => 'Người tử vì đạo đầu tiên'],
            8 => ['label' => 'Sết', 'group' => 'person', 'description' => 'Dòng dõi thánh'],
            9 => ['label' => 'Nô-ê', 'group' => 'person', 'description' => 'Người đóng Tàu tránh đại hồng thủy'],
            10 => ['label' => 'Đại Hồng Thủy', 'group' => 'event', 'description' => 'Sự phán xét thế gian'],
            11 => ['label' => 'Áp-ra-ham', 'group' => 'person', 'description' => 'Tổ phụ đức tin'],
            12 => ['label' => 'Lô', 'group' => 'person', 'description' => 'Cháu trai Áp-ra-ham'],
            13 => ['label' => 'Y-sác', 'group' => 'person', 'description' => 'Làm con thừa kế lời hứa'],
            14 => ['label' => 'Y-ích-ma-ên', 'group' => 'person', 'description' => 'Con của Aga'],
            15 => ['label' => 'Gia-cốp', 'group' => 'person', 'description' => 'Lấy quyền trưởng nam của Ê-sau'],
            16 => ['label' => 'Ê-sau', 'group' => 'person', 'description' => 'Bán trưởng nam lấy canh đậu đỏ'],
        ];

        foreach ($nodes as $id => $node) {
            BlNode::create([
                'id' => $id,
                'label' => $node['label'],
                'group' => $node['group'],
                'description' => $node['description']
            ]);
        }

        // TẠO LƯỚI LIÊN KẾT (EDGES) QUAN HỆ GIỮA CÁC ĐỈNH
        $edges = [
            ['source' => 1, 'target' => 5, 'label' => 'Tiến Hành'],
            ['source' => 5, 'target' => 3, 'label' => 'Tạo Ra'],
            ['source' => 5, 'target' => 4, 'label' => 'Tạo Ra'],
            ['source' => 5, 'target' => 2, 'label' => 'Nơi Chốn'],
            
            ['source' => 3, 'target' => 6, 'label' => 'Sinh ra'],
            ['source' => 3, 'target' => 7, 'label' => 'Sinh ra'],
            ['source' => 4, 'target' => 6, 'label' => 'Sinh ra'],
            ['source' => 4, 'target' => 7, 'label' => 'Sinh ra'],
            
            ['source' => 6, 'target' => 7, 'label' => 'Giết'],
            
            ['source' => 3, 'target' => 8, 'label' => 'Sinh ra (Thay thế)'],
            ['source' => 8, 'target' => 9, 'label' => 'Tổ tiên của'],
            
            ['source' => 1, 'target' => 10, 'label' => 'Giáng Quả'],
            ['source' => 9, 'target' => 10, 'label' => 'Vượt Qua'],
            
            ['source' => 9, 'target' => 11, 'label' => 'Tổ tiên của'],
            ['source' => 11, 'target' => 12, 'label' => 'Bác của'],
            
            ['source' => 11, 'target' => 14, 'label' => 'Sinh ra (với Aga)'],
            ['source' => 11, 'target' => 13, 'label' => 'Sinh ra (với Sa-ra)'],
            
            ['source' => 13, 'target' => 15, 'label' => 'Sinh ra'],
            ['source' => 13, 'target' => 16, 'label' => 'Sinh ra'],
            
            ['source' => 15, 'target' => 16, 'label' => 'Cướp phước lành']
        ];

        foreach ($edges as $edge) {
            BlEdge::create([
                'source_node_id' => $edge['source'],
                'target_node_id' => $edge['target'],
                'relationship' => $edge['label']
            ]);
        }
    }
}
