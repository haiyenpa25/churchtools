# ChurchTool Changelog

Tất cả những thay đổi nổi bật đối với dự án `ChurchTool` sẽ được ghi nhận tại đây theo chuẩn quy tắc AI Workflow.

### [2026-03-23 06:15] - [Tạo Module Mới: Bible Learning RAG]
- **Yêu cầu từ User:** Xây dựng module dùng AI API (Gemini) để chống chế nội dung, phân tích sâu các sách, sự kiện, nhân vật và bản đồ Kinh Thánh với thiết kế Spaced Repetition.
- **AI Model:** Antigravity AI
- **Tác động:**
  - `[+] TẠO MỚI`: 
    - Database Migrations & Models: `bl_subjects`, `bl_entities`, `bl_events`, `bl_entity_events`, `bl_learning_logs`.
    - Seeders & Factories.
    - Cấu trúc Module (`modules/BibleLearning/`): `Contracts`, `Controllers`, `Services`, `Providers`, `Docs/MODULE_MAP.md`.
    - Giao diện Vue 3 Components (ApprovalCenter, FlashcardView, LocationMap, TimelineView).
  - `[*] SỬA ĐỔI`: 
    - `welcome.blade.php`: Tích hợp thẻ Dashboard cho Bible Learning vào khu vực "Công Cụ Đang Hoạt Động".
- **Trạng thái:** Hoàn tất, Backend Database sẵn sàng, Frontend cấu trúc sẵn sàng.

### [2026-03-23 06:22] - [Nâng cấp Hệ Thống AI Workflow Rules]
- **Yêu cầu từ User:** Rà soát toàn bộ dự án, tạo lập chu trình workflow để AI sau này (các Model) luôn đọc hiểu và làm theo một form quy chuẩn (SOP).
- **AI Model:** Antigravity AI
- **Tác động:**
  - `[+] TẠO MỚI`:
    - `.agents/workflows/master_ai_workflow.md`: Quy trình cốt lõi chạy model AI (4 bước bắt buộc).
    - `.agents/workflows/history_all.md`: Quy trình tự động log mọi thứ vào CHANGELOG.
    - `.agents/workflows/create_feature_rule.md`: Quy tắc khi ra lệnh "Tạo tính năng" (Bắt buộc thiết lập UI card vào welcome.blade.php).
    - `.agents/workflows/check_debug.md`: Quy trình tự động debug trước khi giao code.
- **Trạng thái:** Hoàn tất. Mọi rules đã active.
