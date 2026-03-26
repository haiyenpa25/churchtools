# Design Spec: System for Auto-Mapping Text to PowerPoint Master Templates

## 1. Understanding & Goals
**What is being built:** 
A hybrid template mapping system that allows the `ppt_generator.py` (and `sermon_generator.py`) engine to use any custom `.pptx` template to generate slides.
**Why:** To ensure the generated slides are beautiful, using native PowerPoint styling (fonts, colors, placeholders) instead of hardcoding raw shapes via Python.
**Target Users:** Platform Admins / Church Staff who want to upload downloaded templates (e.g., from Canva or Slidesgo) and have them work automatically.
**Key Constraints:** 
- Must require zero to minimal user intervention (upload and it works).
- Must be 100% accurate (provide a fallback if the automation fails).
**Non-Goals:** Building a full visual template editor in the browser. 

## 2. Assumptions
- The input payload contains correctly parsed object types (`title`, `verse`, `main_point`).
- The uploaded PPTX template uses PowerPoint's standard Slide Layout and Placeholder architecture.

## 3. The Proposed Design: "AI/Heuristic Auto-Mapping with User Override"

We will implement a 2-phase system that balances **absolute automation** with **absolute control**.

### Phase 1: Smart Auto-Discovery (Zero Intervention)
When a user uploads a new `.pptx` template:
1. `template_analyzer.py` opens the PPTX and analyzes its `slide_layouts`.
2. It extracts structural data for each layout (e.g., "Layout 3 has a huge text box in the middle", "Layout 5 has a Title box and a Content box").
3. A rule-based Heuristic (or an LLM API call) automatically maps the layout to the app's content types. 
   - *Example logic:* If a layout has exactly 1 massive placeholder, it maps to `verse` and `quote`. If it has a top placeholder and a body placeholder, it maps to `content` and `list`.
4. It saves this as a lightweight config file: `template_mapping.json`.

### Phase 2: Explicit GUI / JSON Override (100% Accuracy Fallback)
If the AI guesses wrong (e.g., it chose an ugly title layout for the Bible Verse):
1. In the Laravel Frontend (or by editing `template_mapping.json` directly), the user can view the mapping:
   - `verse -> Layout 3 (Title and Content)`
2. The user can override it: "No, change `verse` to Layout 5 (Big Text)".
3. The generator will prioritize the user's manual override over the AI's guess.

## 4. Architecture & Data Flow
1. **[Upload]** User uploads `beautiful_theme.pptx`.
2. **[Analyze]** `template_analyzer.py` runs -> `beautiful_theme_map.json` is generated instantly.
3. **[Override]** User (optionally) tweaks the map in the UI.
4. **[Generate]** `sermon_generator.py` receives payload -> looks up `beautiful_theme_map.json` -> generates a beautiful presentation.
