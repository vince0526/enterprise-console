{{--
    Core Databases Workbench View (Blade)

    Sections:
    - Registry: filters, saved views, sortable table, quick view panel
    - Create: stage-first wizard, cross enablers, scope suggestions, DDL preview
    - Guide: reference text for tiers/stages/scopes
    - Ownership/Lifecycle/Links: submodules CRUD panels

    Customize safely:
    - Filters and Saved Views: modify the <form> in the registry tab; keep names aligned with controller query params.
    - Table columns: adjust header and row cells; update aria-sort attributes if you add new sort options.
    - Wizard lists: see VC_STACK, STAGE_TO_VC_MAP, VC and CROSS_ENABLERS in the script section.
    - DDL preview buttons: the routes and POST body are built in previewDDL()/downloadDDL().
    - Quick View: markup under #quickviewWrapper and logic in openQuickView()/closeQuickView().
--}}
@extends('emc.layout')

@section('title', 'Core Databases - EMC')

@section('content')
    @push('head')
        <!-- PrismJS theme bundled via app.css -->
    @endpush
    <style>
        /* TIP: Prefer moving repeated styles into resources/css/emc.css.
                                       Keep inline styles here only for small page-specific tweaks. */
        /* Layout polish */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .75rem;
        }

        .page-header__title {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--color-text-primary);
        }

        .page-header__subtitle {
            margin: .25rem 0 0;
            color: var(--color-text-secondary);
            font-size: .95rem;
        }

        .tabs {
            margin-top: 0.5rem;
        }

        .tabs__nav {
            background: var(--color-background-light);
            padding: .375rem;
            border: 1px solid var(--color-border-light);
            border-radius: 8px;
        }

        .tabs__nav .btn {
            border-radius: 6px;
        }

        .sub-section {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 1px solid var(--color-border-light);
            border-radius: 8px;
            background-color: var(--color-surface, #fff);
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .sub-section__title {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            border-bottom: 1px solid var(--color-border-light);
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .sub-section__title strong {
            color: var(--color-primary);
        }

        /* Table polish */
        .table-container {
            border: 1px solid var(--color-border-light);
            border-radius: 8px;
            overflow: hidden;
            background: var(--color-surface, #fff);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .95rem;
        }

        .table__header {
            position: sticky;
            top: 0;
            z-index: 1;
            background: var(--color-background, #f9fafb);
        }

        .table__header-cell {
            text-align: left;
            font-weight: 600;
            padding: .625rem .75rem;
            border-bottom: 1px solid var(--color-border-light);
            color: var(--color-text-secondary);
        }

        .table__row:nth-child(even) {
            background: rgba(0, 0, 0, .015);
        }

        .table__cell {
            padding: .625rem .75rem;
            border-bottom: 1px solid var(--color-border-light);
            vertical-align: top;
        }

        .table__actions {
            display: flex;
            gap: .5rem;
            align-items: center;
        }

        /* Badges & statuses */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .5rem;
            border-radius: 999px;
            background: var(--color-background-light);
            border: 1px solid var(--color-border-light);
            font-size: .75rem;
            color: var(--color-text-secondary);
        }

        .status {
            display: inline-block;
            padding: .15rem .5rem;
            border-radius: 999px;
            font-size: .75rem;
        }

        .status--success {
            background: #e6f7ee;
            color: #0f5132;
            border: 1px solid #bde5ce;
        }

        .status--warning {
            background: #fff4e5;
            color: #8a5300;
            border: 1px solid #ffdfb5;
        }

        .status--info {
            background: #e7f1ff;
            color: #084298;
            border: 1px solid #cfe2ff;
        }

        /* Toolbar */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin: .75rem 0 1rem;
        }

        .toolbar__title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
    </style>

    <div class="content-frame">
        <div class="content">
            @if (session('status'))
                <div class="alert alert--success">
                    {{ session('status') }}
                </div>
            @endif


            <div class="page-header">
                <div>
                    <h2 class="page-header__title">Core Databases</h2>
                    <p class="page-header__subtitle">Workbench for registry, creation wizard, submodules, and DDL tools</p>
                </div>
                @if ($activeTab === 'registry')
                    <a class="btn btn--small" href="{{ route('emc.core.export.csv') }}" title="Export registry as CSV">Export
                        CSV</a>
                @endif
            </div>

            <div class="tabs">
                <div class="tabs__nav">
                    <a href="?tab=registry"
                        class="btn btn--small {{ $activeTab === 'registry' ? 'btn--primary' : 'btn--secondary' }}">Registry</a>
                    <a href="?tab=create"
                        class="btn btn--small {{ $activeTab === 'create' ? 'btn--primary' : 'btn--secondary' }}">Create</a>
                    <a href="?tab=guide"
                        class="btn btn--small {{ $activeTab === 'guide' ? 'btn--primary' : 'btn--secondary' }}">Guide</a>
                    <span class="ml-auto"></span>
                </div>
                <div class="tabs__content">
                    @if ($activeTab === 'registry')
                        {{-- Registry Filters: add/remove filters here and mirror in controller index() query. --}}
                        <form id="filtersForm" method="GET" class="sub-section mt-3">
                            <input type="hidden" name="tab" value="registry" />
                            <div class="registry-filters">
                                <div>
                                    <label class="form__label">Search</label>
                                    <input name="q" class="form__input" value="{{ request('q', '') }}"
                                        placeholder="name, owner, engine, env, tier..." />
                                </div>
                                <div>
                                    <label class="form__label">Tier</label>
                                    <select name="tier" class="form__select">
                                        <option value="">— Any —</option>
                                        @foreach (['Value Chain', 'Public Goods & Governance', 'CSO', 'Media', 'Financial'] as $t)
                                            <option value="{{ $t }}" @selected(request('tier') === $t)>
                                                {{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form__label">Engine</label>
                                    <select name="engine" class="form__select">
                                        <option value="">— Any —</option>
                                        @foreach (['PostgreSQL', 'MySQL', 'SQL Server'] as $e)
                                            <option value="{{ $e }}" @selected(request('engine') === $e)>
                                                {{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form__label">Environment</label>
                                    <select name="env" class="form__select">
                                        <option value="">— Any —</option>
                                        @foreach (['Dev', 'UAT', 'Prod'] as $e)
                                            <option value="{{ $e }}" @selected(request('env') === $e)>
                                                {{ $e }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form__label">VC Stage</label>
                                    @php($vcStage = request('vc_stage'))
                                    <select name="vc_stage" class="form__select">
                                        <option value="">— Any —</option>
                                        @foreach (['Resource Extraction (Primary)', 'Primary Processing (Materials)', 'Secondary Manufacturing & Assembly', 'Market Access, Trading & Wholesale', 'Logistics, Ports & Fulfillment', 'Retail & Direct-to-Consumer (Goods)', 'Service Delivery (End-User Services)', 'After-Sales, Reverse & End-of-Life'] as $s)
                                            <option value="{{ $s }}" @selected($vcStage === $s)>
                                                {{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-group--scopes">
                                    <label class="form__label">Functional Scopes</label>
                                    @php($selectedScopes = (array) request('scopes', []))
                                    <div class="checkbox-group">
                                        @foreach (['Accounting', 'Inventory', 'Manufacturing', 'HRM', 'Logistics', 'Compliance', 'MediaSpecific', 'FinanceSpecific'] as $s)
                                            <label class="form__checkbox">
                                                <input type="checkbox" name="scopes[]" value="{{ $s }}"
                                                    @checked(in_array($s, $selectedScopes, true)) />
                                                <span>{{ $s }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-3">
                                <button type="submit" class="btn btn--primary">Apply Filters</button>
                                <a href="{{ route('emc.core.index') }}?tab=registry" class="btn btn--secondary">Reset</a>
                                <span class="text-muted ml-auto">Sort</span>
                                @php($sort = request('sort', 'name'))
                                @php($dir = request('direction', 'asc'))
                                <select name="sort" class="form__select w-auto">
                                    @foreach (['name', 'environment', 'platform', 'owner', 'status'] as $col)
                                        <option value="{{ $col }}" @selected($sort === $col)>
                                            {{ ucfirst($col) }}</option>
                                    @endforeach
                                </select>
                                <select name="direction" class="form__select w-auto">
                                    @foreach (['asc' => 'Asc', 'desc' => 'Desc'] as $k => $v)
                                        <option value="{{ $k }}" @selected($dir === $k)>
                                            {{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="saved-views">
                                <label class="form__label">Saved Views</label>
                                <button type="button" class="btn btn--tiny" id="btnSaveView"
                                    title="Save current filters as a named view">Save view</button>
                                <a class="btn btn--tiny btn--secondary"
                                    href="{{ route('emc.core.index', ['tab' => 'registry', 'env' => 'Prod']) }}">All
                                    Prod</a>
                                <a class="btn btn--tiny btn--secondary"
                                    href="{{ route('emc.core.index', ['tab' => 'registry', 'tier' => 'Value Chain', 'vc_stage' => 'Logistics, Ports & Fulfillment']) }}">Stage:
                                    Logistics</a>
                                <a class="btn btn--tiny btn--secondary"
                                    href="{{ route('emc.core.index', ['tab' => 'registry', 'tier' => 'Public Goods & Governance']) }}">Public
                                    Goods</a>
                                <!-- Managed saved views (client-side) -->
                                <div id="savedViewsManaged" class="flex gap-2" aria-live="polite"></div>
                                <!-- Saved Views pagination controls -->
                                <div id="savedViewsPager" class="flex items-center gap-2 mt-2">
                                    <button type="button" id="svPrev" class="btn btn--tiny" disabled>◀ Prev</button>
                                    <span id="svMeta" class="text-muted text-xs"></span>
                                    <button type="button" id="svNext" class="btn btn--tiny" disabled>Next ▶</button>
                                </div>
                            </div>
                            @php($hasFilters = request()->hasAny(['q', 'tier', 'engine', 'env', 'scopes', 'vc_stage']))
                            @if ($hasFilters)
                                <div class="applied-filters">
                                    <span class="text-muted">Applied:</span>
                                    @foreach (['q' => 'q', 'tier' => 'tier', 'engine' => 'engine', 'env' => 'env'] as $label => $key)
                                        @if (request($key))
                                            <span class="badge">{{ strtoupper($label) }}: {{ request($key) }}</span>
                                        @endif
                                    @endforeach
                                    @if (request('vc_stage'))
                                        <span class="badge">STAGE: {{ request('vc_stage') }}</span>
                                    @endif
                                    @foreach ($selectedScopes as $s)
                                        <span class="badge">SCOPE: {{ $s }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </form>
                        <div class="toolbar">
                            <div class="toolbar__left">
                                <h3 class="toolbar__title">Database Registry @if (isset($coreDbs))
                                        <span class="text-muted" style="font-weight:400">({{ $coreDbs->count() }})</span>
                                    @endif
                                </h3>
                            </div>
                            <div class="toolbar__right">
                                <button class="btn btn--primary" onclick="resetCoreDbForm(); openModal('coreDbModal')">+
                                    Add
                                    Core Database</button>
                                <button class="btn btn--secondary" onclick="refreshCoreDbs()">↻ Refresh</button>
                            </div>
                        </div>

                        {{-- Registry Table: update columns and aria-sort attributes as needed. --}}
                        <div class="table-container" id="coreDbsTable">
                            <table class="table">
                                <thead class="table__header">
                                    <tr>
                                        <th class="table__header-cell table__header-cell--sortable" data-sort="name"
                                            role="button" tabindex="0"
                                            aria-sort="{{ request('sort', 'name') === 'name' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                            Name</th>
                                        <th class="table__header-cell table__header-cell--sortable"
                                            data-sort="environment" role="button" tabindex="0"
                                            aria-sort="{{ request('sort') === 'environment' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                            Environment</th>
                                        <th class="table__header-cell table__header-cell--sortable" data-sort="platform"
                                            role="button" tabindex="0"
                                            aria-sort="{{ request('sort') === 'platform' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                            Platform</th>
                                        <th class="table__header-cell table__header-cell--sortable" data-sort="owner"
                                            role="button" tabindex="0"
                                            aria-sort="{{ request('sort') === 'owner' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                            Owner</th>
                                        <th class="table__header-cell" aria-sort="none">Lifecycle</th>
                                        <th class="table__header-cell" aria-sort="none">Linked Connection</th>
                                        <th class="table__header-cell table__header-cell--sortable" data-sort="status"
                                            role="button" tabindex="0"
                                            aria-sort="{{ request('sort') === 'status' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                            Status</th>
                                        <th class="table__header-cell">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="coreDbRows">
                                    @forelse($coreDbs as $db)
                                        <tr class="table__row">
                                            <td class="table__cell"><strong>{{ data_get($db, 'name') }}</strong>
                                                <div class="text-muted text-xs">
                                                    {{ data_get($db, 'description') }}</div>
                                            </td>
                                            <td class="table__cell">{{ data_get($db, 'environment') }}</td>
                                            <td class="table__cell">{{ data_get($db, 'platform') }}</td>
                                            <td class="table__cell">{{ data_get($db, 'owner') }}</td>
                                            <td class="table__cell">{{ data_get($db, 'lifecycle') }}</td>
                                            <td class="table__cell">
                                                @php($lc = data_get($db, 'linked_connection'))
                                                @if (!empty($lc) && isset($connectionByName) && $connectionByName->has($lc))
                                                    @php($conn = $connectionByName->get($lc))
                                                    <a href="{{ url('/emc/db?connection_id=' . $conn->id) }}"
                                                        title="Open Database Connection">{{ $lc }}</a>
                                                @else
                                                    {{ $lc ?? '—' }}
                                                @endif
                                            </td>
                                            @php($status = data_get($db, 'status'))
                                            @php($statusClass = $status === 'healthy' ? 'status--success' : ($status === 'warning' ? 'status--warning' : 'status--info'))
                                            <td class="table__cell"><span
                                                    class="status {{ $statusClass }}">{{ $status ? ucfirst($status) : 'N/A' }}</span>
                                            </td>
                                            <td class="table__cell">
                                                <div class="table__actions">
                                                    <button class="btn btn--small"
                                                        onclick='openQuickView(event, {!! json_encode($db->toArray(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!})'>View</button>
                                                    <button class="btn btn--small btn--secondary"
                                                        onclick='editCoreDb({!! json_encode($db->toArray(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}); event.stopPropagation();'>Edit</button>
                                                    @if (Route::has('emc.core.destroy'))
                                                        <form method="POST"
                                                            action="{{ route('emc.core.destroy', $db) }}"
                                                            onsubmit="return confirm('Delete this core database?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn--small btn--danger"
                                                                onclick="event.stopPropagation()">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="table__cell text-muted" colspan="8">No core databases yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Quick View Slide-over: accessible panel with backdrop and focus handling. -->
                        <div id="quickviewWrapper" aria-hidden="true">
                            <div class="quickview__backdrop" id="qvBackdrop" onclick="closeQuickView()"
                                aria-hidden="true"></div>
                            <aside class="quickview" id="quickview" role="dialog" aria-modal="true"
                                aria-labelledby="qvTitle">
                                <header class="quickview__header">
                                    <h4 class="quickview__title" id="qvTitle">Details</h4>
                                    <button id="qvClose" class="modal__close" aria-label="Close"
                                        onclick="closeQuickView()">×</button>
                                </header>
                                <section class="quickview__body">
                                    <div id="qvBody" class="text-muted">Select a row to see details.</div>
                                </section>
                                <footer class="quickview__footer">
                                    <a id="qvOpenLink" class="btn btn--secondary" href="#" target="_blank"
                                        rel="noopener">Open Connection</a>
                                    <button id="qvPrimaryClose" class="btn btn--primary"
                                        onclick="closeQuickView()">Close</button>
                                </footer>
                            </aside>
                        </div>
                    @elseif($activeTab === 'create')
                        <div class="sub-section">
                            <h4 class="sub-section__title">Create Database (Stage → Industry → Subindustry)</h4>
                            <form id="wizardForm" onsubmit="return submitWizard(event)">
                                <div class="wizard-grid-3">
                                    <div>
                                        <label class="form__label">Tier</label>
                                        <select class="form__select" id="w_tier">
                                            <option>Value Chain</option>
                                            <option>Public Goods & Governance</option>
                                            <option>CSO</option>
                                            <option>Media</option>
                                            <option>Financial</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form__label">Engine</label>
                                        <select class="form__select" id="w_engine">
                                            <option>PostgreSQL</option>
                                            <option>MySQL</option>
                                            <option>SQL Server</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form__label">Environment</label>
                                        <select class="form__select" id="w_env">
                                            <option>Dev</option>
                                            <option>UAT</option>
                                            <option>Prod</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="wizard-grid-3 mt-4">
                                    <div>
                                        <label class="form__label">Value-Chain Stage</label>
                                        <select class="form__select" id="w_stage"></select>
                                        <div class="text-muted text-xs mt-2">Order: Stage
                                            → Industry → Subindustry</div>
                                    </div>
                                    <div>
                                        <label class="form__label">Industry</label>
                                        <select class="form__select" id="w_industry" disabled></select>
                                    </div>
                                    <div>
                                        <label class="form__label">Subindustry</label>
                                        <select class="form__select" id="w_subindustry" disabled></select>
                                    </div>
                                </div>
                                <div class="sub-section mt-4">
                                    <h5 class="sub-section__title mb-2">Cross-Cutting Enablers
                                    </h5>
                                    <div id="w_enablers" class="enablers-grid">
                                    </div>
                                </div>
                                <div class="sub-section mt-4">
                                    <h5 class="sub-section__title mb-2">Functional Scopes</h5>
                                    <div id="w_scopes_group" class="scopes-grid">
                                        @foreach (['Accounting', 'Inventory', 'Manufacturing', 'HRM', 'Logistics', 'Compliance', 'MediaSpecific', 'FinanceSpecific'] as $s)
                                            <label class="enabler">
                                                <input type="checkbox" class="w_scope" value="{{ $s }}" />
                                                <span>{{ $s }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="text-muted suggested-text" id="w_suggested">
                                    </div>
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" class="btn btn--secondary"
                                            onclick="applySuggestedScopes()">Add Suggested</button>
                                        <button type="button" class="btn btn--secondary" onclick="clearScopes()">Clear
                                            All</button>
                                    </div>
                                </div>
                                <div class="wizard-grid-2 mt-4">
                                    <div>
                                        <label class="form__label">Name</label>
                                        <input class="form__input" id="w_name"
                                            placeholder="auto-suggested if blank" />
                                    </div>
                                    <div>
                                        <label class="form__label">Owner (email)</label>
                                        <input class="form__input" id="w_owner_email" placeholder="owner@example.com" />
                                    </div>
                                </div>
                                <div class="sub-section mt-4">
                                    <h5 class="sub-section__title mb-2">DDL Preview</h5>
                                    <pre class="code-pre"><code id="w_ddl" class="language-sql">(select engine & scopes and click Preview)</code></pre>
                                    <div class="flex gap-2">
                                        <button type="button" class="btn btn--secondary"
                                            onclick="previewDDL()">Preview</button>
                                        <button type="button" class="btn" onclick="downloadDDL()">Download
                                            .sql</button>
                                        <button type="button" class="btn btn--secondary"
                                            onclick="copyDDL()">Copy</button>
                                    </div>
                                </div>
                                <div class="flex gap-2 justify-end">
                                    <button type="submit" class="btn btn--primary">Generate</button>
                                </div>
                            </form>
                        </div>
                    @elseif($activeTab === 'guide')
                        <div class="sub-section">
                            <h4 class="sub-section__title">Guide: Five-Tier Reference & Stage Stack</h4>
                            <p class="text-muted">This panel summarizes stages, industries, dependencies, and regulatory
                                touchpoints as per the design spec.</p>
                            <ul class="list-disc" style="padding-left:1.2rem;">
                                <li><strong>Selection order:</strong> Stage → Industry → Subindustry</li>
                                <li><strong>Cross-cutting enablers:</strong> Finance & Insurance, Payments, Standards &
                                    Certification, Legal & Compliance, Data/IT & Cybersecurity, Telecom, Energy & Utilities,
                                    Workforce & Training, R&D & Design, ESG & Reporting</li>
                                <li><strong>Functional scopes:</strong> Accounting, Inventory, Manufacturing, Procurement,
                                    HRM, Logistics, Communications, Compliance, Analytics, MediaSpecific, FinanceSpecific
                                </li>
                                <li>Use the Create tab to generate a new entry and download DDL tailored to the selected
                                    engine and scopes.</li>
                            </ul>
                        </div>
                    @elseif($activeTab === 'ownership')
                        @foreach ($coreDbs as $db)
                            <div class="sub-section">
                                <h4 class="sub-section__title">Ownership: <strong>{{ $db->name }}</strong></h4>
                                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                                    <div>
                                        <table class="table">
                                            <thead class="table__header">
                                                <tr>
                                                    <th>Owner Name/Team</th>
                                                    <th>Role</th>
                                                    <th>Effective Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($db->owners as $owner)
                                                    <tr>
                                                        <td>{{ $owner->owner_name }}</td>
                                                        <td>{{ $owner->role }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($owner->effective_date)->format('Y-m-d') }}
                                                        </td>
                                                        <td>
                                                            <form method="POST"
                                                                action="{{ route('emc.core.owners.destroy', $owner) }}"
                                                                onsubmit="return confirm('Delete this owner entry?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn--small btn--danger">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" style="color: var(--color-text-secondary);">No
                                                            ownership records.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <h5>Add New Owner</h5>
                                        <form method="POST" action="{{ route('emc.core.owners.store') }}">
                                            @csrf
                                            <input type="hidden" name="core_database_id" value="{{ $db->id }}">
                                            <div class="form__group">
                                                <label class="form__label">Owner Name/Team</label>
                                                <input name="owner_name" class="form__input" required>
                                            </div>
                                            <div class="form__group">
                                                <label class="form__label">Role</label>
                                                <input name="role" class="form__input"
                                                    placeholder="e.g., Technical, Business" required>
                                            </div>
                                            <div class="form__group">
                                                <label class="form__label">Effective Date</label>
                                                <input type="date" name="effective_date" class="form__input"
                                                    value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <button type="submit" class="btn btn--primary">Add Owner</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif($activeTab === 'lifecycle')
                        @foreach ($coreDbs as $db)
                            <div class="sub-section">
                                <h4 class="sub-section__title">Lifecycle Events: <strong>{{ $db->name }}</strong></h4>
                                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                                    <div>
                                        <table class="table">
                                            <thead class="table__header">
                                                <tr>
                                                    <th>Event</th>
                                                    <th>Details</th>
                                                    <th>Effective Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($db->lifecycleEvents as $event)
                                                    <tr>
                                                        <td>{{ $event->event_type }}</td>
                                                        <td>{{ $event->details }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($event->effective_date)->format('Y-m-d') }}
                                                        </td>
                                                        <td>
                                                            <form method="POST"
                                                                action="{{ route('emc.core.lifecycle-events.destroy', $event) }}"
                                                                onsubmit="return confirm('Delete this lifecycle event?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn--small btn--danger">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" style="color: var(--color-text-secondary);">No
                                                            lifecycle events.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <h5>Add New Lifecycle Event</h5>
                                        <form method="POST" action="{{ route('emc.core.lifecycle-events.store') }}">
                                            @csrf
                                            <input type="hidden" name="core_database_id" value="{{ $db->id }}">
                                            <div class="form__group">
                                                <label class="form__label">Event Type</label>
                                                <input name="event_type" class="form__input"
                                                    placeholder="e.g., Created, Decommissioned" required>
                                            </div>
                                            <div class="form__group">
                                                <label class="form__label">Details</label>
                                                <textarea name="details" class="form__textarea" rows="2"></textarea>
                                            </div>
                                            <div class="form__group">
                                                <label class="form__label">Effective Date</label>
                                                <input type="date" name="effective_date" class="form__input"
                                                    value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <button type="submit" class="btn btn--primary">Add Event</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif($activeTab === 'links')
                        @foreach ($coreDbs as $db)
                            <div class="sub-section">
                                <h4 class="sub-section__title">Linked Connections & Policies:
                                    <strong>{{ $db->name }}</strong>
                                </h4>
                                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                                    <div>
                                        <table class="table">
                                            <thead class="table__header">
                                                <tr>
                                                    <th>Linked Connection</th>
                                                    <th>Link Type</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($db->links as $link)
                                                    <tr>
                                                        <td>
                                                            @if ($link->databaseConnection)
                                                                <a href="{{ url('/emc/db?connection_id=' . $link->databaseConnection->id) }}"
                                                                    title="Open Database Connection">{{ $link->databaseConnection->name }}</a>
                                                            @else
                                                                {{ $link->linked_connection_name }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $link->link_type }}</td>
                                                        <td>
                                                            <form method="POST"
                                                                action="{{ route('emc.core.links.destroy', $link) }}"
                                                                onsubmit="return confirm('Delete this link?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn--small btn--danger">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" style="color: var(--color-text-secondary);">No
                                                            links.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <h5>Add New Link</h5>
                                        <form method="POST" action="{{ route('emc.core.links.store') }}">
                                            @csrf
                                            <input type="hidden" name="core_database_id" value="{{ $db->id }}">
                                            <div class="form__group">
                                                <label class="form__label">Linked Connection Name</label>
                                                <input name="linked_connection_name" class="form__input"
                                                    list="connectionSuggestions" required>
                                            </div>
                                            <div class="form__group">
                                                <label class="form__label">Link Type</label>
                                                <input name="link_type" class="form__input"
                                                    placeholder="e.g., Primary, Replica, Policy" required>
                                            </div>
                                            <button type="submit" class="btn btn--primary">Add Link</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Modal for create/edit -->
            <div class="modal" id="coreDbModal" aria-hidden="true" role="dialog" aria-labelledby="coreDbModalTitle">
                <div class="modal__backdrop" onclick="closeModal('coreDbModal')"></div>
                <div class="modal__container" role="document">
                    <div class="modal__header">
                        <h3 class="modal__title" id="coreDbModalTitle">Add Core Database</h3>
                        <button class="modal__close" aria-label="Close" onclick="closeModal('coreDbModal')">×</button>
                    </div>
                    <div class="modal__body">
                        <form id="coreDbForm" method="POST" action="{{ route('emc.core.store') }}">
                            @csrf
                            <input type="hidden" id="coreDbForm__method" name="_method" value="POST" />
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                                <div class="form__group">
                                    <label class="form__label form__label--required">Name</label>
                                    <input name="name" class="form__input" required />
                                </div>
                                <div class="form__group">
                                    <label class="form__label form__label--required">Environment</label>
                                    <select name="environment" class="form__select" required>
                                        <option value="Production">Production</option>
                                        <option value="Staging">Staging</option>
                                        <option value="Development">Development</option>
                                    </select>
                                </div>
                                <div class="form__group">
                                    <label class="form__label form__label--required">Platform</label>
                                    <select name="platform" class="form__select" required>
                                        <option value="PostgreSQL">PostgreSQL</option>
                                        <option value="MySQL">MySQL</option>
                                        <option value="SQL Server">SQL Server</option>
                                    </select>
                                </div>
                                <div class="form__group">
                                    <label class="form__label">Owner</label>
                                    <input name="owner" class="form__input" placeholder="Team or Person" />
                                </div>
                                <div class="form__group">
                                    <label class="form__label">Lifecycle</label>
                                    <select name="lifecycle" class="form__select">
                                        <option value="Long-lived">Long-lived</option>
                                        <option value="Temporary">Temporary</option>
                                        <option value="Archived">Archived</option>
                                    </select>
                                </div>
                                <div class="form__group">
                                    <label class="form__label">Linked Connection</label>
                                    <input name="linked_connection" class="form__input"
                                        placeholder="database_connections.name" list="connectionSuggestions" />
                                    <datalist id="connectionSuggestions">
                                        <!-- Will be populated by JavaScript -->
                                    </datalist>
                                </div>
                            </div>
                            <div class="form__group">
                                <label class="form__label">Description</label>
                                <textarea name="description" class="form__textarea" rows="3" placeholder="Purpose, usage scope, SLAs..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal__footer">
                        <button class="btn" onclick="resetCoreDbForm(); closeModal('coreDbModal')">Cancel</button>
                        <button class="btn btn--primary"
                            onclick="document.getElementById('coreDbForm').submit()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const VC_STACK = [
            'Resource Extraction (Primary)',
            'Primary Processing (Materials)',
            'Secondary Manufacturing & Assembly',
            'Market Access, Trading & Wholesale',
            'Logistics, Ports & Fulfillment',
            'Retail & Direct-to-Consumer (Goods)',
            'Service Delivery (End-User Services)',
            'After-Sales, Reverse & End-of-Life'
        ];
        const STAGE_TO_VC_MAP = {
            'Resource Extraction (Primary)': ['Mining', 'Oil & Gas', 'Agriculture', 'Fisheries', 'Wood & Paper'],
            'Primary Processing (Materials)': ['Chemicals', 'Metals', 'Construction', 'Food & Beverage', 'Wood & Paper',
                'Plastics', 'Oil & Gas'
            ],
            'Secondary Manufacturing & Assembly': ['Automotive', 'Aerospace', 'Electronics', 'Pharmaceuticals',
                'Textiles & Apparel', 'Furniture', 'Plastics', 'Metals', 'Wood & Paper', 'Food & Beverage'
            ],
            'Market Access, Trading & Wholesale': ['Wholesale & Trading', 'Retail', 'Food & Beverage',
                'Pharmaceuticals'
            ],
            'Logistics, Ports & Fulfillment': ['Logistics', 'Maritime', 'Retail', 'Agriculture', 'Food & Beverage'],
            'Retail & Direct-to-Consumer (Goods)': ['Retail', 'Hospitality', 'Travel & Tourism'],
            'Service Delivery (End-User Services)': ['Healthcare', 'Education', 'Hospitality', 'Travel & Tourism',
                'Utilities', 'ITServices', 'Telecommunications'
            ],
            'After-Sales, Reverse & End-of-Life': ['Waste & Recycling', 'Automotive', 'Electronics', 'Healthcare',
                'Logistics'
            ]
        };
        const VC = {
            'Automotive': ['Vehicle Assembly', 'Auto Parts', 'EV Batteries'],
            'Aerospace': ['Aircraft Assembly', 'MRO', 'Avionics'],
            'Electronics': ['Semiconductors', 'Consumer Devices', 'Industrial IoT'],
            'Agriculture': ['Row Crops', 'Horticulture', 'Livestock'],
            'Fisheries': ['Aquaculture', 'Wild Capture', 'Cold Chain'],
            'Mining': ['Open Pit', 'Underground', 'Mineral Processing'],
            'Oil & Gas': ['Upstream', 'Midstream', 'Downstream'],
            'Chemicals': ['Basic Chemicals', 'Specialty', 'Fertilizers'],
            'Pharmaceuticals': ['APIs', 'Formulation', 'Distribution'],
            'Textiles & Apparel': ['Spinning', 'Weaving', 'Garments'],
            'Food & Beverage': ['Meat Processing', 'Dairy', 'Beverages'],
            'Construction': ['Cement', 'Building Materials', 'Contracting'],
            'Utilities': ['Power Generation', 'Transmission', 'Distribution'],
            'Logistics': ['Courier', 'Freight Forwarding', 'Warehousing', 'Air Cargo'],
            'Wholesale & Trading': ['Commodity Trading', 'Pharma Wholesale', 'B2B Marketplace'],
            'Retail': ['Grocery', 'General Merchandise', 'E-commerce'],
            'Hospitality': ['Hotels', 'Restaurants', 'Catering'],
            'Travel & Tourism': ['Airlines', 'Cruise', 'Tour Operators'],
            'Healthcare': ['Hospitals', 'Clinics', 'Pharmacies'],
            'Education': ['K-12', 'Universities', 'Vocational'],
            'Telecommunications': ['Mobile', 'Fixed Broadband', 'Data Centers'],
            'ITServices': ['Software Dev', 'Managed Services', 'Cloud'],
            'Waste & Recycling': ['Solid Waste', 'Recycling', 'E-waste'],
            'Metals': ['Steel', 'Non-ferrous', 'Foundry'],
            'Wood & Paper': ['Forestry', 'Pulp', 'Paper & Packaging'],
            'Plastics': ['Resins', 'Molding', 'Recycling'],
            'Maritime': ['Shipbuilding', 'Ports', 'Shipping Lines'],
            'Furniture': ['Residential', 'Office', 'Fixtures']
        };
        const CROSS_ENABLERS = ['Finance & Insurance', 'Payments', 'Standards & Certification', 'Legal & Compliance',
            'Data/IT & Cybersecurity', 'Telecom', 'Energy & Utilities', 'Workforce & Training', 'R&D & Design',
            'ESG & Reporting'
        ];

        function slug(s) {
            return String(s || '').toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
        }

        function setSelectOptions(selectEl, options, placeholder) {
            if (!selectEl) return;
            selectEl.innerHTML = '';
            if (placeholder) {
                const op = document.createElement('option');
                op.value = '';
                op.textContent = placeholder;
                op.disabled = true;
                op.selected = true;
                selectEl.appendChild(op);
            }
            options.forEach(v => {
                const o = document.createElement('option');
                o.value = v;
                o.textContent = v;
                selectEl.appendChild(o);
            });
        }

        function scopesFromStage(stage) {
            const base = new Set();
            switch (stage) {
                case 'Resource Extraction (Primary)':
                    ['Procurement', 'Logistics', 'HRM', 'Compliance', 'Analytics'].forEach(s => base.add(s));
                    break;
                case 'Primary Processing (Materials)':
                    ['Manufacturing', 'Inventory', 'Compliance', 'HRM', 'Logistics', 'Analytics'].forEach(s => base.add(s));
                    break;
                case 'Secondary Manufacturing & Assembly':
                    ['Manufacturing', 'Procurement', 'Inventory', 'Compliance', 'Analytics', 'HRM', 'Logistics'].forEach(
                        s => base.add(s));
                    break;
                case 'Market Access, Trading & Wholesale':
                    ['Sales', 'Inventory', 'Accounting', 'Compliance', 'Analytics', 'Logistics'].forEach(s => base.add(s));
                    break;
                case 'Logistics, Ports & Fulfillment':
                    ['Logistics', 'Inventory', 'Compliance', 'Analytics'].forEach(s => base.add(s));
                    break;
                case 'Retail & Direct-to-Consumer (Goods)':
                    ['Sales', 'Inventory', 'Accounting', 'Logistics', 'Communications', 'Analytics'].forEach(s => base.add(
                        s));
                    break;
                case 'Service Delivery (End-User Services)':
                    ['HRM', 'Communications', 'Inventory', 'Accounting', 'Compliance', 'Analytics'].forEach(s => base.add(
                        s));
                    break;
                case 'After-Sales, Reverse & End-of-Life':
                    ['Logistics', 'Inventory', 'Compliance', 'Analytics', 'Communications'].forEach(s => base.add(s));
                    break;
            }
            return Array.from(base);
        }

        function scopesFromEnablers(enablers) {
            const base = new Set();
            (enablers || []).forEach(e => {
                if (e === 'Finance & Insurance') {
                    base.add('FinanceSpecific');
                    base.add('Accounting');
                }
                if (e === 'Payments') {
                    base.add('FinanceSpecific');
                    base.add('Sales');
                }
                if (e === 'Standards & Certification') {
                    base.add('Compliance');
                }
                if (e === 'Legal & Compliance') {
                    base.add('Compliance');
                }
                if (e === 'Data/IT & Cybersecurity') {
                    base.add('Analytics');
                    base.add('Compliance');
                }
                if (e === 'Telecom') {
                    base.add('Communications');
                }
                if (e === 'Energy & Utilities') {
                    base.add('Manufacturing');
                }
                if (e === 'Workforce & Training') {
                    base.add('HRM');
                }
                if (e === 'R&D & Design') {
                    base.add('Manufacturing');
                    base.add('Analytics');
                }
                if (e === 'ESG & Reporting') {
                    base.add('Compliance');
                    base.add('Analytics');
                }
            });
            return Array.from(base);
        }

        function scopeSuggest(tier, sub, stage, enablers) {
            const base = new Set();
            if (tier === 'Value Chain') {
                ['Accounting', 'Inventory', 'Manufacturing', 'Logistics', 'HRM', 'Compliance', 'Analytics'].forEach(s =>
                    base.add(s));
                if ((sub || '').includes('Retail')) base.add('Sales');
            }
            if (tier === 'Public Goods & Governance') {
                ['Inventory', 'Logistics', 'Compliance', 'Analytics', 'Communications'].forEach(s => base.add(s));
            }
            if (tier === 'CSO') {
                ['Accounting', 'HRM', 'Communications', 'Compliance', 'Analytics'].forEach(s => base.add(s));
            }
            if (tier === 'Media') {
                ['MediaSpecific', 'Sales', 'Analytics', 'Compliance', 'Communications'].forEach(s => base.add(s));
            }
            if (tier === 'Financial') {
                ['FinanceSpecific', 'Compliance', 'Analytics', 'Accounting', 'Sales', 'HRM'].forEach(s => base.add(s));
            }
            scopesFromStage(stage).forEach(s => base.add(s));
            scopesFromEnablers(enablers).forEach(s => base.add(s));
            return Array.from(base);
        }

        function refreshCoreDbs() {
            location.reload();
        }

        function setSelectValue(selectEl, value) {
            if (!selectEl) return;
            for (const o of selectEl.options) {
                if (o.value === value) {
                    o.selected = true;
                    break;
                }
            }
        }

        function populateConnectionSuggestions() {
            // Fetch database connections for auto-suggest
            fetch('/api/v1/companies/1/database-connections', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.ok ? response.json() : null)
                .then(data => {
                    const datalist = document.getElementById('connectionSuggestions');
                    if (data && data.data && datalist) {
                        datalist.innerHTML = '';
                        data.data.forEach(conn => {
                            const option = document.createElement('option');
                            option.value = conn.name || conn.id;
                            datalist.appendChild(option);
                        });
                    }
                })
                .catch(() => {}); // Silent fail if API not available
        }

        function editCoreDb(row) {
            const form = document.getElementById('coreDbForm');
            const title = document.getElementById('coreDbModalTitle');
            const method = document.getElementById('coreDbForm__method');
            if (!row || !row.id) {
                return;
            }
            title.textContent = 'Edit Core Database';
            form.action = '{{ route('emc.core.index') }}' + '/' + row.id;
            method.value = 'PATCH';
            form.querySelector('[name="name"]').value = row.name || '';
            setSelectValue(form.querySelector('[name="environment"]'), row.environment || '');
            setSelectValue(form.querySelector('[name="platform"]'), row.platform || '');
            form.querySelector('[name="owner"]').value = row.owner || '';
            setSelectValue(form.querySelector('[name="lifecycle"]'), row.lifecycle || '');
            form.querySelector('[name="linked_connection"]').value = row.linked_connection || '';
            form.querySelector('[name="description"]').value = row.description || '';
            openModal('coreDbModal');
        }

        function resetCoreDbForm() {
            const form = document.getElementById('coreDbForm');
            const method = document.getElementById('coreDbForm__method');
            const title = document.getElementById('coreDbModalTitle');
            form.action = '{{ route('emc.core.store') }}';
            method.value = 'POST';
            title.textContent = 'Add Core Database';
            form.reset();
        }

        // Quick View controls
        function openQuickView(ev, row) {
            if (ev && ev.stopPropagation) ev.stopPropagation();
            // Remember trigger to restore focus on close
            window.__qvLastTrigger = ev && ev.currentTarget ? ev.currentTarget : null;
            const wrap = document.getElementById('quickviewWrapper');
            const pane = document.getElementById('quickview');
            const title = document.getElementById('qvTitle');
            const body = document.getElementById('qvBody');
            const link = document.getElementById('qvOpenLink');
            if (!wrap || !pane || !title || !body) return;
            document.documentElement.classList.add('quickview--open');
            const wrapEl = document.getElementById('quickviewWrapper');
            if (wrapEl) wrapEl.setAttribute('aria-hidden', 'false');
            title.textContent = row?.name || 'Details';
            const lc = row?.linked_connection;
            let linkHref = '#';
            if (lc && typeof lc === 'string') {
                linkHref = '/emc/db?connection_name=' + encodeURIComponent(lc);
            }
            if (link) link.href = linkHref;
            body.innerHTML = `
                                <div class="sub-section">
                                    <div class="wizard-grid-2">
                                        <div><div class="text-muted">Environment</div><div>${row?.environment ?? '—'}</div></div>
                                        <div><div class="text-muted">Platform</div><div>${row?.platform ?? '—'}</div></div>
                                        <div><div class="text-muted">Owner</div><div>${row?.owner ?? '—'}</div></div>
                                        <div><div class="text-muted">Lifecycle</div><div>${row?.lifecycle ?? '—'}</div></div>
                                    </div>
                                </div>
                                <div class="sub-section">
                                    <div class="text-muted">Description</div>
                                    <div>${(row?.description ?? '').toString().trim() || '—'}</div>
                                </div>
                        `;
            // Focus trap: move focus to close button and trap Tab cycles within the panel
            const closeBtn = document.getElementById('qvClose');
            const primaryClose = document.getElementById('qvPrimaryClose');
            const focusables = [closeBtn, document.getElementById('qvOpenLink'), primaryClose].filter(Boolean);
            if (closeBtn) closeBtn.focus();
            // Remove previous handler if any
            if (window.__qvKeyHandler) {
                pane.removeEventListener('keydown', window.__qvKeyHandler);
            }
            window.__qvKeyHandler = function(e) {
                if (e.key === 'Tab' && focusables.length) {
                    const idx = focusables.indexOf(document.activeElement);
                    if (e.shiftKey) {
                        if (idx <= 0) {
                            e.preventDefault();
                            focusables[focusables.length - 1].focus();
                        }
                    } else {
                        if (idx === -1 || idx >= focusables.length - 1) {
                            e.preventDefault();
                            focusables[0].focus();
                        }
                    }
                }
            };
            pane.addEventListener('keydown', window.__qvKeyHandler);
        }

        function closeQuickView() {
            document.documentElement.classList.remove('quickview--open');
            const wrap = document.getElementById('quickviewWrapper');
            if (wrap) wrap.setAttribute('aria-hidden', 'true');
            // Remove key handler
            const pane = document.getElementById('quickview');
            if (pane && window.__qvKeyHandler) {
                pane.removeEventListener('keydown', window.__qvKeyHandler);
                window.__qvKeyHandler = null;
            }
            // Restore focus to the last trigger if available
            if (window.__qvLastTrigger && typeof window.__qvLastTrigger.focus === 'function') {
                window.__qvLastTrigger.focus();
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            populateConnectionSuggestions();
            // ESC to close quick view
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeQuickView();
                }
            });
            // Debounced search input submit
            const searchInput = document.querySelector('input[name="q"]');
            if (searchInput) {
                let t;
                searchInput.addEventListener('input', function() {
                    clearTimeout(t);
                    t = setTimeout(() => {
                        const form = this.closest('form');
                        if (form) form.requestSubmit();
                    }, 400);
                });
            }
            // Persist sort selection in localStorage
            const sortSel = document.querySelector('select[name="sort"]');
            const dirSel = document.querySelector('select[name="direction"]');
            if (sortSel && dirSel) {
                const key = 'emc.core.sort';
                try {
                    const saved = JSON.parse(localStorage.getItem(key) || '{}');
                    if (saved.sort) sortSel.value = saved.sort;
                    if (saved.dir) dirSel.value = saved.dir;
                } catch {}
                const saveSort = () => {
                    try {
                        localStorage.setItem(key, JSON.stringify({
                            sort: sortSel.value,
                            dir: dirSel.value
                        }));
                    } catch {}
                };
                sortSel.addEventListener('change', saveSort);
                dirSel.addEventListener('change', saveSort);
            }
            // Populate Stage/Industry/Subindustry
            const stageSel = document.getElementById('w_stage');
            const indSel = document.getElementById('w_industry');
            const subSel = document.getElementById('w_subindustry');
            setSelectOptions(stageSel, VC_STACK, 'Pick a stage');
            if (stageSel) {
                stageSel.addEventListener('change', function() {
                    const stage = this.value;
                    const inds = STAGE_TO_VC_MAP[stage] || [];
                    setSelectOptions(indSel, inds, 'Choose industry');
                    if (indSel) {
                        indSel.disabled = inds.length === 0;
                    }
                    if (subSel) {
                        subSel.disabled = true;
                        setSelectOptions(subSel, [], 'Choose subindustry');
                    }
                    updateSuggestedText();
                });
            }
            if (indSel) {
                indSel.addEventListener('change', function() {
                    const subs = VC[this.value] || [];
                    setSelectOptions(subSel, subs, 'Choose subindustry');
                    if (subSel) {
                        subSel.disabled = subs.length === 0;
                    }
                    updateSuggestedText();
                });
            }
            if (subSel) {
                subSel.addEventListener('change', updateSuggestedText);
            }
            const tierSel = document.getElementById('w_tier');
            if (tierSel) {
                tierSel.addEventListener('change', updateSuggestedText);
            }
            // Cross Enablers
            const enWrap = document.getElementById('w_enablers');
            if (enWrap) {
                CROSS_ENABLERS.forEach(e => {
                    const label = document.createElement('label');
                    label.className = 'enabler';
                    const cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.value = e;
                    cb.addEventListener('change', updateSuggestedText);
                    const span = document.createElement('span');
                    span.textContent = e;
                    label.appendChild(cb);
                    label.appendChild(span);
                    enWrap.appendChild(label);
                });
            }
            updateSuggestedText();
        });
    </script>
    <script>
        function buildPath(tier, stage, industry, subindustry, extras = {}) {
            switch (tier) {
                case 'Value Chain':
                    return `Value Chain → ${stage || 'Stage'} → ${industry || 'Industry'} → ${subindustry || 'Subindustry'}`;
                case 'Public Goods & Governance':
                    return `Public Goods → ${extras.public_good || 'PG'} → ${extras.lead_org || 'Org'} → ${extras.program || 'Program'}`;
                case 'CSO':
                    return `CSO → ${extras.cso_super || 'Super'} → ${extras.cso_type || 'Type'}`;
                case 'Media':
                    return `Media → ${extras.media_sector || 'Sector'} → ${extras.media_subsector || 'Subsector'} → ${extras.media_channel || 'Channel'}`;
                case 'Financial':
                    return `Financial → ${extras.fin_sector || 'Sector'} → ${extras.fin_subsector || 'Subsector'} → ${extras.institution || 'Institution'}`;
                default:
                    return tier || '';
            }
        }

        function suggestNameByTier(tier, stage, industry, subindustry, extras = {}) {
            if (tier === 'Value Chain' && stage && industry && subindustry)
                return `${slug(stage)}_${slug(industry)}_${slug(subindustry)}`;
            if (tier === 'Public Goods & Governance' && extras.public_good && extras.program)
                return `${slug(extras.public_good)}_${slug(extras.program)}`;
            if (tier === 'Media' && extras.media_sector && extras.media_subsector)
                return `${slug(extras.media_sector)}_${slug(extras.media_subsector)}`;
            if (tier === 'Financial' && extras.fin_sector && extras.fin_subsector)
                return `${slug(extras.fin_sector)}_${slug(extras.fin_subsector)}`;
            if (tier === 'CSO' && extras.cso_super && extras.cso_type)
                return `${slug((extras.cso_super||'').split(' ')[0])}_${slug(extras.cso_type)}`;
            return 'new_database';
        }

        function updateSuggestedText() {
            const tier = document.getElementById('w_tier').value;
            const stage = document.getElementById('w_stage').value;
            const ind = document.getElementById('w_industry').value;
            const sub = document.getElementById('w_subindustry').value;
            const enablers = Array.from(document.querySelectorAll('#w_enablers input[type=checkbox]:checked')).map(cb => cb
                .value);
            const suggested = scopeSuggest(tier, sub, stage, enablers);
            const el = document.getElementById('w_suggested');
            if (el) {
                el.textContent = 'Suggested scopes: ' + (suggested.length ? suggested.join(' • ') : '(none)');
            }
            return suggested;
        }

        function applySuggestedScopes() {
            const suggested = updateSuggestedText();
            const cbs = Array.from(document.querySelectorAll('#w_scopes_group .w_scope'));
            cbs.forEach(cb => {
                if (suggested.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        }

        function clearScopes() {
            const cbs = Array.from(document.querySelectorAll('#w_scopes_group .w_scope'));
            cbs.forEach(cb => cb.checked = false);
            updateSuggestedText();
        }

        function selectedWizardScopes() {
            const cbs = Array.from(document.querySelectorAll('#w_scopes_group .w_scope'));
            const chosen = cbs.filter(cb => cb.checked).map(cb => cb.value);
            // fallback: allow comma input if present (older markup)
            const textEl = document.getElementById('w_scopes');
            if (chosen.length === 0 && textEl) {
                return (textEl.value || '').split(',').map(s => s.trim()).filter(Boolean);
            }
            return chosen;
        }

        async function previewDDL() {
            const engine = document.getElementById('w_engine').value;
            const scopes = selectedWizardScopes();
            const body = new URLSearchParams();
            body.set('engine', engine);
            scopes.forEach(s => body.append('functional_scopes[]', s));
            const res = await fetch("{{ route('emc.core.ddl') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body
            });
            const sql = await res.text();
            const code = document.getElementById('w_ddl');
            code.textContent = sql;
            if (window.PrismHighlight) {
                window.PrismHighlight(code);
            }
        }

        function downloadDDL() {
            // Reuse preview but force download by opening in new window
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('emc.core.ddl') }}";
            form.target = '_blank';
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);
            const engine = document.getElementById('w_engine').value;
            const scopes = selectedWizardScopes();
            const e = document.createElement('input');
            e.type = 'hidden';
            e.name = 'engine';
            e.value = engine;
            form.appendChild(e);
            scopes.forEach(s => {
                const i = document.createElement('input');
                i.type = 'hidden';
                i.name = 'functional_scopes[]';
                i.value = s;
                form.appendChild(i);
            });
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function copyDDL() {
            const text = document.getElementById('w_ddl').textContent || '';
            if (!navigator.clipboard) {
                // fallback
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                return;
            }
            navigator.clipboard.writeText(text);
        }
        async function submitWizard(e) {
            e.preventDefault();
            const tier = document.getElementById('w_tier').value;
            const stage = document.getElementById('w_stage').value;
            const industry = document.getElementById('w_industry').value;
            const subindustry = document.getElementById('w_subindustry').value;
            const scopes = selectedWizardScopes();
            const name = document.getElementById('w_name').value || suggestNameByTier(tier, stage, industry,
                subindustry, {});
            const owner_email = document.getElementById('w_owner_email').value || 'owner@example.com';
            const engine = document.getElementById('w_engine').value;
            const env = document.getElementById('w_env').value;
            const tax_path = buildPath(tier, stage, industry, subindustry, {});
            const body = new URLSearchParams();
            body.set('name', name);
            body.set('owner_email', owner_email);
            body.set('tier', tier);
            body.set('tax_path', tax_path);
            body.set('vc_stage', stage);
            body.set('vc_industry', industry);
            body.set('vc_subindustry', subindustry);
            body.set('engine', engine);
            body.set('env', env);
            scopes.forEach(s => body.append('functional_scopes[]', s));
            const res = await fetch("{{ route('emc.core.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body
            });
            if (res.ok) {
                window.location = "{{ route('emc.core.index') }}?tab=registry";
            }
            return false;
        }
    </script>
    @push('scripts')
        <!-- Prism is loaded via app.js bundle -->
        <script>
            // Saved Views (persisted via API with localStorage fallback)
            const SAVED_VIEWS_KEY = 'emc.core.savedViews.v1';
            const SAVED_VIEWS_API = "{{ route('emc.core.saved-views.index') }}";
            const SV_STATE = { offset: 0, limit: 10, total: 0, next: null, prev: null };

            function parseLinkHeader(linkHeader) {
                // Parse RFC 5988 Link header into { rel: url }
                const out = {};
                if (!linkHeader) return out;
                linkHeader.split(',').forEach(part => {
                    const m = part.match(/<([^>]+)>;\s*rel="([^"]+)"/);
                    if (m) out[m[2]] = m[1];
                });
                return out;
            }

            async function apiList(params = {}) {
                try {
                    const p = new URLSearchParams();
                    const limit = params.limit ?? SV_STATE.limit;
                    const offset = params.offset ?? SV_STATE.offset;
                    p.set('limit', String(limit));
                    p.set('offset', String(offset));
                    const r = await fetch(`${SAVED_VIEWS_API}?${p.toString()}`);
                    if (!r.ok) throw new Error();
                    const data = await r.json();
                    // update pagination state from headers
                    SV_STATE.total = Number(r.headers.get('X-SavedViews-Total') || '0');
                    SV_STATE.limit = Number(r.headers.get('X-SavedViews-Limit') || String(limit));
                    const links = parseLinkHeader(r.headers.get('Link'));
                    SV_STATE.next = links.next || null;
                    SV_STATE.prev = links.prev || null;
                    // try to derive offset from Link URLs if present
                    const derive = (url) => {
                        if (!url) return null;
                        try {
                            const u = new URL(url, window.location.origin);
                            return Number(u.searchParams.get('offset') || '0');
                        } catch { return null; }
                    };
                    SV_STATE.nextOffset = derive(SV_STATE.next);
                    SV_STATE.prevOffset = derive(SV_STATE.prev);
                    return data;
                } catch {
                    return null;
                }
            }
            async function apiSave(view) {
                try {
                    const r = await fetch(SAVED_VIEWS_API, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(view)
                    });
                    if (!r.ok) throw new Error();
                    return await r.json();
                } catch {
                    return null;
                }
            }
            async function apiDelete(id) {
                try {
                    await fetch(`${SAVED_VIEWS_API}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                } catch {}
            }

            function lsLoad() {
                try {
                    return JSON.parse(localStorage.getItem(SAVED_VIEWS_KEY) || '[]');
                } catch {
                    return [];
                }
            }

            function lsSave(list) {
                try {
                    localStorage.setItem(SAVED_VIEWS_KEY, JSON.stringify(list));
                } catch {}
            }
            async function loadSavedViews() {
                const api = await apiList({ offset: SV_STATE.offset, limit: SV_STATE.limit });
                return api ?? lsLoad();
            }
            async function saveSavedView(entry) {
                const api = await apiSave(entry);
                if (api) return api;
                // fallback merge
                const list = lsLoad();
                const existing = list.findIndex(v => v.name === entry.name);
                if (existing >= 0) list[existing] = entry;
                else list.push(entry);
                lsSave(list);
                return entry;
            }
            async function deleteSavedView(entry) {
                if (entry.id) await apiDelete(entry.id);
                else {
                    const list = lsLoad();
                    const idx = list.findIndex(v => v.name === entry.name);
                    if (idx >= 0) {
                        list.splice(idx, 1);
                        lsSave(list);
                    }
                }
            }

            function currentFiltersFromForm(form) {
                const data = new FormData(form);
                const obj = {};
                for (const [k, v] of data.entries()) {
                    if (k.endsWith('[]')) {
                        const key = k.slice(0, -2);
                        obj[key] = obj[key] || [];
                        obj[key].push(v);
                    } else {
                        obj[k] = v;
                    }
                }
                return obj;
            }

            async function renderSavedViews(container, form) {
                const views = await loadSavedViews();
                container.innerHTML = '';
                views.forEach((v) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn--tiny btn--secondary';
                    btn.textContent = v.name;
                    btn.title = 'Apply saved view';
                    btn.addEventListener('click', () => {
                        // apply filters to form inputs then submit
                        Object.entries(v.filters || {}).forEach(([k, val]) => {
                            const input = form.querySelector(`[name="${k}"]`);
                            const multi = form.querySelectorAll(`[name="${k}[]"]`);
                            if (multi && multi.length) {
                                // scopes[] etc.
                                multi.forEach(cb => {
                                    cb.checked = Array.isArray(val) ? val.includes(cb
                                            .value) :
                                        false;
                                });
                            } else if (input) {
                                input.value = val;
                            }
                        });
                        form.requestSubmit();
                    });
                    const del = document.createElement('button');
                    del.type = 'button';
                    del.className = 'btn btn--tiny';
                    del.textContent = '×';
                    del.title = 'Delete saved view';
                    del.addEventListener('click', async () => {
                        await deleteSavedView(v);
                        await renderSavedViews(container, form);
                    });
                    const wrap = document.createElement('span');
                    wrap.className = 'flex gap-2';
                    wrap.appendChild(btn);
                    wrap.appendChild(del);
                    container.appendChild(wrap);
                });
                // update pager UI
                const meta = document.getElementById('svMeta');
                const prevBtn = document.getElementById('svPrev');
                const nextBtn = document.getElementById('svNext');
                if (meta) meta.textContent = `${Math.min(SV_STATE.offset + 1, SV_STATE.total)}-${Math.min(SV_STATE.offset + views.length, SV_STATE.total)} of ${SV_STATE.total}`;
                if (prevBtn) {
                    prevBtn.disabled = !SV_STATE.prev;
                    prevBtn.onclick = async () => {
                        if (!SV_STATE.prev) return;
                        SV_STATE.offset = SV_STATE.prevOffset ?? Math.max(0, SV_STATE.offset - SV_STATE.limit);
                        await renderSavedViews(container, form);
                    };
                }
                if (nextBtn) {
                    nextBtn.disabled = !SV_STATE.next;
                    nextBtn.onclick = async () => {
                        if (!SV_STATE.next) return;
                        SV_STATE.offset = SV_STATE.nextOffset ?? (SV_STATE.offset + SV_STATE.limit);
                        await renderSavedViews(container, form);
                    };
                }
            }
            document.addEventListener('DOMContentLoaded', function() {
                // Clickable sortable headers (also keyboard accessible)
                const form = document.getElementById('filtersForm') || document.querySelector(
                    'form[action*="emc/core"]');
                const sortSel = form?.querySelector('select[name="sort"]');
                const dirSel = form?.querySelector('select[name="direction"]');
                document.querySelectorAll('.table__header-cell--sortable').forEach(th => {
                    const key = th.getAttribute('data-sort');
                    const handler = () => {
                        if (!form || !sortSel || !dirSel) return;
                        if (sortSel.value === key) {
                            dirSel.value = (dirSel.value === 'asc') ? 'desc' : 'asc';
                        } else {
                            sortSel.value = key;
                            dirSel.value = 'asc';
                        }
                        form.requestSubmit();
                    };
                    th.addEventListener('click', handler);
                    th.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            handler();
                        }
                    });
                });

                // Saved views
                const container = document.getElementById('savedViewsManaged');
                const saveBtn = document.getElementById('btnSaveView');
                if (container && form) renderSavedViews(container, form);
                if (saveBtn && form) {
                    saveBtn.addEventListener('click', async () => {
                        const name = prompt('Name this view');
                        if (!name) return;
                        const filters = currentFiltersFromForm(form);
                        await saveSavedView({
                            name,
                            context: 'core_databases',
                            filters
                        });
                        await renderSavedViews(container, form);
                    });
                }
            });
        </script>
    @endpush
@endsection
