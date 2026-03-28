# 🗺️ BẢN ĐỒ KIẾN TRÚC: MODULE KNOWLEDGE GRAPH

Tài liệu Grapuco Tracking này dùng để theo dõi sự phụ thuộc của hệ thống (Blast Radius / Impact Zone). Bất kỳ Kỹ sư/AI nào chạm vào Module này bắt buộc phải đọc kỹ trước khi bắt đầu.

## 1. Local NLP Pipeline (Data Engineering Model)
Mô tả: Hệ thống nạp dữ liệu tốc độ cao tại Local (Máy tính cá nhân), tận dụng VRAM và CPU cục bộ kết hợp với Ollama. Tránh thắt cổ chai vòng lặp I/O MySQL.

```json
{
  "Module": "Local_NLP_Pipeline",
  "EntryPoint": "App\\Console\\Commands\\RunOllamaPipelineCommand",
  "Trigger": "php artisan bible:ollama-pipeline [--category=kinh-thanh] [--book=xyz] [--file=abc.txt]",
  "ExecutionFlow": [
    "1. Lắng nghe tham số mảng Arrays/Checkbox File từ Controller (OllamaController)",
    "2. Đọc file từ storage/app/tai-lieu/kinh-thanh. Cân Hash MD5.",
    "3. Nếu File trùng Hash với DB `bl_imported_files` -> SKIP, nhảy sang Bước 6. (Bởi ImportTrackerService)",
    "4. Gửi đoạn văn cho SlidingWindowService (Cắt đè 250 từ)",
    "5. Gửi tệp cắt cho OllamaInferenceService (Call http://localhost:11434, model Qwen 2.5)",
    "6. Nạp lại Memory cũ (Hydrate) -> Nhận JSON từ Qwen 2.5 và đẩy cho InMemoryResolutionService",
    "7. Dò tìm Alias và Upsert vào mảng Hash Map (RAM)",
    "8. Khi rảnh rỗi (Hết sách), xả RAM thông qua MemoryDumperService (Sinh file JSON Dump)"
  ],
  "ImpactZone": [
    "storage/app/tai-lieu/",
    "database/data/bible_dump/",
    "MySQL: bl_imported_files"
  ],
  "BlastRadius": "Nếu đổi model trong OllamaInferenceService, cấu trúc Prompt có thể bị phá vỡ. Nếu thay đổi Thuật toán Tracker, tính năng Chống trùng lặp sẽ sụp đổ, sinh ra File rác JSON."
}
```

## 2. Server Cloud Pipeline (Web Dashboard Model)
Mô tả: Hệ thống cũ chạy bằng Laravel Queue. Giao tiếp với Cloud Gemini và chạy trực tiếp trên MySQL. Dễ bị hụt hơi trên Shared Hosting.

```json
{
  "Module": "Cloud_Gemini_Pipeline",
  "EntryPoint": "Modules\\BibleLearning\\Http\\Controllers\\GraphController@adminWorkQueue",
  "Trigger": "Nút Vận Hành Hàng Đợi (Web UI)",
  "ExecutionFlow": [
    "1. Job ExtractBibleChunkJob được kéo từ bảng jobs",
    "2. Gọi GeminiExtractionService nạp Gemini API",
    "3. Trả về JSON, insert trực tiếp vào MySQL bl_nodes, bl_edges qua GraphRepository"
  ],
  "ImpactZone": [
    "MySQL: bl_nodes, bl_edges, jobs, bl_imported_files"
  ],
  "BlastRadius": "Phụ thuộc 100% vào mạng kết nối Google. Nếu sửa GeminiExtractionService return rỗng, Job sẽ nuốt lỗi và không retry."
}
```

## 3. Cỗ Máy Quản Lý Kinh Thánh Đa Tầng (Bible Manager App)
Mô tả: Ứng dụng Quản trị Cơ Sở Dữ Liệu Kinh Thánh chuẩn 4-Layer (Controller - Service - Repository - Contract). Không lưu logic xử lý ở Controller. Toàn bộ là Client-Side AlpineJS gọi API REST. Cuối cùng nhét chặt vào file `index.blade.php`.

```json
{
  "Module": "BibleManagerApp",
  "EntryPoint": "Modules\\BibleLearning\\Http\\Controllers\\BibleManagerController",
  "Trigger": "Route: /bible-manager hoặc GET/PUT API /api/verses",
  "ExecutionFlow": [
    "1. Controller (Cổng Web) tiếp nhận Request. Không chọc MySQL.",
    "2. Controller chuyển giao Data thô cho BibleManagerService (Lớp Não Bộ).",
    "3. Service Kiểm tra Validation (Rỗng, Format) rồi vứt Data sang RepositoryInterface.",
    "4. AppServiceProvider trỏ Interface về BibleManagerRepository thực thi lệnh UPDATE SQL cuối cùng."
  ],
  "ImpactZone": [
    "MySQL: bible_books, bible_chapters, bible_verses",
    "Blade: resources/views/manager/index.blade.php"
  ],
  "BlastRadius": "Luồng API REST. Nếu đổi Return Type của BibleManagerRepository, Service sẽ sụp đổ. Controller sẽ văng HTTP Error 500."
}
```
