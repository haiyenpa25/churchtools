## 1. Data Flow (Bible Learning Portal Dashboard)
```json
{
  "Feature_Portal_Hub": {
    "EntryPoint": {
      "Vue": "resources/js/components/BibleLearning/BiblePortal.vue",
      "Route_GET": "/bible-learning",
      "API_Stats": "GET /api/portal/stats"
    },
    "Controller": "PortalController",
    "Repository": ["ApprovalRepository", "FlashcardRepository", "App\\Models\\BlTempEntity"]
  }
}
```

## 2. Data Flow (Event Timeline)
```json
{
  "Feature_Event_Timeline": {
    "EntryPoint": {
      "Vue": "resources/js/components/BibleLearning/EventTimeline.vue",
      "Route_GET": "/bible-learning/timeline",
      "API_Timeline": "GET /api/events"
    },
    "Controller": "EventController",
    "Repository": ["EventRepositoryInterface", "EventRepository"],
    "Model_Database": "App\\Models\\BlEvent (bl_events)"
  }
}
```

## 3. Data Flow (Gamified Quizzes Arena)
```json
{
  "Feature_Quiz_Arena": {
    "EntryPoint": {
      "Vue": "resources/js/components/BibleLearning/QuizSession.vue",
      "Route_GET": "/bible-learning/quiz",
      "API_Quizzes": "GET /api/quizzes/random"
    },
    "Controller": "QuizController",
    "Repository": ["QuizRepositoryInterface", "QuizRepository"],
    "Model_Database": "App\\Models\\BlQuiz (bl_quizzes)"
  }
}
```

## 4. Data Flow (Knowledge Graph)
```json
{
  "Feature_Knowledge_Graph": {
    "EntryPoint": {
      "Vue": "resources/js/components/BibleLearning/KnowledgeGraph.vue",
      "Route_GET": "/bible-learning/graph",
      "API_Graph": "GET /api/graph"
    },
    "Controller": "GraphController",
    "Repository": ["GraphRepositoryInterface", "GraphRepository"],
    "Model_Database": "App\\Models\\BlNode (bl_nodes) & App\\Models\\BlEdge (bl_edges)"
  }
}
```

## 5. Data Flow (RAG Web Crawler)
```json
{
  "Feature_Crawler": {
    "Target": "kinhthanh.httlvn.org/?v=VI1934",
    "API_EntryPoint": "POST /api/crawl/gemini",
    "Controller": "FlashcardController@crawlHTTLVN",
    "Service": "RagScraperService",
    "Dependency": "DOMDocument, XPath",
    "Output": "Raw Pure Text for Gemini Prompts"
  }
}
```

## 2. Data Flow (Approval Center)
```json
{
  "Feature_Approval_Center": {
    "EntryPoint": "resources/js/components/BibleLearning/ApprovalCenter.vue",
    "Controller": "ApprovalController",
    "Service": "ApprovalService",
    "Repository": ["ApprovalRepositoryInterface", "ApprovalRepository"],
    "Model_Database": "bl_temp_entities",
    "Next_Step": "Upon Approval, triggers auto-migration to bl_flashcards"
  }
}
```

## 3. Data Flow (Flashcard Spaced Repetition)
```json
{
  "Feature_Flashcard_Study": {
    "EntryPoint": {
      "Vue": "resources/js/components/BibleLearning/FlashcardStudy.vue",
      "Route_GET": "/bible-learning/study",
      "API_Due_Cards": "GET /api/flashcards/due",
      "API_Review": "POST /api/flashcards/{id}/review"
    },
    "Controller": "FlashcardController",
    "Service": "SpacedRepetitionService",
    "Algorithm": "SuperMemo-2 (SM-2)",
    "Repository": ["FlashcardRepositoryInterface", "FlashcardRepository"],
    "Model_Database": [
      "App\\Models\\BlFlashcard (bl_flashcards)",
      "App\\Models\\BlFlashcardReview (bl_flashcard_reviews)"
    ]
  }
}
```
