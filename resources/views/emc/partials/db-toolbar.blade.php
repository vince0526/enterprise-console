@php
    $submenu = request()->get('submenu', 'connections');
    $submenuTitles = [
        'connections' => 'Connections',
        'backup' => 'Backup & Restore',
        'performance' => 'Performance Monitor',
        'query' => 'Query Runner',
        'replication' => 'Replication & Clustering'
    ];
    $currentTitle = $submenuTitles[$submenu] ?? 'Database Management';
@endphp

<div class="db-toolbar">
    <div class="db-toolbar__breadcrumb">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb__item">
                    <a href="{{ route('emc.index') }}" class="breadcrumb__link">EMC</a>
                </li>
                <li class="breadcrumb__item">
                    <a href="{{ route('emc.db') }}" class="breadcrumb__link">Database Management</a>
                </li>
                <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
                    {{ $currentTitle }}
                </li>
            </ol>
        </nav>
    </div>
    
    <div class="db-toolbar__actions">
        <div class="db-toolbar__status">
            @switch($submenu)
                @case('connections')
                    <span class="status-indicator status-indicator--success" title="All connections healthy">
                        <span class="status-indicator__dot"></span>
                        3 Active Connections
                    </span>
                    @break
                @case('backup')
                    <span class="status-indicator status-indicator--info" title="Last backup completed">
                        <span class="status-indicator__dot"></span>
                        Last backup: {{ now()->subHours(2)->format('H:i') }}
                    </span>
                    @break
                @case('performance')
                    <span class="status-indicator status-indicator--warning" title="Performance monitoring active">
                        <span class="status-indicator__dot"></span>
                        Monitoring active
                    </span>
                    @break
                @case('query')
                    <span class="status-indicator status-indicator--info" title="Query environment ready">
                        <span class="status-indicator__dot"></span>
                        Ready to execute
                    </span>
                    @break
                @case('replication')
                    <span class="status-indicator status-indicator--success" title="All clusters healthy">
                        <span class="status-indicator__dot"></span>
                        3 Clusters healthy
                    </span>
                    @break
            @endswitch
        </div>
        
        <div class="db-toolbar__buttons">
            @switch($submenu)
                @case('connections')
                    <button class="btn btn--primary btn--sm" onclick="openModal('addConnectionModal')" title="Add new database connection">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add Connection
                    </button>
                    <button class="btn btn--secondary btn--sm" onclick="refreshConnections()" title="Refresh connection status">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <polyline points="1 20 1 14 7 14"></polyline>
                            <path d="m3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                        Refresh
                    </button>
                    @break
                @case('backup')
                    <button class="btn btn--primary btn--sm" onclick="openModal('createBackupModal')" title="Create new backup">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                        </svg>
                        Create Backup
                    </button>
                    <button class="btn btn--secondary btn--sm" onclick="openModal('scheduleBackupModal')" title="Schedule automated backups">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Schedule
                    </button>
                    @break
                @case('performance')
                    <button class="btn btn--primary btn--sm" onclick="openModal('alertConfigModal')" title="Configure performance alerts">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        Configure Alerts
                    </button>
                    <button class="btn btn--secondary btn--sm" onclick="exportPerformanceReport()" title="Export performance report">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Export Report
                    </button>
                    @break
                @case('query')
                    <button class="btn btn--primary btn--sm" onclick="executeQuery()" title="Execute query (Ctrl+Enter)">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                        Execute
                    </button>
                    <button class="btn btn--secondary btn--sm" onclick="saveQuery()" title="Save current query">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Save
                    </button>
                    @break
                @case('replication')
                    <button class="btn btn--primary btn--sm" onclick="openModal('createClusterModal')" title="Create new cluster">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Create Cluster
                    </button>
                    <button class="btn btn--secondary btn--sm" onclick="openModal('replicationSetupModal')" title="Setup database replication">
                        <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path>
                        </svg>
                        Setup Replication
                    </button>
                    @break
            @endswitch
            
            <div class="db-toolbar__help">
                <button class="btn btn--ghost btn--sm" onclick="showHelp('{{ $submenu }}')" title="Get help for {{ $currentTitle }}">
                    <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.db-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: var(--color-background-secondary);
    border-bottom: 1px solid var(--color-border-primary);
    margin-bottom: 1.5rem;
}

.db-toolbar__breadcrumb {
    flex: 1;
}

.breadcrumb {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 0.875rem;
}

.breadcrumb__item {
    display: flex;
    align-items: center;
}

.breadcrumb__item:not(:last-child)::after {
    content: '/';
    margin: 0 0.5rem;
    color: var(--color-text-tertiary);
}

.breadcrumb__link {
    color: var(--color-text-secondary);
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb__link:hover {
    color: var(--color-primary);
}

.breadcrumb__item--current {
    color: var(--color-text-primary);
    font-weight: 500;
}

.db-toolbar__actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.db-toolbar__status {
    display: flex;
    align-items: center;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--color-text-secondary);
}

.status-indicator__dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.status-indicator--success {
    color: var(--color-success);
}

.status-indicator--warning {
    color: var(--color-warning);
}

.status-indicator--error {
    color: var(--color-danger);
}

.status-indicator--info {
    color: var(--color-info);
}

.db-toolbar__buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.db-toolbar__help {
    margin-left: 0.5rem;
}

.btn__icon {
    width: 16px;
    height: 16px;
    margin-right: 0.25rem;
}

.btn--sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.btn--ghost {
    background: transparent;
    border: 1px solid transparent;
    color: var(--color-text-secondary);
}

.btn--ghost:hover {
    background: var(--color-background-tertiary);
    color: var(--color-text-primary);
}

@media (max-width: 768px) {
    .db-toolbar {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
        padding: 1rem;
    }
    
    .db-toolbar__actions {
        justify-content: space-between;
        flex-wrap: wrap;
    }
    
    .db-toolbar__buttons {
        flex-wrap: wrap;
    }
    
    .btn--sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    .btn__icon {
        width: 14px;
        height: 14px;
    }
}
</style>

<script>
function showHelp(submenu) {
    const helpContent = {
        connections: 'Manage database connections, test connectivity, and configure connection settings for your enterprise databases.',
        backup: 'Create, schedule, and manage database backups. Configure automated backup policies and restoration points.',
        performance: 'Monitor database performance metrics, analyze slow queries, and configure performance alerts.',
        query: 'Execute SQL queries with syntax highlighting, save frequently used queries, and export results.',
        replication: 'Configure database replication, manage clusters, and monitor high availability setups.'
    };
    
    alert(helpContent[submenu] || 'Help information for this section.');
}

function exportPerformanceReport() {
    alert('Exporting performance report...');
    // Implement actual export functionality
}
</script>