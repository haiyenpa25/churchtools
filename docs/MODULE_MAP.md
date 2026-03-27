# BIBLE LEARNING MODULE MAP
*(Auto-generated per G-A-E-V Workflow Rule)*

## Knowledge Graph Pipeline

```json
{
  "Module": "BibleLearning",
  "Feature": "Knowledge Graph AI Ingestion Pipeline",
  "EntryPoints": [
    "Command: php artisan bible:ingest"
  ],
  "Core_Jobs": [
    "ExtractBibleChunkJob"
  ],
  "Core_Services": [
    "GeminiExtractionService (AI Call)",
    "EntityResolutionService (Deduplication & Alias Logic)"
  ],
  "Repositories": [
    "GraphRepository"
  ],
  "Impact_Zone": [
    "Tables: bl_nodes, bl_edges, bl_aliases",
    "Data Source: trinh-chieu/kinh thanh/*.txt",
    "External API: Gemini 1.5 Flash"
  ],
  "Description": "Uses Laravel Jobs to batch process 1189 text files. Commands push jobs -> Jobs call Gemini -> Output JSON passed to EntityResolutionService -> Inserted into MySQL via GraphRepository FirstOrCreate.",
  "Status": "Development"
}
```
