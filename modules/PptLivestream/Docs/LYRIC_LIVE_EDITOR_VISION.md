# Tầm Nhìn Phát Triển: LYRIC LIVE-EDITOR cho ChurchTool

Tài liệu này lưu trữ bản thiết kế lõi (Core Blueprint) cho module "Lyric Live-Editor" được nạp vào Antigravity, hướng tới việc phục vụ công tác truyền thông của Hội Thánh Thạnh Mỹ Lợi.

## 1. Phân tích Cú pháp Thông minh (Smart Lyrics Parser)
- **Nhận diện cấu trúc:** Hệ thống tự động tách các khối dựa trên từ khóa: `Câu 1:`, `Câu 2:`, `Điệp khúc:`.
- **Phân loại Layout:**
  - *Câu 1/2:* Dùng font thường, căn lề trái hoặc giữa.
  - *Điệp khúc:* Tự động áp dụng Style đặc biệt (ví dụ: Chữ in nghiêng, hoặc màu chữ khác biệt, bôi đậm) để ban hát dễ nhận diện.
- **Ngắt Slide tự động:** Dựa trên số dòng tối đa thiết lập trong Template (ví dụ: 2-4 dòng cho Lower Third), hệ thống tự bóc tách một "Câu" dài thành nhiều Slide tự động mà không cắt gãy chữ.

## 2. Tính năng Chỉnh sửa Trực diện (Visual WYSIWYG Editor)
- **Giao diện Canvas mô phỏng:** 
  - *Khung hình 16:9:* Một vùng Preview trên Web dùng HTML5/Canvas mô phỏng chính xác tỉ lệ Slide PowerPoint.
  - *Vùng an toàn (Safe Zone):* Hiển thị Grid/Ruler để đảm bảo chữ không dính vào logo góc hoặc dải thông tin mục sư.
- **Kéo thả Tọa độ:** Cho phép kéo chuột vùng Textbox trực tiếp trên giao diện thay vì phải nhập số X, Y thủ công.
- **Bảng điều khiển Real-time:**
  - *Template Gallery:* Trực quan hoá các preset "Chữ trắng nền xanh", "Chữ vàng bóng đổ"...
  - *Typography:* Chỉnh Font (Google Fonts), Size, Line-height, Letter-spacing.
  - *Background Mode:* Chuyển đổi giữa Solid Green, Transparent (PNG Sequence) hoặc Motion Background.

## 3. Thuật toán Xử lý "Chuyên nghiệp" (Professional Logic)
- **Smart Margin & Padding:** Tự động tạo lề an toàn để chữ không bao giờ dính mép.
- **Text Shadow & Outline:** Render bóng đổ (Drop Shadow) và Viền chữ (Stroke/Outline) - tính năng bắt buộc cho Livestream để chống chìm chữ trên nền sáng/tối cục bộ.
- **Hỗ trợ Song ngữ (Dual Language):** Chia 2 layer chữ riêng biệt (Ví dụ: Tiếng Anh to, tiếng Việt nhỏ nghiêng phía dưới) trên cùng một slide.
- **Dấu câu thông minh (Smart Punctuation Cleanup):** Tự động dọn dẹp các dấu phẩy/chấm ở cuối dòng đứt đoạn, giúp văn bản trình chiếu thanh thoát, hiện đại.

## 4. Quy trình vận hành (The Workflow)
1. **Upload/Dán file .txt:** Đưa lời bài hát thô vào hệ thống.
2. **Chọn Template:** Chỉ định chuẩn "Livestream Lower-Third" hoặc "Màn hình LED".
3. **Preview & Chỉnh sửa thủ công:** Review từng slide trên Web. Nút "Tách Slide" (Split) tích hợp sẵn để ngắt dòng chủ động theo ý đồ người đánh đàn.
4. **Render:** Bấm nút, Laravel đẩy cục Data hoàn chỉnh sang Python Engine để nhả file `.pptx`.

## 5. Tầm Nhìn Phục Vụ (Spiritual Vision)
Việc tỉ mỉ trong từng vị trí đặt chữ, font chữ không chỉ mang ý nghĩa kỹ thuật số, mà là mong muốn đem dâng Lễ Vật tốt nhất trong sự thờ phượng.
Mục tiêu tối thượng của **ChurchTool Lyric Live-Editor** là giúp tín hữu dễ dàng hòa lòng ngợi khen Chúa, không bị phân tâm bởi lỗi kỹ thuật, và giải phóng áp lực cho đội ngũ Media của Hội Thánh.
