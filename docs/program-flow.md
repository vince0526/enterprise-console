# Enterprise Console — Program Flow (Overview)

This document summarizes the flow based on our implementation and discussion. It focuses on the CORE DATABASES module and the ERD subdomains we added.

## High-level Navigation

- / → redirect to /emc/core
- /emc/core → Core Databases Workbench
  - Tabs: Registry, Create, Ownership, Lifecycle, Links
- /erd/\* → Read-only browse endpoints (verification for ERD schema)

## Core Databases Module Flow

1. Registry Tab
   - Read paginated list with filters (engine, env, vc_stage, etc.)
   - Saved Views: inline rename/duplicate/delete; toast feedback; search with pagination
   - Export CSV; sort aliases (environment→env, platform→engine)
2. Create Tab (Wizard)
   - Select Tier → Stage → Industry → Subindustry; choose Engine & Env
   - Optional Cross-cutting Enablers → suggest Functional Scopes
   - Preview/Download DDL (no persistence)
   - Generate → POST /emc/core (persist record)
   - Redirect back to Registry upon success
3. Submodules
   - Ownership: owners CRUD
   - Lifecycle: lifecycle events CRUD
   - Links: arbitrary links CRUD

## Saved Views Flow

- List saved views → search, page, limit (capped by config), RFC 5988 Link headers
- Create → POST; Rename inline → PATCH; Duplicate inline → POST; Delete → DELETE
- Conflict handling → 422 with error toasts

## ERD Domains (Added)

- Taxonomy: Industry, Subindustry, Value Chain Stage, Public Good
- Programs: Program (links to Public Good and Lead Gov Org)
- Regulation: Domain, Legal Instrument, Regulated Sector, Compliance Obligation + pivots
- CSO Structures: CSO Super Category, CSO Type + pivots
- Facilities/Providers: Service Channel, Facility Site, Provider Registry
- Beneficiaries & Metrics: Beneficiary Group, Funding Source, KPI + pivots

## Data Flow (Selected)

- DDL Service: engine + scopes → SQL text (no write)
- Create Core DB: request payload normalized (legacy → modern), persisted
- Saved Views: API-driven CRUD with search/pagination; inline UI actions
- ERD: browse endpoints expose lists for verification/UI prototyping

---

This is a living document. Extend with sequence diagrams and state charts as needed.
