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
- GitHub CI with DigiOps release packaging

## DigiOps deployment

Every successful push to main produces the artifact:

digiops-release

Release contract:

- schema: DIGIOPS-RELEASE/1
- browser route: /excompass/
- public target: public_html/excompass/
- private target: private_html/excompass/
- persistent private path: storage/
- health endpoint: /excompass/health.php

The public payload contains only web entrypoints, assets and the Admin UI. Application code, configuration, CLI tools and the future DB schema remain in the private payload. The public runtime bridge resolves private_html/excompass automatically.

## Local run

php -S 127.0.0.1:8080

Open http://127.0.0.1:8080

For a subdirectory development environment set EXCOMPASS_BASE_PATH, for example /excompass.

## Create first admin

Set a strong password in the environment, then run:

EXCOMPASS_ADMIN_PASSWORD='replace-with-12-plus-characters' php bin/create-admin.php 'Admin Name' admin@example.com

On DigiOps the command should be run from private_html/excompass.

Then open /excompass/admin/.

Current entity records and scores are illustrative. Production rankings should use evidence-backed, editorially reviewed and versioned scoring.
