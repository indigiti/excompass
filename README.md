# ExCompass

ExCompass is a multi-layer local intelligence, ranking and decision-support web application.

## Runtime architecture

The application is intentionally database-free today:

Presentation -> domain services -> repository interfaces -> JSON/file adapters

The public site, Admin, authentication, editorial workflow and audit trail do not require MySQL. JSON writes are locked and replaced atomically.

The database directory is retained only as a future compatibility contract. A future MySQL adapter can implement the same repository interfaces without changing public pages or editorial workflow code.

## Current capabilities

- 17 discovery verticals with semantic SVG icons
- Click-oriented discovery homepage
- Ranking, entity and cross-category search pages
- File-backed entity persistence
- Admin authentication with password hashing and secure sessions
- RBAC for Admin, Editor, Researcher and Commercial roles
- Draft -> Review -> Approved -> Published editorial workflow
- JSON audit trail for editorial changes
- Catalog, Ranking and Search domain separation
- Subdirectory-safe URLs
- GitHub CI: syntax, smoke, scoring and storage/workflow checks

## Local run

php -S 127.0.0.1:8080

Open http://127.0.0.1:8080

For a subdirectory deployment set EXCOMPASS_BASE_PATH, for example /excompass.

## Create first admin

Set a strong password in the environment, then run:

EXCOMPASS_ADMIN_PASSWORD='replace-with-12-plus-characters' php bin/create-admin.php 'Admin Name' admin@example.com

Then open /admin/.

Current entity records and scores are illustrative. Production rankings should use evidence-backed, editorially reviewed and versioned scoring.
