@extends('emc.layout')

@section('title', 'Database Management - EMC')

@section('content')
<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Database Connections</h1>
        <span class="status status--info">
            <span>●</span>
            {{ $connections ?? 0 }} Active Connections
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="openModal('addConnectionModal')">
            <span>+</span>
            Add Connection
        </button>
        <button class="btn btn--secondary" onclick="refreshConnections()">
            <span>↻</span>
            Refresh
        </button>
    </div>
</div>

<div class="card">
    <div class="card__header">
        <h2 class="card__title">Database Connections</h2>
        <p class="card__subtitle">Manage database connections and view relational data</p>
    </div>
    <div class="card__content">
        <div class="table-container">
            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th class="table__header-cell">Name</th>
                        <th class="table__header-cell">Type</th>
                        <th class="table__header-cell">Host</th>
                        <th class="table__header-cell">Database</th>
                        <th class="table__header-cell">Status</th>
                        <th class="table__header-cell">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table__row table__row--clickable" onclick="viewConnection(1)">
                        <td class="table__cell">Main Production DB</td>
                        <td class="table__cell">MySQL</td>
                        <td class="table__cell">prod-db.company.com</td>
                        <td class="table__cell">emc_production</td>
                        <td class="table__cell">
                            <span class="status status--success">
                                <span>●</span>
                                Connected
                            </span>
                        </td>
                        <td class="table__cell">
                            <div class="table__actions">
                                <button class="btn btn--small btn--secondary" onclick="editConnection(1, event)">Edit</button>
                                <button class="btn btn--small btn--secondary" onclick="testConnection(1, event)">Test</button>
                                <button class="btn btn--small btn--danger" onclick="deleteConnection(1, event)">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table__row table__row--clickable" onclick="viewConnection(2)">
                        <td class="table__cell">Development DB</td>
                        <td class="table__cell">PostgreSQL</td>
                        <td class="table__cell">dev-db.company.com</td>
                        <td class="table__cell">emc_development</td>
                        <td class="table__cell">
                            <span class="status status--warning">
                                <span>●</span>
                                Slow
                            </span>
                        </td>
                        <td class="table__cell">
                            <div class="table__actions">
                                <button class="btn btn--small btn--secondary" onclick="editConnection(2, event)">Edit</button>
                                <button class="btn btn--small btn--secondary" onclick="testConnection(2, event)">Test</button>
                                <button class="btn btn--small btn--danger" onclick="deleteConnection(2, event)">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table__row table__row--clickable" onclick="viewConnection(3)">
                        <td class="table__cell">Analytics DB</td>
                        <td class="table__cell">MongoDB</td>
                        <td class="table__cell">analytics.company.com</td>
                        <td class="table__cell">analytics</td>
                        <td class="table__cell">
                            <span class="status status--error">
                                <span>●</span>
                                Disconnected
                            </span>
                        </td>
                        <td class="table__cell">
                            <div class="table__actions">
                                <button class="btn btn--small btn--secondary" onclick="editConnection(3, event)">Edit</button>
                                <button class="btn btn--small btn--secondary" onclick="testConnection(3, event)">Test</button>
                                <button class="btn btn--small btn--danger" onclick="deleteConnection(3, event)">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Connection Modal -->
<div class="modal" id="addConnectionModal">
    <div class="modal__backdrop" onclick="closeModal('addConnectionModal')"></div>
    <div class="modal__container" style="width: 600px;">
        <div class="modal__header">
            <h3 class="modal__title">Add Database Connection</h3>
            <button class="modal__close" onclick="closeModal('addConnectionModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="connectionForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="connectionName">Connection Name</label>
                    <input type="text" id="connectionName" class="form__input" placeholder="Enter connection name" required>
                    <div class="form__help">A friendly name to identify this connection</div>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="connectionType">Database Type</label>
                    <select id="connectionType" class="form__select" required>
                        <option value="">Select database type</option>
                        <option value="mysql">MySQL</option>
                        <option value="postgresql">PostgreSQL</option>
                        <option value="mssql">Microsoft SQL Server</option>
                        <option value="mongodb">MongoDB</option>
                        <option value="oracle">Oracle</option>
                        <option value="sqlite">SQLite</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="connectionHost">Host</label>
                    <input type="text" id="connectionHost" class="form__input" placeholder="localhost or IP address" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 120px; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label" for="connectionPort">Port</label>
                        <input type="number" id="connectionPort" class="form__input" placeholder="3306">
                    </div>
                    <div class="form__group">
                        <label class="form__label form__label--required" for="connectionDatabase">Database</label>
                        <input type="text" id="connectionDatabase" class="form__input" placeholder="database_name" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="connectionUsername">Username</label>
                        <input type="text" id="connectionUsername" class="form__input" placeholder="database_user" required>
                    </div>
                    <div class="form__group">
                        <label class="form__label form__label--required" for="connectionPassword">Password</label>
                        <input type="password" id="connectionPassword" class="form__input" placeholder="password" required>
                    </div>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="connectionString">Connection String (Optional)</label>
                    <textarea id="connectionString" class="form__textarea" placeholder="Custom connection string..."></textarea>
                    <div class="form__help">Override default connection parameters with a custom string</div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('addConnectionModal')">Cancel</button>
            <button class="btn btn--secondary" onclick="testNewConnection()">Test Connection</button>
            <button class="btn btn--primary" onclick="saveConnection()">Save Connection</button>
        </div>
    </div>
</div>

<!-- View Connection Modal -->
<div class="modal" id="viewConnectionModal">
    <div class="modal__backdrop" onclick="closeModal('viewConnectionModal')"></div>
    <div class="modal__container" style="width: 800px; max-width: 90vw;">
        <div class="modal__header">
            <h3 class="modal__title">Connection Details</h3>
            <button class="modal__close" onclick="closeModal('viewConnectionModal')">&times;</button>
        </div>
        <div class="modal__body">
            <div id="connectionDetails">
                <!-- Connection details will be loaded here -->
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('viewConnectionModal')">Close</button>
            <button class="btn btn--primary" onclick="openRelationalView()">View Relations</button>
        </div>
    </div>
</div>

<script>
// Modal functionality
function openModal(modalId) {
    document.getElementById(modalId).classList.add('modal--active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('modal--active');
    document.body.style.overflow = '';
}

// Connection management functions
function viewConnection(id) {
    // Load connection details
    const connectionDetails = {
        1: {
            name: 'Main Production DB',
            type: 'MySQL',
            host: 'prod-db.company.com',
            database: 'emc_production',
            username: 'prod_user',
            status: 'Connected',
            tables: 45,
            size: '2.3 GB',
            lastBackup: '2025-09-21 03:00:00'
        },
        2: {
            name: 'Development DB',
            type: 'PostgreSQL',
            host: 'dev-db.company.com',
            database: 'emc_development',
            username: 'dev_user',
            status: 'Slow',
            tables: 38,
            size: '450 MB',
            lastBackup: '2025-09-22 02:30:00'
        },
        3: {
            name: 'Analytics DB',
            type: 'MongoDB',
            host: 'analytics.company.com',
            database: 'analytics',
            username: 'analytics_user',
            status: 'Disconnected',
            tables: 12,
            size: '5.7 GB',
            lastBackup: '2025-09-20 23:45:00'
        }
    };
    
    const connection = connectionDetails[id];
    const detailsHtml = `
        <div class="card">
            <div class="card__header">
                <h4 class="card__title">${connection.name}</h4>
                <span class="status status--${connection.status === 'Connected' ? 'success' : connection.status === 'Slow' ? 'warning' : 'error'}">
                    <span>●</span>
                    ${connection.status}
                </span>
            </div>
            <div class="card__content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-lg);">
                    <div>
                        <h5 style="margin-bottom: var(--space-sm); color: var(--color-text-primary);">Connection Info</h5>
                        <dl style="margin: 0;">
                            <dt style="font-weight: var(--font-weight-medium); color: var(--color-text-primary);">Type:</dt>
                            <dd style="margin-bottom: var(--space-sm); color: var(--color-text-secondary);">${connection.type}</dd>
                            <dt style="font-weight: var(--font-weight-medium); color: var(--color-text-primary);">Host:</dt>
                            <dd style="margin-bottom: var(--space-sm); color: var(--color-text-secondary);">${connection.host}</dd>
                            <dt style="font-weight: var(--font-weight-medium); color: var(--color-text-primary);">Database:</dt>
                            <dd style="margin-bottom: var(--space-sm); color: var(--color-text-secondary);">${connection.database}</dd>
                            <dt style="font-weight: var(--font-weight-medium); color: var(--color-text-primary);">Username:</dt>
                            <dd style="color: var(--color-text-secondary);">${connection.username}</dd>
                        </dl>
                    </div>
                    <div>
                        <h5 style="margin-bottom: var(--space-sm); color: var(--color-text-primary);">Statistics</h5>
                        <dl style="margin: 0;">
                            <dt style="font-weight: var(--font-weight-medium); color: var(--color-text-primary);">Tables:</dt>
                            <dd style="margin-bottom: var(--space-sm); color: var(--color-text-secondary);">${connection.tables}</dd>
                            <dt style="font-weight: var(--font-weight-medium); color: var(--color-text-primary);">Size:</dt>
                            <dd style="margin-bottom: var(--space-sm); color: var(--color-text-secondary);">${connection.size}</dd>
                            <dt style="font-weight: var(--font-weight-medium); color: var(--color-text-primary);">Last Backup:</dt>
                            <dd style="color: var(--color-text-secondary);">${connection.lastBackup}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('connectionDetails').innerHTML = detailsHtml;
    openModal('viewConnectionModal');
}

function editConnection(id, event) {
    event.stopPropagation();
    // Load connection data into edit form
    openModal('addConnectionModal');
}

function testConnection(id, event) {
    event.stopPropagation();
    alert('Testing connection... This would normally test the database connection.');
}

function deleteConnection(id, event) {
    event.stopPropagation();
    if (confirm('Are you sure you want to delete this connection?')) {
        alert('Connection deleted. This would normally remove the connection from the database.');
    }
}

function saveConnection() {
    const form = document.getElementById('connectionForm');
    if (form.checkValidity()) {
        alert('Connection saved successfully!');
        closeModal('addConnectionModal');
        // Reset form
        form.reset();
    } else {
        form.reportValidity();
    }
}

function testNewConnection() {
    alert('Testing connection... This would validate the connection parameters.');
}

function refreshConnections() {
    alert('Refreshing connections... This would reload the connection list.');
}

function openRelationalView() {
    alert('Opening relational view... This would show the database schema and relationships.');
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const activeModal = document.querySelector('.modal.modal--active');
        if (activeModal) {
            closeModal(activeModal.id);
        }
    }
});
</script>
@endsection
