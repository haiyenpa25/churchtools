# Module Map: PptLivestream

This file documents the dependency graph and architectural linkages for the `PptLivestream` module.

## Feature: Sermon_Template_Architecture

```json
{
  "feature": "Sermon_Template_Architecture",
  "entry_point": [
    "Modules\\PptLivestream\\Http\\Controllers\\PptController@sermonAnalyzeTemplate",
    "Modules\\PptLivestream\\Http\\Controllers\\PptController@sermonGenerate"
  ],
  "linkages": [
    {
      "file": "modules/PptLivestream/Http/Controllers/PptController.php",
      "depends_on": [
        "Modules\\PptLivestream\\Contracts\\PptEngineServiceInterface",
        "Modules\\PptLivestream\\Contracts\\TemplateRepositoryInterface"
      ]
    },
    {
      "file": "modules/PptLivestream/Services/PptEngineService.php",
      "functions": ["analyzeTemplate", "generateSermon", "parseSermonFile"],
      "executes_python": ["template_analyzer.py", "sermon_generator.py", "sermon_parser.py"]
    },
    {
      "file": "modules/PptLivestream/Repositories/TemplateRepository.php",
      "functions": ["getAll", "findById", "create", "update", "delete"]
    }
  ],
  "impact_zone": [
    "engine/template_analyzer.py",
    "engine/sermon_generator.py",
    "modules/PptLivestream/Http/Controllers/PptController.php"
  ]
}
```
