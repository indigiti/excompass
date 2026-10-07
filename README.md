# ExCompass

ExCompass is a multi-layer local intelligence, ranking and decision-support web application.

Baseline:
- 17 discovery verticals with semantic SVG icons
- Click-oriented homepage
- Ranking, entity and cross-category search pages
- Catalog, ranking and search domain layers
- Subdirectory-safe URL helper
- Isolated illustrative seed data
- Smoke checks

Architecture:
Presentation -> Application bootstrap -> Domain services -> Repository/data layer.

Run locally:
php -S 127.0.0.1:8080

For a subdirectory deployment set EXCOMPASS_BASE_PATH, for example /excompass.

Current records and scores are illustrative. Production rankings must be evidence-backed, editorially reviewed and versioned.
