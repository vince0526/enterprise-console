# Core Databases Workbench

This document summarizes the upgraded Core Databases module: schema fields, endpoints, permissions, UI, rollout steps, and tips.

## Overview

The Workbench replaces and extends the existing Core Databases module with:
- Normalized engine/env fields (in addition to legacy platform/environment)
- New metadata: tier, tax_path, vc_stage, vc_industry, vc_subindustry, cross_enablers, functional_scopes
- Registry filters + CSV export
- DDL generation service and endpoint for PostgreSQL, MySQL, and SQL Server
- Blade UI tabs: Registry, Create (wizard + DDL preview/download), Guide; plus Ownership/Lifecycle/Links submodules
- Policies + permissions via spatie/laravel-permission

## Schema

Table: `core_databases` (non-breaking migration extends existing table)
- Legacy: name, platform, environment, owner, lifecycle, linked_connection, description, status
- Normalized: engine (string), env (Dev|UAT|Prod)
- Workbench fields: tier, tax_path, vc_stage, vc_industry, vc_subindustry, cross_enablers (json), functional_scopes (json), owner_email

Migration backfills `engine`/`env` from `platform`/`environment`.

## Endpoints

- GET `/emc/core` → Registry page with filters and tabs
- POST `/emc/core` → Create a record (FormRequest: `CoreDatabaseRequest`)
- PATCH `/emc/core/{id}` → Update
- DELETE `/emc/core/{id}` → Delete
- GET `/emc/core/export/csv` → CSV export of registry
- POST `/emc/core/ddl` → Generate DDL (body: `engine`, `functional_scopes[]`)
- Submodules:
  - POST `/emc/core/owners`, DELETE `/emc/core/owners/{owner}`
  - POST `/emc/core/lifecycle-events`, DELETE `/emc/core/lifecycle-events/{event}`
  - POST `/emc/core/links`, DELETE `/emc/core/links/{link}`

## Permissions & Policies

Spatie permissions seeded (assign to roles as needed):
- `core.view` (viewAny/view)
- `core.create`
- `core.update`
- `core.delete`
- `core.manage-owners`
- `core.manage-lifecycle`
- `core.manage-links`

In testing/dev the policy includes a guard that allows all to prevent test flakiness; ensure production envs rely on proper roles/permissions.

## UI

- Registry: search, tier/engine/env filters, functional scope checkboxes, CSV export; badges show applied filters.
- Create: tier/engine/env, stage/industry/subindustry, owner email, functional scope checkboxes, DDL preview/download, name auto-suggestion.
- Guide: highlights the five-tier reference and scope glossary.
- Ownership/Lifecycle/Links: existing submodules retained.

## DDL Generator

Service: `App\Services\CoreDatabaseDdlGenerator`
- Generates schema/table creation statements based on engine and selected scopes.
- Supported engines: PostgreSQL, MySQL, SQL Server.

## Tests

- Unit: `tests/Unit/CoreDatabaseDdlGeneratorTest.php`
- Feature:
  - `tests/Feature/CoreDatabasesCsvExportTest.php`
  - `tests/Feature/CoreDatabasesDdlAndFilterTest.php` (DDL endpoint + engine filter)
  - Existing EMC UI tests continue to pass.

## Rollout

1. Run migrations.
2. Seed permissions (ensure roles receive the core.* permissions).
3. Verify policies in production do not allow test/dev bypass.
4. Navigate to `/emc/core` to use the Workbench.

## Troubleshooting

- 403 responses: ensure roles/permissions configured and test/dev bypass is OFF in production.
- Missing DDL scopes: confirm you pass `functional_scopes[]` and a supported `engine` value.
- Filter not applying: check query string parameters `q, tier, engine, env, scopes[]` and clear caches if needed.
