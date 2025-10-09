## Core Databases Legacy Column Migration Plan

Goal: Gradually deprecate legacy columns `environment` and `platform` in favor of new normalized columns `env` and `engine` without data loss or breaking existing code/routes until consumers are updated.

### Current State

- Both legacy (`environment`, `platform`) and new (`env`, `engine`) columns exist.
- FormRequest backfills both directions during create/update.
- Tests rely exclusively on new columns; legacy kept for backward compatibility and historical data.

### Risks

- Direct consumer queries or exports may still reference legacy columns.
- Dropping NOT NULL legacy columns prematurely would break inserts that bypass FormRequest.
- Indexes/constraints duplication could add write overhead.

### Phased Approach

1. Audit & Telemetry (Week 0–1)
   - Add lightweight logging (or Telescope tag) when legacy columns are read directly in application code (optional feature flag).
   - Identify external consumers (BI dashboards, ETL jobs) querying `environment` / `platform`.

2. Read Path Abstraction (Week 1–2)
   - Add accessors on `CoreDatabase` model: `getEnvironmentAttribute()` returns `$this->envFormatted()` mapping short codes to long names.
   - Similarly for `platform` mapping to `engine`.
   - Replace direct template references to legacy columns with new ones or accessors.

3. Write Path Toggle (Week 2–3)
   - Feature flag `core_databases.legacy_write` (default true). When false, FormRequest stops writing legacy columns (but still reads them if present).
   - Run with flag off in staging; validate no regressions.

4. Backfill & Freeze (Week 3)
   - One-off Artisan command to ensure all rows have non-null `engine`/`env` (already mostly handled in previous migration) and copy back to legacy if missing.
   - Remove NOT NULL from legacy columns (or keep until drop) – optional if risk of null reads is acceptable.

5. Deprecation Announce (Week 3)
   - Communicate removal timeline to stakeholders.
   - Provide mapping guidance in docs.

6. Soft Removal (Week 4)
   - Add DB triggers (optional) or scheduled job verifying legacy columns equal new columns for drift detection.
   - Mark legacy columns as deprecated in code comments & phpdoc (@deprecated tag).

7. Drop Columns (Week 6)
   - New migration: drop `environment`, `platform`.
   - Remove backfill logic from `CoreDatabaseRequest`.
   - Update tests to assert absence of legacy columns if necessary.

### Rollback Strategy

- If issues arise post-drop, revert migration and keep backfill logic; run drift detection to find mismatched rows.

### Acceptance Criteria

- No failing tests after flag off and after columns removed.
- No external consumer tickets reporting missing data for two weeks post removal.

### Follow-ups

- Add formal enum mapping for `env` (Dev/UAT/Prod) & `engine` (PostgreSQL/MySQL/SQL Server) to enforce consistency.
- Consider partial index or composite index `(engine, env)` to optimize registry filters.
