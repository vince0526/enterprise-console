<!-- markdownlint-disable MD024 -->

# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning via git tags.

## [0.6.1] - 2025-10-09

### Added

- Inline editable rename for Saved Views (no modal): edit name in-place and save on blur/Enter with server validation.
- Documentation: Saved Views API (endpoints, params, pagination headers, error codes) in `docs/saved-views-api.md`.
- Documentation: Production deployment notes in `docs/DEPLOYMENT.md` (env, caches, migrations, Vite build).
- Documentation: Git Credential Manager setup for Windows in `docs/git-credential-manager-windows.md`.
- CI: GitHub Actions workflow `ci.yml` running Pint, PHPStan and test suite on push/PR.

### Changed

- README updated with links to the new docs and version info.

## [0.6.0] - 2025-10-05

### Added

- Core Databases workbench (Registry, Ownership, Lifecycle, Links) with modernized UI and filters.
- Saved Views system with API (list/store/destroy) and client UI with pagination, page-size, search and compact mode.
- CSV export for registry and DDL preview/download tools for engine-specific schemas.
- Configurable API limits for Saved Views (default limit and hard cap).
- Policies and validation hardening, pagination Link headers (RFC 5988), and metadata response headers.
- Tests for CRUD, pagination, search, limits, conflict handling and UI smoke.

---

[0.6.1]: https://github.com/vince0526/enterprise-console/releases/tag/v0.6.1
[0.6.0]: https://github.com/vince0526/enterprise-console/releases/tag/v0.6.0
