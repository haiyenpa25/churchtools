<?php

namespace Database\Seeders;

use App\Models\BlEdge;
use App\Models\BlNode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class B66GraphSeeder extends Seeder
{
    /**
     * Bảng mô phỏng "Max Level": Toàn Trí Kinh Thánh 66 Sách
     * Kiến trúc 3 Cấp: Giao Ước -> Phân Loại Sách -> Quyển Sách.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        BlNode::truncate();
        BlEdge::truncate();
        Schema::enableForeignKeyConstraints();

        $nodes = [];
        $edges = [];

        // 1. CHÓP ĐỈNH (ROOT)
        $nodes[] = ['id' => 1000, 'label' => 'KINH THÁNH', 'group' => 'concept', 'desc' => 'Lời Đức Chúa Trời Hằng Sống. Bao gồm 66 sách.'];

        // 2. TẦNG GIAO ƯỚC (TESTAMENTS)
        $nodes[] = ['id' => 1001, 'label' => 'Cựu Ước', 'group' => 'testament', 'desc' => '39 Sách thời kỳ trước Chúa Giê-xu.'];
        $nodes[] = ['id' => 1002, 'label' => 'Tân Ước', 'group' => 'testament', 'desc' => '27 Sách thời kỳ Chúa Giê-xu và Hội Thánh.'];

        $edges[] = ['from' => 1000, 'to' => 1001, 'label' => 'Phần 1'];
        $edges[] = ['from' => 1000, 'to' => 1002, 'label' => 'Phần 2'];

        // 3. TẦNG PHÂN LOẠI CỰU ƯỚC
        $otCategories = [
            2001 => 'Ngũ Thư',
            2002 => 'Lịch Sử Cựu Ước',
            2003 => 'Thi Ca',
            2004 => 'Tiên Tri Lớn',
            2005 => 'Tiên Tri Nhỏ',
        ];

        foreach ($otCategories as $id => $label) {
            $nodes[] = ['id' => $id, 'label' => $label, 'group' => 'category', 'desc' => "Nhóm sách $label trong Cựu Ước."];
            $edges[] = ['from' => 1001, 'to' => $id, 'label' => 'Bao Gồm'];
        }

        // 4. TẦNG PHÂN LOẠI TÂN ƯỚC
        $ntCategories = [
            3001 => 'Phúc Âm',
            3002 => 'Lịch Sử Tân Ước',
            3003 => 'Thư Tín Phao-lô',
            3004 => 'Thư Tín Chung',
            3005 => 'Mặc Khải',
        ];

        foreach ($ntCategories as $id => $label) {
            $nodes[] = ['id' => $id, 'label' => $label, 'group' => 'category', 'desc' => "Nhóm sách $label trong Tân Ước."];
            $edges[] = ['from' => 1002, 'to' => $id, 'label' => 'Bao Gồm'];
        }

        // 5. DATA SÁCH CỰU ƯỚC (39 QUYỂN)
        $otBooks = [
            // NGŨ THƯ
            ['id' => 1, 'cat' => 2001, 'name' => 'Sáng Thế Ký', 'ref' => '1', 'desc' => 'Khởi nguyên của Cơ Đốc Giáo và Dân Y-sơ-ra-ên.'],
            ['id' => 2, 'cat' => 2001, 'name' => 'Xuất Ê-díp-tô Ký', 'ref' => '2', 'desc' => 'Giải cứu khỏi Ai Cập và ban kinh luật.'],
            ['id' => 3, 'cat' => 2001, 'name' => 'Lê-vi Ký', 'ref' => '3', 'desc' => 'Luật lệ về sự thánh khiết và thầy tế lễ.'],
            ['id' => 4, 'cat' => 2001, 'name' => 'Dân Số Ký', 'ref' => '4', 'desc' => 'Sự trôi dạt trong đồng vắng.'],
            ['id' => 5, 'cat' => 2001, 'name' => 'Phục Truyền', 'ref' => '5', 'desc' => 'Nhắc lại luật pháp trước khi vào Đất Hứa.'],

            // LỊCH SỬ
            ['id' => 6, 'cat' => 2002, 'name' => 'Giô-suê', 'ref' => '6', 'desc' => 'Chiếm hữu Đất Hứa.'],
            ['id' => 7, 'cat' => 2002, 'name' => 'Các Quan Xét', 'ref' => '7', 'desc' => 'Thời kỳ tối tăm của Y-sơ-ra-ên.'],
            ['id' => 8, 'cat' => 2002, 'name' => 'Ru-tơ', 'ref' => '8', 'desc' => 'Cốt truyện về Tình yêu và Sự cứu chuộc.'],
            ['id' => 9, 'cat' => 2002, 'name' => 'I & II Sa-mu-ên', 'ref' => '9', 'desc' => 'Cuộc đời của Đa-vít và Vương quốc Y-sơ-ra-ên.'],
            ['id' => 11, 'cat' => 2002, 'name' => 'I & II Các Vua', 'ref' => '11', 'desc' => 'Vương quốc chia đôi và suy tàn.'],
            ['id' => 15, 'cat' => 2002, 'name' => 'Ê-xơ-ra / Nê-hê-mi', 'ref' => '15', 'desc' => 'Trở về từ chốn lưu đày.'],
            ['id' => 17, 'cat' => 2002, 'name' => 'Ê-xơ-tê', 'ref' => '17', 'desc' => 'Sự bảo toàn kỳ diệu của tuyển dân.'],

            // THI CA
            ['id' => 18, 'cat' => 2003, 'name' => 'Gióp', 'ref' => '18', 'desc' => 'Đau khổ và Sự tể trị của Đức Chúa Trời.'],
            ['id' => 19, 'cat' => 2003, 'name' => 'Thi Thiên', 'ref' => '19', 'desc' => 'Thánh ca ngợi khen và tương giao tâm linh.'],
            ['id' => 20, 'cat' => 2003, 'name' => 'Châm Ngôn', 'ref' => '20', 'desc' => 'Sự khôn ngoan thực tiễn.'],
            ['id' => 21, 'cat' => 2003, 'name' => 'Truyền Đạo', 'ref' => '21', 'desc' => 'Hư không nếu thiếu Chúa.'],
            ['id' => 22, 'cat' => 2003, 'name' => 'Nhã Ca', 'ref' => '22', 'desc' => 'Tình yêu vợ chồng thánh khiết.'],

            // TIÊN TRI LỚN
            ['id' => 23, 'cat' => 2004, 'name' => 'Ê-sai', 'ref' => '23', 'desc' => 'Phúc âm của Cựu Ước (Tiên tri Đấng Mê-si).'],
            ['id' => 24, 'cat' => 2004, 'name' => 'Giê-rê-mi', 'ref' => '24', 'desc' => 'Tiên tri rơi lệ vì Giê-ru-sa-lem.'],
            ['id' => 25, 'cat' => 2004, 'name' => 'Ca Thương', 'ref' => '25', 'desc' => 'Sự khóc than cho Thánh Thành.'],
            ['id' => 26, 'cat' => 2004, 'name' => 'Ê-xê-chi-ên', 'ref' => '26', 'desc' => 'Khải tượng và phục hồi tinh thần.'],
            ['id' => 27, 'cat' => 2004, 'name' => 'Đa-ni-ên', 'ref' => '27', 'desc' => 'Đức Chúa Trời tể trị các Đế quốc.'],

            // TIÊN TRI NHỎ
            ['id' => 28, 'cat' => 2005, 'name' => 'Ô-sê', 'ref' => '28', 'desc' => 'Tình yêu không phai của Chúa.'],
            ['id' => 32, 'cat' => 2005, 'name' => 'Giô-na', 'ref' => '32', 'desc' => 'Ân điển cho dân ngoại (Ni-ni-ve).'],
            ['id' => 39, 'cat' => 2005, 'name' => 'Ma-la-chi', 'ref' => '39', 'desc' => 'Sự chuẩn bị cuối cùng trong Cựu Ước.'],
        ];

        // 6. TÂN ƯỚC (27 QUYỂN)
        $ntBooks = [
            // PHÚC ÂM
            ['id' => 40, 'cat' => 3001, 'name' => 'Ma-thi-ơ', 'ref' => '40', 'desc' => 'Chúa Giê-xu là Vua của dân Do Thái.'],
            ['id' => 41, 'cat' => 3001, 'name' => 'Mác', 'ref' => '41', 'desc' => 'Chúa Giê-xu - Người Tôi Tớ hành động.'],
            ['id' => 42, 'cat' => 3001, 'name' => 'Lu-ca', 'ref' => '42', 'desc' => 'Chúa Giê-xu - Con Người Toàn Hảo.'],
            ['id' => 43, 'cat' => 3001, 'name' => 'Giăng', 'ref' => '43', 'desc' => 'Chúa Giê-xu - Con Đức Chúa Trời.'],

            // LỊCH SỬ TÂN ƯỚC
            ['id' => 44, 'cat' => 3002, 'name' => 'Công Vụ Các Sứ Đồ', 'ref' => '44', 'desc' => 'Sự ra đời và bành trướng của Hội Thánh.'],

            // THƯ TÍN ĐẠI DIỆN PHAO-LÔ
            ['id' => 45, 'cat' => 3003, 'name' => 'Rô-ma', 'ref' => '45', 'desc' => 'Tín lý nền tảng: Xưng Công Bình Bằng Đức Tin.'],
            ['id' => 46, 'cat' => 3003, 'name' => '1 & 2 Cô-rinh-tô', 'ref' => '46', 'desc' => 'Sửa trị và khuyên bảo vấn đề trong Hội Thánh.'],
            ['id' => 48, 'cat' => 3003, 'name' => 'Ga-la-ti', 'ref' => '48', 'desc' => 'Tự do trong Đấng Christ.'],
            ['id' => 49, 'cat' => 3003, 'name' => 'Ê-phê-sô', 'ref' => '49', 'desc' => 'Hội Thánh là thân thể Đấng Christ.'],
            ['id' => 50, 'cat' => 3003, 'name' => 'Phi-líp', 'ref' => '50', 'desc' => 'Thư tín của sự Vui Mừng.'],

            // THƯ TÍN CHUNG
            ['id' => 58, 'cat' => 3004, 'name' => 'Hê-bơ-rơ', 'ref' => '58', 'desc' => 'Sự trổi hơn muôn phần của Chúa Giê-xu.'],
            ['id' => 59, 'cat' => 3004, 'name' => 'Gia-cơ', 'ref' => '59', 'desc' => 'Đức tin hành động.'],
            ['id' => 60, 'cat' => 3004, 'name' => '1 & 2 Phi-e-rơ', 'ref' => '60', 'desc' => 'Đứng vững giữa vòng bắt bớ.'],
            ['id' => 62, 'cat' => 3004, 'name' => '1 Giăng', 'ref' => '62', 'desc' => 'Sự bảo đảm của Sự Tương Giao.'],

            // TIÊN TRI
            ['id' => 66, 'cat' => 3005, 'name' => 'Khải Huyền', 'ref' => '66', 'desc' => 'Sự Tồn Tại Tối Hậu của Đấng Christ.'],
        ];

        // LÀM GIÀU JSON TRƯỚC KHI LƯU
        foreach (array_merge($otBooks, $ntBooks) as $book) {
            $isOT = $book['cat'] < 3000;
            $groupName = $isOT ? 'book_ot' : 'book_nt';
            $url = 'https://kinhthanh.httlvn.org/?v=VI1934&b='.$book['ref'];

            $htmlDesc = "<p><b>Chủ đề:</b> {$book['desc']}</p>";
            $htmlDesc .= "<a href='{$url}' target='_blank' style='display:inline-block; margin-top:8px; padding:6px 12px; background:#2563eb; color:white; border-radius:6px; text-decoration:none; font-size:12px; font-weight:bold;'>📖 Đọc Bản HTTLVN 1934</a>";

            $nodes[] = [
                'id' => $book['id'],
                'label' => $book['name'],
                'group' => $groupName,
                'desc' => $htmlDesc,
                'order' => $book['id'],
            ];

            $edges[] = ['from' => $book['cat'], 'to' => $book['id'], 'label' => 'Quyển'];
        }

        // 8. TẦNG VI MÔ (ZOOM-IN VÀO NHÂN VẬT & ĐỊA DANH CỦA TỪNG SÁCH)
        // - Sáng Thế Ký (id = 1)
        $genNodes = [
            ['id' => 9001, 'label' => 'A-đam', 'group' => 'person', 'desc' => 'Tổ phụ loài người. <b>(Sáng Thế Ký 2)</b>'],
            ['id' => 9002, 'label' => 'Ê-va', 'group' => 'person', 'desc' => 'Người nữ đầu tiên. <b>(Sáng Thế Ký 2)</b>'],
            ['id' => 9003, 'label' => 'Nô-ê', 'group' => 'person', 'desc' => 'Người đóng tàu vượt Cơn Đại Hồng Thủy. <b>(Sáng Thế Ký 6)</b>'],
            ['id' => 9004, 'label' => 'Áp-ra-ham', 'group' => 'person', 'desc' => 'Tổ phụ của Đức Tin. <b>(Sáng Thế Ký 12)</b>'],
            ['id' => 9005, 'label' => 'Vườn Ê-đen', 'group' => 'place', 'desc' => 'Nơi con người chối bỏ Đức Chúa Trời. <b>(Sáng Thế Ký 3)</b>'],
        ];

        foreach ($genNodes as $gn) {
            $nodes[] = [
                'id' => $gn['id'],
                'label' => $gn['label'],
                'group' => $gn['group'],
                'desc' => "<p>{$gn['desc']}</p><a href='https://kinhthanh.httlvn.org/?v=VI1934&b=1' target='_blank' style='display:inline-block; margin-top:8px; padding:6px 12px; background:#14b8a6; color:white; border-radius:6px; text-decoration:none; font-size:12px; font-weight:bold;'>Đọc Dẫn Chứng</a>",
            ];
            $edges[] = ['from' => 1, 'to' => $gn['id'], 'label' => 'Ghi chép về'];
        }

        // Quan hệ nội bộ Sáng Thế Ký
        $edges[] = ['from' => 9001, 'to' => 9002, 'label' => 'Chồng của'];
        $edges[] = ['from' => 9005, 'to' => 9001, 'label' => 'Nơi ở của'];
        $edges[] = ['from' => 9005, 'to' => 9002, 'label' => 'Nơi ở của'];

        // - Xuất Ê-díp-tô Ký (id = 2)
        $exNodes = [
            ['id' => 9011, 'label' => 'Môi-se', 'group' => 'person', 'desc' => 'Vị lãnh tụ Vĩ Đại của Y-sơ-ra-ên. Tác giả Ngũ Thư. <b>(Xuất 3)</b>'],
            ['id' => 9012, 'label' => 'A-rôn', 'group' => 'person', 'desc' => 'Anh trai Môi-se, Thầy Tế Lễ Thượng Phẩm đầu tiên. <b>(Xuất 4)</b>'],
            ['id' => 9013, 'label' => 'Pha-ra-ôn', 'group' => 'person', 'desc' => 'Vua hung bạo của Ai Cập, cứng lòng 10 lần. <b>(Xuất 5)</b>'],
            ['id' => 9014, 'label' => 'Biển Đỏ', 'group' => 'place', 'desc' => 'Nơi Chúa rẽ nước cứu tuyển dân. <b>(Xuất 14)</b>'],
            ['id' => 9015, 'label' => 'Mười Điều Răn', 'group' => 'event', 'desc' => 'Giao Ước Luật Pháp tại núi Si-nai. <b>(Xuất 20)</b>'],
        ];

        foreach ($exNodes as $en) {
            $nodes[] = [
                'id' => $en['id'],
                'label' => $en['label'],
                'group' => $en['group'],
                'desc' => "<p>{$en['desc']}</p><a href='https://kinhthanh.httlvn.org/?v=VI1934&b=2' target='_blank' style='display:inline-block; margin-top:8px; padding:6px 12px; background:#14b8a6; color:white; border-radius:6px; text-decoration:none; font-size:12px; font-weight:bold;'>Đọc Dẫn Chứng</a>",
            ];
            $edges[] = ['from' => 2, 'to' => $en['id'], 'label' => 'Ghi chép về'];
        }

        // Quan hệ nội bộ Xuất Ê
        $edges[] = ['from' => 9011, 'to' => 9012, 'label' => 'Em trai của'];
        $edges[] = ['from' => 9011, 'to' => 9013, 'label' => 'Đối đầu với'];
        $edges[] = ['from' => 9011, 'to' => 9014, 'label' => 'Giơ gậy rẽ nước'];
        $edges[] = ['from' => 9011, 'to' => 9015, 'label' => 'Lãnh nhận Bảng Đá'];

        // ══════════════════════════════════════════════════════
        // 9. TẦNG VI MÔ MỞ RỘNG: 4 SÁCH TRỌNG YẾU
        // ══════════════════════════════════════════════════════

        // ── Đa-ni-ên (id = 27) — OT Tiên Tri
        $danNodes = [
            ['id' => 9021, 'label' => 'Đa-ni-ên', 'group' => 'person', 'desc' => 'Tiên tri trung thành giữa lầu đài Ba-by-lôn. <b>(Đa-ni-ên 1)</b>'],
            ['id' => 9022, 'label' => 'Sia-đơ-rắc', 'group' => 'person', 'desc' => 'Một trong 3 người bạn của Đa-ni-ên không quỳ lạy tượng vàng. <b>(Đa-ni-ên 3)</b>'],
            ['id' => 9023, 'label' => 'Lò Lửa', 'group' => 'event', 'desc' => 'Đức Chúa Trời bảo vệ 3 người trong lò lửa đỏ. <b>(Đa-ni-ên 3:25)</b>'],
            ['id' => 9024, 'label' => 'Hang Sư Tử', 'group' => 'event', 'desc' => 'Đa-ni-ên không bị sư tử hại vì Chúa bảo vệ. <b>(Đa-ni-ên 6)</b>'],
            ['id' => 9025, 'label' => 'Ba-by-lôn', 'group' => 'place', 'desc' => 'Đế quốc vĩ đại nơi tuyển dân bị lưu đày. <b>(Đa-ni-ên 1:1)</b>'],
        ];
        $link27 = 'https://kinhthanh.httlvn.org/?v=VI1934&b=27';
        foreach ($danNodes as $dn) {
            $nodes[] = ['id' => $dn['id'], 'label' => $dn['label'], 'group' => $dn['group'], 'desc' => "<p>{$dn['desc']}</p><a href='{$link27}' target='_blank' style='display:inline-block;margin-top:8px;padding:6px 12px;background:#8b5cf6;color:white;border-radius:6px;text-decoration:none;font-size:12px;font-weight:bold;'>Đọc Đa-ni-ên</a>", 'order' => 27];
            $edges[] = ['from' => 27, 'to' => $dn['id'], 'label' => 'Ghi chép về'];
        }
        $edges[] = ['from' => 9021, 'to' => 9022, 'label' => 'Đồng hành với'];
        $edges[] = ['from' => 9022, 'to' => 9023, 'label' => 'Trải nghiệm'];
        $edges[] = ['from' => 9021, 'to' => 9024, 'label' => 'Trải nghiệm'];
        $edges[] = ['from' => 9021, 'to' => 9025, 'label' => 'Sống tại'];

        // ── Ma-thi-ơ (id = 40) — NT Phúc Âm
        $matNodes = [
            ['id' => 9031, 'label' => 'Chúa Giê-xu', 'group' => 'person', 'desc' => 'Con Đức Chúa Trời, Đấng Cứu Thế muôn đời. <b>(Ma-thi-ơ 1:21)</b>'],
            ['id' => 9032, 'label' => 'Giu-đa Ích-ca-ri-ốt', 'group' => 'person', 'desc' => 'Môn đồ phản bội Chúa với 30 miếng bạc. <b>(Ma-thi-ơ 26:15)</b>'],
            ['id' => 9033, 'label' => 'Bết-lê-hem', 'group' => 'place', 'desc' => 'Thành phố Đa-vít, nơi Chúa Giê-xu giáng sinh. <b>(Ma-thi-ơ 2:1)</b>'],
            ['id' => 9034, 'label' => 'Phép Báp-têm', 'group' => 'event', 'desc' => 'Giê-xu chịu phép báp-têm bởi Giăng Báp-tít. Trời mở ra. <b>(Ma-thi-ơ 3:16)</b>'],
            ['id' => 9035, 'label' => 'Bài Giảng Trên Núi', 'group' => 'event', 'desc' => 'Tám Phúc: Nền tảng đạo đức của Vương Quốc Đức Chúa Trời. <b>(Ma-thi-ơ 5-7)</b>'],
        ];
        $link40 = 'https://kinhthanh.httlvn.org/?v=VI1934&b=40';
        foreach ($matNodes as $mn) {
            $nodes[] = ['id' => $mn['id'], 'label' => $mn['label'], 'group' => $mn['group'], 'desc' => "<p>{$mn['desc']}</p><a href='{$link40}' target='_blank' style='display:inline-block;margin-top:8px;padding:6px 12px;background:#f59e0b;color:white;border-radius:6px;text-decoration:none;font-size:12px;font-weight:bold;'>Đọc Ma-thi-ơ</a>", 'order' => 40];
            $edges[] = ['from' => 40, 'to' => $mn['id'], 'label' => 'Ghi chép về'];
        }
        $edges[] = ['from' => 9031, 'to' => 9033, 'label' => 'Giáng sinh tại'];
        $edges[] = ['from' => 9031, 'to' => 9034, 'label' => 'Trải nghiệm'];
        $edges[] = ['from' => 9031, 'to' => 9035, 'label' => 'Rao giảng'];
        $edges[] = ['from' => 9032, 'to' => 9031, 'label' => 'Phản bội'];
        // Cross-book: Ê-sai tiên tri về Chúa Giê-xu
        $edges[] = ['from' => 23, 'to' => 9031, 'label' => 'Tiên tri về'];

        // ── Giăng (id = 43) — NT Phúc Âm
        $johnNodes = [
            ['id' => 9041, 'label' => 'Ni-cô-đem', 'group' => 'person', 'desc' => 'Người Pha-ri-si đến gặp Chúa ban đêm. Nghe về "sinh lại". <b>(Giăng 3)</b>'],
            ['id' => 9042, 'label' => 'La-xa-rơ', 'group' => 'person', 'desc' => 'Người được Chúa Giê-xu kêu ra khỏi mồ sau 4 ngày. <b>(Giăng 11)</b>'],
            ['id' => 9043, 'label' => 'Ma-ri (chị La-xa-rơ)', 'group' => 'person', 'desc' => 'Người xức dầu thơm cho chân Chúa. <b>(Giăng 12)</b>'],
            ['id' => 9044, 'label' => 'Phép Lạ Tại Ca-na', 'group' => 'event', 'desc' => 'Phép lạ đầu tiên: Nước hóa rượu tại tiệc cưới Ca-na. <b>(Giăng 2:1-11)</b>'],
            ['id' => 9045, 'label' => 'Phòng Tiệc Ly', 'group' => 'place', 'desc' => 'Nơi Chúa Giê-xu lập Tiệc Thánh và rửa chân môn đồ. <b>(Giăng 13)</b>'],
        ];
        $link43 = 'https://kinhthanh.httlvn.org/?v=VI1934&b=43';
        foreach ($johnNodes as $jn) {
            $nodes[] = ['id' => $jn['id'], 'label' => $jn['label'], 'group' => $jn['group'], 'desc' => "<p>{$jn['desc']}</p><a href='{$link43}' target='_blank' style='display:inline-block;margin-top:8px;padding:6px 12px;background:#06b6d4;color:white;border-radius:6px;text-decoration:none;font-size:12px;font-weight:bold;'>Đọc Giăng</a>", 'order' => 43];
            $edges[] = ['from' => 43, 'to' => $jn['id'], 'label' => 'Ghi chép về'];
        }
        $edges[] = ['from' => 9031, 'to' => 9041, 'label' => 'Dạy dỗ'];
        $edges[] = ['from' => 9031, 'to' => 9042, 'label' => 'Phục sinh'];
        $edges[] = ['from' => 9042, 'to' => 9043, 'label' => 'Anh / Chị của'];

        // ── Khải Huyền (id = 66) — NT Tiên Tri
        $revNodes = [
            ['id' => 9051, 'label' => 'Giăng Sứ Đồ', 'group' => 'person', 'desc' => 'Tác giả Khải Huyền, nhận khải tượng trên đảo Bát-mô. <b>(Khải Huyền 1:9)</b>'],
            ['id' => 9052, 'label' => 'Đảo Bát-mô', 'group' => 'place', 'desc' => 'Nơi Giăng bị lưu đày và nhận khải thị cuối cùng. <b>(Khải Huyền 1:9)</b>'],
            ['id' => 9053, 'label' => 'Bảy Con Dấu', 'group' => 'event', 'desc' => 'Các cuộn sách được mở bởi Chiên Con — phán xét cuối cùng. <b>(Khải Huyền 6)</b>'],
            ['id' => 9054, 'label' => 'Giê-ru-sa-lem Mới', 'group' => 'place', 'desc' => 'Thành phố vĩ đại từ trời xuống — đời sống đời đời. <b>(Khải Huyền 21)</b>'],
            ['id' => 9055, 'label' => 'Chiên Con', 'group' => 'concept', 'desc' => 'Hình ảnh Đấng Christ đã bị giết nhưng đang sống — Vua của vua chúa. <b>(Khải Huyền 5:6)</b>'],
        ];
        $link66 = 'https://kinhthanh.httlvn.org/?v=VI1934&b=66';
        foreach ($revNodes as $rn) {
            $nodes[] = ['id' => $rn['id'], 'label' => $rn['label'], 'group' => $rn['group'], 'desc' => "<p>{$rn['desc']}</p><a href='{$link66}' target='_blank' style='display:inline-block;margin-top:8px;padding:6px 12px;background:#dc2626;color:white;border-radius:6px;text-decoration:none;font-size:12px;font-weight:bold;'>Đọc Khải Huyền</a>", 'order' => 66];
            $edges[] = ['from' => 66, 'to' => $rn['id'], 'label' => 'Ghi chép về'];
        }
        $edges[] = ['from' => 9051, 'to' => 9052, 'label' => 'Lưu đày tại'];
        $edges[] = ['from' => 9055, 'to' => 9053, 'label' => 'Mở'];
        $edges[] = ['from' => 9055, 'to' => 9054, 'label' => 'Ngự trị trong'];
        // Cross-book: Chiên Con = Chúa Giê-xu trong Giăng
        $edges[] = ['from' => 9031, 'to' => 9055, 'label' => 'Ứng nghiệm là'];

        // ══════════════════════════════════════════════════════
        // TẦNG VI MÔ MỞ RỘNG KHỔNG LỒ (THEO YÊU CẦU MAX LEVEL)
        // ══════════════════════════════════════════════════════

        $extendedData = [
            // CỰU ƯỚC
            3 => [ // Lê-vi Ký
                ['id' => 9101, 'label' => 'Lễ Vượt Qua', 'group' => 'event', 'desc' => 'Kỷ niệm Chúa cứu Y-sơ-ra-ên khỏi Ai Cập.'],
                ['id' => 9102, 'label' => 'Đền Tạm', 'group' => 'place', 'desc' => 'Nơi ngự của Đức Chúa Trời giữa vòng dân sự.'],
                ['id' => 9103, 'label' => 'Thầy Tế Lễ', 'group' => 'concept', 'desc' => 'Người trung gian giữa Đức Chúa Trời và con người.'],
            ],
            6 => [ // Giô-suê
                ['id' => 9104, 'label' => 'Giô-suê', 'group' => 'person', 'desc' => 'Người kế nhiệm Môi-se, dẫn dân vào Đất Hứa.'],
                ['id' => 9105, 'label' => 'Giê-ri-cô', 'group' => 'place', 'desc' => 'Thành trì kiên cố bị sụp đổ bởi tiếng kèn.'],
                ['id' => 9106, 'label' => 'Sông Giô-đanh', 'group' => 'place', 'desc' => 'Dòng sông rẽ nước cho Y-sơ-ra-ên đi qua.'],
            ],
            7 => [ // Các Quan Xét
                ['id' => 9107, 'label' => 'Sam-sôn', 'group' => 'person', 'desc' => 'Quan xét có sức mạnh phi thường từ tóc.'],
                ['id' => 9108, 'label' => 'Ghê-đê-ôn', 'group' => 'person', 'desc' => 'Quan xét chiến thắng với 300 người.'],
                ['id' => 9109, 'label' => 'Đê-bô-ra', 'group' => 'person', 'desc' => 'Nữ tiên tri và nữ quan xét của Y-sơ-ra-ên.'],
            ],
            8 => [ // Ru-tơ
                ['id' => 9110, 'label' => 'Ru-tơ', 'group' => 'person', 'desc' => 'Người nữ Mô-áp, tổ mẫu của Vua Đa-vít.'],
                ['id' => 9111, 'label' => 'Bô-ô', 'group' => 'person', 'desc' => 'Người chuộc sản nghiệp của Na-ô-mi, cưới Ru-tơ.'],
            ],
            9 => [ // I Sa-mu-ên
                ['id' => 9112, 'label' => 'Sa-mu-ên', 'group' => 'person', 'desc' => 'Tiên tri và quan xét cuối cùng.'],
                ['id' => 9113, 'label' => 'Đa-vít', 'group' => 'person', 'desc' => 'Vị vua vĩ đại nhất của Y-sơ-ra-ên, người vừa lòng Đức Chúa Trời.'],
                ['id' => 9114, 'label' => 'Gô-li-át', 'group' => 'person', 'desc' => 'Tên khổng lồ Phi-li-tin bị Đa-vít đánh bại.'],
            ],
            11 => [ // I Các Vua
                ['id' => 9115, 'label' => 'Sa-lô-môn', 'group' => 'person', 'desc' => 'Vua khôn ngoan nhất, xây dựng Đền Thờ đầu tiên.'],
                ['id' => 9116, 'label' => 'Ê-li', 'group' => 'person', 'desc' => 'Tiên tri quyền năng, được cất lên trời trong xe lửa.'],
                ['id' => 9117, 'label' => 'Đền Thờ', 'group' => 'place', 'desc' => 'Trung tâm thờ phượng tại Giê-ru-sa-lem.'],
            ],
            23 => [ // Ê-sai
                ['id' => 9118, 'label' => 'Ê-sai', 'group' => 'person', 'desc' => 'Tiên tri vĩ đại, báo trước sự giáng sinh của Đấng Mê-si.'],
                ['id' => 9119, 'label' => 'Đấng Mê-si', 'group' => 'concept', 'desc' => 'Đấng Cứu Thế được xức dầu.'],
            ],
            32 => [ // Giô-na
                ['id' => 9120, 'label' => 'Giô-na', 'group' => 'person', 'desc' => 'Tiên tri chạy trốn Chúa, bị cá nuốt 3 ngày.'],
                ['id' => 9121, 'label' => 'Ni-ni-ve', 'group' => 'place', 'desc' => 'Thủ đô A-si-ri, ăn năn khi nghe giảng.'],
            ],

            // TÂN ƯỚC
            42 => [ // Lu-ca
                ['id' => 9122, 'label' => 'Xa-chê', 'group' => 'person', 'desc' => 'Người thu thuế nhỏ thó trèo lên cây sung để gặp Chúa.'],
                ['id' => 9123, 'label' => 'Người Sa-ma-ri Nhân Lành', 'group' => 'concept', 'desc' => 'Câu chuyện thí dụ về tình yêu thương láng giềng.'],
            ],
            44 => [ // Công Vụ
                ['id' => 9124, 'label' => 'Phao-lô', 'group' => 'person', 'desc' => 'Sứ đồ của dân ngoại, tác giả 13 thư tín Tân Ước.'],
                ['id' => 9125, 'label' => 'Lễ Ngũ Tuần', 'group' => 'event', 'desc' => 'Đức Thánh Linh giáng lâm, Hội Thánh đầu tiên được thành lập.'],
                ['id' => 9126, 'label' => 'An-ti-ốt', 'group' => 'place', 'desc' => 'Nơi môn đồ lần đầu tiên được gọi là Cơ-đốc nhân.'],
            ],
            45 => [ // Rô-ma
                ['id' => 9127, 'label' => 'Sự Xưng Công Bình', 'group' => 'concept', 'desc' => 'Con người được kể là vô tội nhờ đức tin nơi Đấng Christ.'],
                ['id' => 9128, 'label' => 'Ân Điển', 'group' => 'concept', 'desc' => 'Sự ban cho không xứng đáng từ Đức Chúa Trời.'],
            ],
            58 => [ // Hê-bơ-rơ
                ['id' => 9129, 'label' => 'Mên-chi-xê-đéc', 'group' => 'person', 'desc' => 'Vua và Thầy Tế Lễ bí ẩn, hình bóng của Đấng Christ.'],
                ['id' => 9130, 'label' => 'Đức Tin', 'group' => 'concept', 'desc' => 'Sự biết chắc vững vàng của những điều mình đương mong đợi.'],
            ],
        ];

        foreach ($extendedData as $bookId => $bookNodes) {
            $linkBook = 'https://kinhthanh.httlvn.org/?v=VI1934&b='.$bookId;
            foreach ($bookNodes as $en) {
                $color = ($bookId < 40) ? '#14b8a6' : '#3b82f6'; // Teal for OT, Blue for NT

                $nodes[] = [
                    'id' => $en['id'],
                    'label' => $en['label'],
                    'group' => $en['group'],
                    'desc' => "<p>{$en['desc']}</p><a href='{$linkBook}' target='_blank' style='display:inline-block;margin-top:8px;padding:6px 12px;background:{$color};color:white;border-radius:6px;text-decoration:none;font-size:12px;font-weight:bold;'>Đọc Dẫn Chứng</a>",
                    'order' => $bookId,
                ];

                $edges[] = ['from' => $bookId, 'to' => $en['id'], 'label' => 'Ghi chép về'];
            }
        }

        // Expanded Cross-book connections
        $edges[] = ['from' => 9031, 'to' => 9124, 'label' => 'Kêu gọi']; // Jesus calls Paul
        $edges[] = ['from' => 9118, 'to' => 9119, 'label' => 'Tiên tri về']; // Isaiah prophesies Messiah
        $edges[] = ['from' => 9119, 'to' => 9031, 'label' => 'Ứng nghiệm là']; // Messiah is Jesus
        $edges[] = ['from' => 9104, 'to' => 9105, 'label' => 'Công chiếm']; // Joshua conquers Jericho
        $edges[] = ['from' => 9110, 'to' => 9111, 'label' => 'Vợ của']; // Ruth is wife of Boaz
        $edges[] = ['from' => 9111, 'to' => 9113, 'label' => 'Tổ tiên của']; // Boaz is ancestor of David
        $edges[] = ['from' => 9113, 'to' => 9114, 'label' => 'Đánh bại']; // David defeats Goliath
        $edges[] = ['from' => 9113, 'to' => 9115, 'label' => 'Cha của']; // David is father of Solomon
        $edges[] = ['from' => 9115, 'to' => 9117, 'label' => 'Xây dựng']; // Solomon builds Temple
        $edges[] = ['from' => 9120, 'to' => 9121, 'label' => 'Giảng đạo tại']; // Jonah preaches at Nineveh

        // ══════════════════════════════════════════════════════
        // 10. CẤY VÀO DB BẰNG ELOQUENT
        // ══════════════════════════════════════════════════════
        foreach ($nodes as $n) {
            BlNode::create([
                'id' => $n['id'],
                'label' => $n['label'],
                'group' => $n['group'],
                'description' => $n['desc'] ?? '',
            ]);
        }

        foreach ($edges as $e) {
            BlEdge::create([
                'source_node_id' => $e['from'],
                'target_node_id' => $e['to'],
                'relationship' => $e['label'],
            ]);
        }
    }
}
