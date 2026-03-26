---
description: Quy tắc giới hạn độ dài file — tự động chia file khi quá dài
---

# File Length Rule — ChurchTool Project

> **QUAN TRỌNG**: Trước mỗi yêu cầu, AI PHẢI đọc rule này và các rule liên quan trong `.agent/workflows/` để thực hiện đúng.

## Quy tắc cứng (Hard Rules) — Giới hạn 300–400 dòng

| Loại file | Giới hạn tối đa | Hành động khi vượt |
|-----------|----------------|---------------------|
| Blade view (`.blade.php`) | **300 lines** | Chia thành partials trong `_partials/` |
| PHP Controller | **300 lines** | Tách method ra Service class |
| PHP Service | **300 lines** | Tách thành multiple services |
| JavaScript (`.js`) | **300 lines** | Tách module riêng theo tính năng |
| Python (`.py`) | **300 lines** | Tách function ra module riêng |
| CSS (`.css`) | **300 lines** | Tách theo component |

## Nguyên tắc chia file

1. **Chia theo tính năng** — mỗi file chỉ làm 1 việc (Single Responsibility)
2. **Không tách tuỳ tiện** — tách theo ranh giới logic tự nhiên (comment, blank line, class/function)
3. **Đặt tên rõ ràng** — `feature_action.blade.php`, `featureService.php`, `feature.helper.js`
4. **Cập nhật import/include** — đảm bảo file cha load đúng các file con

## Cấu trúc Blade (View files)

```
resources/views/
  {feature}/
    index.blade.php          # Main entry, chỉ @include partials (~50 lines)
    _partials/
      head.blade.php         # <head> CSS/JS deps
      step1_*.blade.php      # Các bước / sections
      step2_*.blade.php
      toasts.blade.php       # Notifications
  public/js/{feature}/
    {feature}.js             # Alpine.js / JS logic riêng
```

## Cấu trúc PHP (Controller / Service)

```
Http/Controllers/
  {Feature}Controller.php    # Chỉ route handler (~150 dòng)

Services/
  {Feature}Service.php       # Business logic
  {Feature}ParserService.php # Parser logic (nếu cần)
  {Feature}ExportService.php # Export logic (nếu cần)
```

## Cách áp dụng khi gặp file quá dài

// turbo
1. Đọc file để hiểu cấu trúc: `view_file`  
2. Xác định ranh giới section tự nhiên (comments, blank lines, logic groups)  
3. Tạo partial/module files mới  
4. Thay thế nội dung bằng `@include`, `import`, hoặc `require`  
5. Verify bằng browser hoặc syntax check  

## Workflow trigger tự động

Khi tạo hoặc chỉnh sửa file mà kết quả vượt 300 dòng:
1. **DỪNG** — không commit file dài
2. **Phân tích** — tìm ranh giới chia
3. **Chia ngay** — tạo files con
4. **Xác nhận** — đảm bảo không có broken import

## Ví dụ đã thực hiện

- **`ppt.blade.php`** (604 lines) → chia thành:
  - `ppt/index.blade.php` (~50 lines)  
  - `ppt/_partials/head.blade.php` (~18 lines)  
  - `ppt/_partials/step1_input.blade.php` (~160 lines)  
  - `ppt/_partials/step2_wysiwyg.blade.php` (~120 lines)  
  - `ppt/_partials/toasts.blade.php` (~45 lines)  
  - `public/js/ppt/ppt_generator.js` (~260 lines)

- **`PptController.php`** → đang vượt, cần tách SermonController
