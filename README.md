# ExCompass

ExCompass is a database-free, multi-layer local intelligence, ranking and decision-support web application.

## Current product baseline

The public experience is built as a premium editorial discovery product rather than a generic directory:

- bold editorial typography and high-contrast dark/light composition
- glass and champagne-toned surfaces
- semantic category icon system
- intent-first discovery
- cinematic score/index presentation
- bento-style editor selections
- locality intelligence
- responsive mobile layouts
- reduced-motion accessibility support
- cross-category search
- explainable ranking/profile views
- category-specific filters and decision facts
- rich score breakdowns, evidence/version provenance and locality intelligence
- compare and ranking-report workflows
- downloadable profile briefs and ranking badges
- JSON-backed enquiry capture with Admin inbox
- external HTTPS photography with no image files copied into ExCompass
- image-host allowlist, source/credit metadata, verification flag and automatic visual fallback
- compact premium header with responsive navigation and active category rail state
- city registry and city-scoped entity identity
- city → area/locality → vertical drilldown
- Pune-only active dataset today, with future cities isolated by composite geography keys

The development seed catalog currently contains exactly **105 published demo profiles, all scoped to Pune** so the whole product can be tested without manual setup:

- Real Estate: 25
- Other 16 verticals: 5 each
- Total: 105

All seeded profiles carry a demo marker and the public UI clearly labels the working dataset as demonstration data.

## Runtime architecture

The application is intentionally database-free:

Presentation -> domain services -> repository interfaces -> JSON/file adapters

Admin edits persist to JSON with locking and atomic replacement. The future MySQL schema is retained only as a compatibility contract so a later database adapter can implement the same repository interfaces without rewriting the product.

## Admin and workflow

- password-hashed admin authentication
- secure sessions + CSRF protection
- Admin / Editor / Researcher / Commercial RBAC
- Draft -> Review -> Approved -> Published workflow
- audit trail
- rich profile editor for decision facts, editorial notes, locality data, evidence and remote image URLs
- enquiry inbox for permitted Admin/Commercial roles
- DigiOps split-layout Admin runtime uses private-root dependencies and is HTTP smoke-tested
- one-time web bootstrap creates the first admin when no admin-role account exists, then permanently locks setup
- public pages expose published records only

## DigiOps deployment

Every successful push to main produces the artifact named digiops-release.

Release contract:

- schema: DIGIOPS-RELEASE/1
- version: 1.4.2
- browser route: /excompass/
- public target: public_html/excompass/
- private target: private_html/excompass/
- persistent private path: storage/
- health endpoint: /excompass/health.php

## First admin

Run from the private application directory:

EXCOMPASS_ADMIN_PASSWORD='replace-with-12-plus-characters' php bin/create-admin.php 'Admin Name' admin@example.com

Then open /excompass/admin/.

## Quality gates

CI validates PHP syntax, Pune-only city registry integrity, city/area normalization, 105-record seed integrity, rich profile and remote-media metadata, HTTPS/host safety, scoring, JSON persistence, RBAC workflow, city/area/ranking/profile/compare/report HTTP rendering and the exact DigiOps release payload.
