{{-- Dynamic Slide Form by type --}}
<template x-if="activeSlide">
<div>
    {{-- title --}}
    <template x-if="activeSlide.type === 'title'">
        <div class="space-y-2">
            <div><label class="field-label">Tựa Đề Bài Giảng</label><input type="text" x-model="activeSlide.data.title" placeholder="Người Nữ Được Tạo Dựng Bình Đẳng" class="field-input font-semibold"></div>
            <div class="grid grid-cols-2 gap-2">
                <div><label class="field-label">Diễn Giả / MS</label><input type="text" x-model="activeSlide.data.speaker" placeholder="MS. Nguyễn Văn A" class="field-input text-xs"></div>
                <div><label class="field-label">Ngày / Nhóm</label><input type="text" x-model="activeSlide.data.date" placeholder="21/03/2026" class="field-input text-xs"></div>
            </div>
            <div><label class="field-label">Phụ Đề</label><input type="text" x-model="activeSlide.data.subtitle" placeholder="Sáng thế ký 2" class="field-input text-xs"></div>
        </div>
    </template>

    {{-- verse --}}
    <template x-if="activeSlide.type === 'verse'">
        <div class="space-y-2">
            <div><label class="field-label text-emerald-500">Phân Đoạn / Tham chiếu</label><input type="text" x-model="activeSlide.data.ref" placeholder="Giăng 3:16" class="field-input text-emerald-400 font-bold"></div>
            <div><label class="field-label">Nội dung Lời Chúa</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Vì Đức Chúa Trời yêu thương thế gian..." class="field-input italic resize-none"></textarea></div>
            <div><label class="field-label">Bản dịch</label><input type="text" x-model="activeSlide.data.translation" placeholder="Bản Hiệu Đính 2010" class="field-input text-xs text-gray-500"></div>
        </div>
    </template>

    {{-- main_point --}}
    <template x-if="activeSlide.type === 'main_point'">
        <div class="space-y-2">
            <div><label class="field-label text-yellow-500">Số / Thứ tự ý</label><input type="text" x-model="activeSlide.data.number" placeholder="I. / 1. / A." class="field-input text-yellow-500 font-bold text-sm w-24"></div>
            <div><label class="field-label text-yellow-500">Ý Chính Trọng Tâm</label><textarea x-model="activeSlide.data.text" rows="2" placeholder="NGƯỜI NỮ BÌNH ĐẲNG VỚI NGƯỜI NAM" class="field-input font-black text-base uppercase resize-none"></textarea></div>
        </div>
    </template>

    {{-- content --}}
    <template x-if="activeSlide.type === 'content'">
        <div class="space-y-2">
            <div><label class="field-label">Tiêu Đề Nhỏ (tùy chọn)</label><input type="text" x-model="activeSlide.data.title" placeholder="Luận điểm..." class="field-input text-sm font-semibold"></div>
            <div><label class="field-label">Đoạn Văn Bản</label><textarea x-model="activeSlide.data.text" rows="4" placeholder="Nội dung diễn giải chi tiết..." class="field-input resize-none"></textarea></div>
        </div>
    </template>

    {{-- origin --}}
    <template x-if="activeSlide.type === 'origin'">
        <div class="space-y-2">
            <div class="grid grid-cols-2 gap-2">
                <div><label class="field-label text-rose-500">Từ Gốc</label><input type="text" x-model="activeSlide.data.word" placeholder="כנגדו" class="field-input text-rose-400 font-black uppercase text-lg"></div>
                <div><label class="field-label">Phiên Âm</label><input type="text" x-model="activeSlide.data.phonetic" placeholder="ke·neg·dô" class="field-input font-mono text-gray-400"></div>
            </div>
            <div><label class="field-label">Ngôn Ngữ Gốc</label><input type="text" x-model="activeSlide.data.lang" placeholder="Hebrew / Greek / Aramaic" class="field-input text-xs text-gray-500"></div>
            <div><label class="field-label">Định Nghĩa / Giải Nghĩa</label><textarea x-model="activeSlide.data.meaning" rows="2" placeholder="Kẻ giúp đỡ tương xứng..." class="field-input resize-none"></textarea></div>
        </div>
    </template>

    {{-- list --}}
    <template x-if="activeSlide.type === 'list'">
        <div class="space-y-2">
            <div><label class="field-label">Tiêu Đề Danh Sách</label><input type="text" x-model="activeSlide.data.title" placeholder="3 Đặc Điểm..." class="field-input font-bold"></div>
            <div><label class="field-label">Các Mục (mỗi mục 1 dòng)</label><textarea x-model="activeSlide.data.items" rows="4" placeholder="Phẩm chất tĩnh lặng&#10;Yêu thương gia đình&#10;Bạn đồng hành" class="field-input font-mono resize-none text-sm"></textarea></div>
        </div>
    </template>

    {{-- comparison --}}
    <template x-if="activeSlide.type === 'comparison'">
        <div class="space-y-2">
            <div><label class="field-label">Tiêu đề So Sánh</label><input type="text" x-model="activeSlide.data.title" placeholder="So sánh..." class="field-input font-bold"></div>
            <div class="grid grid-cols-2 gap-2">
                <div><label class="field-label text-blue-400">Cột TRÁI (A)</label><textarea x-model="activeSlide.data.left" rows="3" placeholder="Người Nữ&#10;- Được tạo dựng sau&#10;- Từ xương sườn Adam" class="field-input text-sm resize-none"></textarea></div>
                <div><label class="field-label text-amber-400">Cột PHẢI (B)</label><textarea x-model="activeSlide.data.right" rows="3" placeholder="Người Nam&#10;- Được tạo dựng trước&#10;- Từ bụi đất" class="field-input text-sm resize-none"></textarea></div>
            </div>
        </div>
    </template>

    {{-- quote --}}
    <template x-if="activeSlide.type === 'quote'">
        <div class="space-y-2">
            <div><label class="field-label">Trích Dẫn</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="&quot;Sự im lặng là vàng nhưng đôi khi nó cũng là sự hèn nhát&quot;" class="field-input italic resize-none"></textarea></div>
            <div><label class="field-label">Nguồn / Tác Giả</label><input type="text" x-model="activeSlide.data.author" placeholder="C.S. Lewis, Mere Christianity" class="field-input text-xs text-gray-400"></div>
        </div>
    </template>

    {{-- question --}}
    <template x-if="activeSlide.type === 'question'">
        <div><label class="field-label text-cyan-400">Câu Hỏi Suy Ngẫm / Thảo Luận</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Bạn nghĩ điều gì đã thay đổi khi Eva xuất hiện?" class="field-input text-xl font-semibold resize-none"></textarea></div>
    </template>

    {{-- illustration --}}
    <template x-if="activeSlide.type === 'illustration'">
        <div class="space-y-2">
            <div><label class="field-label">Tiêu Đề / Câu Chuyện</label><input type="text" x-model="activeSlide.data.title" placeholder="Câu chuyện minh họa..." class="field-input font-semibold"></div>
            <div><label class="field-label">Nội Dung Kể Chuyện</label><textarea x-model="activeSlide.data.text" rows="4" placeholder="Ngày kia, một người đàn ông..." class="field-input resize-none"></textarea></div>
        </div>
    </template>

    {{-- context --}}
    <template x-if="activeSlide.type === 'context'">
        <div class="space-y-2">
            <div class="grid grid-cols-2 gap-2">
                <div><label class="field-label text-amber-400">Thời Điểm / Năm</label><input type="text" x-model="activeSlide.data.era" placeholder="Khoảng 1400 TC" class="field-input text-xs"></div>
                <div><label class="field-label">Địa Điểm</label><input type="text" x-model="activeSlide.data.place" placeholder="Vườn Ê-đen" class="field-input text-xs"></div>
            </div>
            <div><label class="field-label">Bối Cảnh Lịch Sử / Văn Hóa</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Trong nền văn hóa cổ đại, người phụ nữ..." class="field-input resize-none"></textarea></div>
        </div>
    </template>

    {{-- application --}}
    <template x-if="activeSlide.type === 'application'">
        <div class="space-y-2">
            <div><label class="field-label text-green-400">Áp Dụng Thực Tiễn Hôm Nay</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Trong cuộc sống gia đình hiện đại..." class="field-input resize-none"></textarea></div>
            <div><label class="field-label">Hành Động Cụ Thể</label><textarea x-model="activeSlide.data.action" rows="2" placeholder="Tuần này hãy thể hiện sự tôn trọng với..." class="field-input resize-none text-green-400 font-semibold"></textarea></div>
        </div>
    </template>

    {{-- conclusion --}}
    <template x-if="activeSlide.type === 'conclusion'">
        <div class="space-y-2">
            <div><label class="field-label">Tóm Tắt / Kết Luận</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Hôm nay chúng ta đã thấy rằng..." class="field-input resize-none"></textarea></div>
        </div>
    </template>

    {{-- prayer --}}
    <template x-if="activeSlide.type === 'prayer'">
        <div><label class="field-label text-indigo-400">Lời Cầu Nguyện</label><textarea x-model="activeSlide.data.text" rows="4" placeholder="Lạy Chúa, xin giúp chúng con..." class="field-input italic resize-none"></textarea></div>
    </template>

    {{-- invitation --}}
    <template x-if="activeSlide.type === 'invitation'">
        <div class="space-y-2">
            <div><label class="field-label text-red-400">Lời Mời Gọi / Kêu Gọi Quyết Định</label><textarea x-model="activeSlide.data.text" rows="2" placeholder="Nếu bạn muốn tiếp nhận Chúa..." class="field-input font-bold resize-none"></textarea></div>
            <div><label class="field-label">Hướng Dẫn Phản Hồi</label><textarea x-model="activeSlide.data.guide" rows="2" placeholder="Xin cúi đầu cầu nguyện theo..." class="field-input text-sm resize-none"></textarea></div>
        </div>
    </template>

    {{-- announcement --}}
    <template x-if="activeSlide.type === 'announcement'">
        <div class="space-y-2">
            <div><label class="field-label">Thông Báo</label><input type="text" x-model="activeSlide.data.title" placeholder="Họp Hội Thánh Tháng 4" class="field-input font-bold"></div>
            <div><label class="field-label">Chi Tiết</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Ngày giờ, địa điểm..." class="field-input resize-none text-sm"></textarea></div>
        </div>
    </template>

    {{-- song --}}
    <template x-if="activeSlide.type === 'song'">
        <div class="space-y-2">
            <div><label class="field-label text-teal-400">Số Bài / Tên Bài Hát</label><input type="text" x-model="activeSlide.data.name" placeholder="125 - Cúi Xin Vua Thánh Ngự Lai" class="field-input font-bold text-teal-400"></div>
        </div>
    </template>

    {{-- section_break --}}
    <template x-if="activeSlide.type === 'section_break'">
        <div class="space-y-2">
            <div><label class="field-label">Nhãn Phần / Đoạn</label><input type="text" x-model="activeSlide.data.label" placeholder="PHẦN 2 / II." class="field-input font-black text-base uppercase"></div>
            <div><label class="field-label">Mô Tả Phần Này</label><input type="text" x-model="activeSlide.data.subtitle" placeholder="Vai trò của người Nữ..." class="field-input text-sm text-gray-400"></div>
        </div>
    </template>

    {{-- timeline --}}
    <template x-if="activeSlide.type === 'timeline'">
        <div class="space-y-2">
            <div><label class="field-label">Tiêu đề Dòng Thời Gian</label><input type="text" x-model="activeSlide.data.title" placeholder="Hành Trình Phục Hồi..." class="field-input font-bold"></div>
            <div><label class="field-label">Các Mốc (Mỗi mốc 1 dòng, định dạng: Năm | Sự kiện)</label><textarea x-model="activeSlide.data.events" rows="4" placeholder="4004 TC | Sự sa ngã&#10;33 SC | Jesus phục sinh&#10;2000 SC | Hôm nay" class="field-input font-mono text-xs resize-none"></textarea></div>
        </div>
    </template>

    {{-- image --}}
    <template x-if="activeSlide.type === 'image'">
        <div class="space-y-2">
            <div><label class="field-label">Mô Tả Hình Ảnh</label><input type="text" x-model="activeSlide.data.caption" placeholder="Hình minh hoạ: Hoa vườn Ê-đen" class="field-input"></div>
            <div><label class="field-label text-gray-500">Ghi Chú Hiển Thị (Chèn khi có file hình)</label><input type="text" x-model="activeSlide.data.note" placeholder="[Ảnh sẽ hiển thị khi xuất PPTX]" class="field-input text-xs text-gray-500 italic"></div>
        </div>
    </template>

    {{-- memory_verse --}}
    <template x-if="activeSlide.type === 'memory_verse'">
        <div class="space-y-2">
            <div><label class="field-label text-purple-400">Câu Gốc Ghi Nhớ</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Câu Kinh Thánh cần ghi nhớ trong tuần..." class="field-input italic resize-none text-purple-200"></textarea></div>
            <div><label class="field-label">Phân Đoạn</label><input type="text" x-model="activeSlide.data.ref" placeholder="Giăng 3:16" class="field-input text-purple-400 font-bold"></div>
        </div>
    </template>

    {{-- reflection --}}
    <template x-if="activeSlide.type === 'reflection'">
        <div><label class="field-label">Điểm Suy Ngẫm / Tự Kiểm Tra</label><textarea x-model="activeSlide.data.text" rows="4" placeholder="Hỏi chính mình: Tôi có đang...?" class="field-input resize-none"></textarea></div>
    </template>

    {{-- definition --}}
    <template x-if="activeSlide.type === 'definition'">
        <div class="space-y-2">
            <div><label class="field-label text-cyan-400">Thuật Ngữ / Từ Định Nghĩa</label><input type="text" x-model="activeSlide.data.term" placeholder="Bình đẳng / Bổ khuyết" class="field-input font-black text-cyan-400 text-lg uppercase"></div>
            <div><label class="field-label">Định Nghĩa</label><textarea x-model="activeSlide.data.definition" rows="3" placeholder="Bình đẳng theo nghĩa Kinh Thánh là..." class="field-input resize-none"></textarea></div>
        </div>
    </template>

    {{-- testimony --}}
    <template x-if="activeSlide.type === 'testimony'">
        <div class="space-y-2">
            <div><label class="field-label text-orange-400">Nhân Chứng / Câu Chuyện Thực</label><input type="text" x-model="activeSlide.data.name" placeholder="Chị Nguyễn Thị B" class="field-input text-orange-400 font-bold"></div>
            <div><label class="field-label">Nội Dung Chứng Đạo</label><textarea x-model="activeSlide.data.text" rows="3" placeholder="Trước kia tôi..." class="field-input italic resize-none"></textarea></div>
        </div>
    </template>

    {{-- map --}}
    <template x-if="activeSlide.type === 'map'">
        <div class="space-y-2">
            <div><label class="field-label">Tiêu Đề Bản Đồ</label><input type="text" x-model="activeSlide.data.title" placeholder="Hành Trình Dân Israel" class="field-input font-bold"></div>
            <div><label class="field-label">Mô Tả Địa Lý / Khu Vực</label><textarea x-model="activeSlide.data.text" rows="2" placeholder="Từ Ai Cập đến đất Hứa..." class="field-input resize-none text-sm"></textarea></div>
        </div>
    </template>

    {{-- blank --}}
    <template x-if="activeSlide.type === 'blank'">
        <div class="text-xs text-gray-500 italic p-2 bg-gray-800/40 rounded-lg">Slide trắng – Dùng để phân tách hoặc làm nền cho phần khác.</div>
    </template>

    {{-- fallback --}}
    <template x-if="!['title','verse','main_point','content','origin','list','comparison','quote','question','illustration','context','application','conclusion','prayer','invitation','announcement','song','section_break','timeline','image','memory_verse','reflection','definition','testimony','map','blank'].includes(activeSlide.type)">
        <div class="text-xs text-gray-500 italic">Loại slide: <span x-text="activeSlide.type"></span></div>
    </template>
</div>
</template>
