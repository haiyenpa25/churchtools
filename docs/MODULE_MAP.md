# 🗺️ BẢN ĐỒ KIẾN TRÚC: MODULE KNOWLEDGE GRAPH

Tài liệu Grapuco Tracking này dùng để theo dõi sự phụ thuộc của hệ thống (Blast Radius / Impact Zone). Bất kỳ Kỹ sư/AI nào chạm vào Module này bắt buộc phải đọc kỹ trước khi bắt đầu.

## 1. Local NLP Pipeline (Data Engineering Model)
Mô tả: Hệ thống nạp dữ liệu tốc độ cao tại Local (Máy tính cá nhân), tận dụng VRAM và CPU cục bộ kết hợp với Ollama. Tránh thắt cổ chai vòng lặp I/O MySQL.

```json
{
  "Module": "Local_NLP_Pipeline",
  "EntryPoint": "App\\Console\\Commands\\RunOllamaPipelineCommand",
  "Trigger": "php artisan bible:ollama-pipeline",
  "ExecutionFlow": [
    "1. Đọc file từ storage/app/tai-lieu/kinh-thanh",
    "2. Gửi đoạn văn cho SlidingWindowService (Cắt đè 250 từ)",
    "3. Gửi tệp cắt cho OllamaInferenceService (Call http://localhost:11434)",
    "4. Nhận JSON từ Qwen 2.5 và đẩy cho InMemoryResolutionService",
    "5. Dò tìm Alias và Upsert vào mảng Hash Map (RAM)",
    "6. Khi rảnh rỗi (Hết sách), xả RAM thông qua MemoryDumperService"
  ],
  "ImpactZone": [
    "storage/app/tai-lieu/",
    "database/data/bible_dump/"
  ],
  "BlastRadius": "Nếu đổi model trong OllamaInferenceService, cấu trúc Prompt có thể bị phá vỡ. Nếu thay đổi InMemoryResolutionService, Aliasing có thể bị trật nhịp."
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
