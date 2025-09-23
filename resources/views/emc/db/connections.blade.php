<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Database Connections</h1>
        <span class="status status--info">
            <span>●</span>
            {{ $connections ?? 3 }} Active Connections
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

<!-- Connection Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-lg); margin-bottom: var(--space-lg);">
    <div class="metric-card">
        <div class="metric-card__value">3</div>
        <div class="metric-card__label">Total Connections</div>
        <div class="metric-card__change metric-card__change--positive">+1 this week</div>
    </div>
    <div class="metric-card">
        <div class="metric-card__value">2</div>
        <div class="metric-card__label">Active Connections</div>
        <div class="metric-card__change metric-card__change--neutral">No change</div>
    </div>
    <div class="metric-card">
        <div class="metric-card__value">98.5%</div>
        <div class="metric-card__label">Uptime</div>
        <div class="metric-card__change metric-card__change--positive">+0.2% vs last month</div>
    </div>
    <div class="metric-card">
        <div class="metric-card__value">45ms</div>
        <div class="metric-card__label">Avg Response Time</div>
        <div class="metric-card__change metric-card__change--negative">+5ms vs last week</div>
    </div>
</div>

<div class="card">
    <div class="card__header">
        <h2 class="card__title">Database Connections</h2>
        <p class="card__subtitle">Manage database connections and view relational data</p>
        <div class="card__actions">
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
    <div class="card__content">
        <!-- Loading State -->
        <div id="connectionsLoading" class="loading-state" style="display: none;">
            <div class="loading-state__content">
                <div class="spinner"></div>
                <div class="loading-state__text">Loading connections...</div>
            </div>
        </div>
        
        <div class="table-container" id="connectionsTable">
            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th class="table__header-cell">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th class="table__header-cell">Name</th>
                        <th class="table__header-cell">Type</th>
                        <th class="table__header-cell">Host</th>
                        <th class="table__header-cell">Database</th>
                        <th class="table__header-cell">Status</th>
                        <th class="table__header-cell">Last Test</th>
                        <th class="table__header-cell">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table__row table__row--clickable" onclick="viewConnection(1)">
                        <td class="table__cell">
                            <input type="checkbox" class="connection-checkbox" value="1" onclick="event.stopPropagation()">
                        </td>
                        <td class="table__cell">
                            <div>
                                <strong>Main Production DB</strong>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Primary database for retail operations</div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="badge badge--secondary">MySQL</span>
                        </td>
                        <td class="table__cell">prod-db.company.com</td>
                        <td class="table__cell">emc_production</td>
                        <td class="table__cell">
                            <span class="status status--success">
                                <span>●</span>
                                Connected
                            </span>
                        </td>
                        <td class="table__cell">
                            <time datetime="2025-09-22T14:30:00">2 mins ago</time>
                        </td>
                        <td class="table__cell">
                            <div class="table__actions">
                                <button class="btn btn--small btn--secondary" onclick="editConnection(1, event)" title="Edit connection">Edit</button>
                                <button class="btn btn--small btn--secondary" onclick="testConnection(1, event)" title="Test connection">Test</button>
                                <button class="btn btn--small btn--danger" onclick="deleteConnection(1, event)" title="Delete connection">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table__row table__row--clickable" onclick="viewConnection(2)">
                        <td class="table__cell">
                            <input type="checkbox" class="connection-checkbox" value="2" onclick="event.stopPropagation()">
                        </td>
                        <td class="table__cell">
                            <div>
                                <strong>Development DB</strong>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Development and testing database</div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="badge badge--info">PostgreSQL</span>
                        </td>
                        <td class="table__cell">dev-db.company.com</td>
                        <td class="table__cell">emc_development</td>
                        <td class="table__cell">
                            <span class="status status--warning">
                                <span>●</span>
                                Slow
                            </span>
                        </td>
                        <td class="table__cell">
                            <time datetime="2025-09-22T14:25:00">7 mins ago</time>
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
                        <td class="table__cell">
                            <input type="checkbox" class="connection-checkbox" value="3" onclick="event.stopPropagation()">
                        </td>
                        <td class="table__cell">
                            <div>
                                <strong>Analytics DB</strong>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Business intelligence and reporting</div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="badge badge--warning">MongoDB</span>
                        </td>
                        <td class="table__cell">analytics.company.com</td>
                        <td class="table__cell">analytics</td>
                        <td class="table__cell">
                            <span class="status status--error">
                                <span>●</span>
                                Disconnected
                            </span>
                        </td>
                        <td class="table__cell">
                            <time datetime="2025-09-22T13:15:00">1 hour ago</time>
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
        
        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions" style="display: none;">
            <div class="bulk-actions__content">
                <span class="bulk-actions__count">0 connections selected</span>
                <div class="bulk-actions__buttons">
                    <button class="btn btn--small btn--secondary" onclick="bulkTestConnections()">Test Selected</button>
                    <button class="btn btn--small btn--danger" onclick="bulkDeleteConnections()">Delete Selected</button>
                </div>
            </div>
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

// Selection management
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.connection-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.connection-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const countSpan = bulkActions.querySelector('.bulk-actions__count');
    
    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        countSpan.textContent = `${checkboxes.length} connection${checkboxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.connection-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Loading states
function showLoading() {
    document.getElementById('connectionsLoading').style.display = 'flex';
    document.getElementById('connectionsTable').style.opacity = '0.5';
}

function hideLoading() {
    document.getElementById('connectionsLoading').style.display = 'none';
    document.getElementById('connectionsTable').style.opacity = '1';
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
            lastBackup: '2025-09-21 03:00:00',
            uptime: '99.8%',
            avgResponse: '35ms'
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
            lastBackup: '2025-09-22 02:30:00',
            uptime: '97.2%',
            avgResponse: '125ms'
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
            lastBackup: '2025-09-20 23:45:00',
            uptime: '85.3%',
            avgResponse: 'N/A'
        }
    };
    
    const connection = connectionDetails[id];
    const statusClass = connection.status === 'Connected' ? 'success' : connection.status === 'Slow' ? 'warning' : 'error';
    
    const detailsHtml = `
        <div class="connection-details">
            <div class="connection-details__header">
                <h4 class="connection-details__title">${connection.name}</h4>
                <span class="status status--${statusClass}">
                    <span>●</span>
                    ${connection.status}
                </span>
            </div>
            
            <div class="connection-details__grid">
                <div class="connection-details__section">
                    <h5>Connection Information</h5>
                    <dl class="connection-details__list">
                        <dt>Type:</dt>
                        <dd><span class="badge badge--secondary">${connection.type}</span></dd>
                        <dt>Host:</dt>
                        <dd>${connection.host}</dd>
                        <dt>Database:</dt>
                        <dd>${connection.database}</dd>
                        <dt>Username:</dt>
                        <dd>${connection.username}</dd>
                    </dl>
                </div>
                
                <div class="connection-details__section">
                    <h5>Performance Metrics</h5>
                    <dl class="connection-details__list">
                        <dt>Uptime:</dt>
                        <dd>${connection.uptime}</dd>
                        <dt>Avg Response:</dt>
                        <dd>${connection.avgResponse}</dd>
                        <dt>Tables:</dt>
                        <dd>${connection.tables}</dd>
                        <dt>Size:</dt>
                        <dd>${connection.size}</dd>
                    </dl>
                </div>
                
                <div class="connection-details__section">
                    <h5>Backup Information</h5>
                    <dl class="connection-details__list">
                        <dt>Last Backup:</dt>
                        <dd>${connection.lastBackup}</dd>
                        <dt>Status:</dt>
                        <dd><span class="status status--success"><span>●</span> Automated</span></dd>
                    </dl>
                </div>
            </div>
            
            <div class="connection-details__actions">
                <button class="btn btn--primary" onclick="testConnectionDetailed(${id})">Test Connection</button>
                <button class="btn btn--secondary" onclick="editConnection(${id})">Edit Settings</button>
                <button class="btn btn--secondary" onclick="viewConnectionLogs(${id})">View Logs</button>
            </div>
        </div>
    `;
    
    document.getElementById('connectionDetails').innerHTML = detailsHtml;
    openModal('viewConnectionModal');
}

function editConnection(id, event) {
    if (event) event.stopPropagation();
    
    // Pre-populate form with connection data
    const connections = {
        1: { name: 'Main Production DB', type: 'mysql', host: 'prod-db.company.com', port: 3306, database: 'emc_production', username: 'prod_user' },
        2: { name: 'Development DB', type: 'postgresql', host: 'dev-db.company.com', port: 5432, database: 'emc_development', username: 'dev_user' },
        3: { name: 'Analytics DB', type: 'mongodb', host: 'analytics.company.com', port: 27017, database: 'analytics', username: 'analytics_user' }
    };
    
    const connection = connections[id];
    if (connection) {
        document.getElementById('connectionName').value = connection.name;
        document.getElementById('connectionType').value = connection.type;
        document.getElementById('connectionHost').value = connection.host;
        document.getElementById('connectionPort').value = connection.port;
        document.getElementById('connectionDatabase').value = connection.database;
        document.getElementById('connectionUsername').value = connection.username;
        
        // Change modal title
        document.querySelector('#addConnectionModal .modal__title').textContent = 'Edit Database Connection';
    }
    
    openModal('addConnectionModal');
}

function testConnection(id, event) {
    if (event) event.stopPropagation();
    
    const button = event.target;
    const originalText = button.textContent;
    
    // Show loading state
    button.textContent = 'Testing...';
    button.disabled = true;
    
    // Simulate connection test
    setTimeout(() => {
        const success = Math.random() > 0.3; // 70% success rate
        
        if (success) {
            button.textContent = '✓ Connected';
            button.classList.add('btn--success');
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('btn--success');
                button.disabled = false;
            }, 2000);
        } else {
            button.textContent = '✗ Failed';
            button.classList.add('btn--danger');
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('btn--danger');
                button.disabled = false;
            }, 2000);
        }
    }, 1000 + Math.random() * 2000);
}

function deleteConnection(id, event) {
    if (event) event.stopPropagation();
    
    if (confirm('Are you sure you want to delete this connection? This action cannot be undone.')) {
        const row = event.target.closest('.table__row');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0.5';
        
        setTimeout(() => {
            row.remove();
            showNotification('Connection deleted successfully', 'success');
        }, 300);
    }
}

function bulkTestConnections() {
    const selectedIds = Array.from(document.querySelectorAll('.connection-checkbox:checked')).map(cb => cb.value);
    showNotification(`Testing ${selectedIds.length} connections...`, 'info');
    
    setTimeout(() => {
        showNotification(`${selectedIds.length} connections tested successfully`, 'success');
    }, 2000);
}

function bulkDeleteConnections() {
    const selectedIds = Array.from(document.querySelectorAll('.connection-checkbox:checked')).map(cb => cb.value);
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} connection(s)? This action cannot be undone.`)) {
        selectedIds.forEach(id => {
            const checkbox = document.querySelector(`.connection-checkbox[value="${id}"]`);
            const row = checkbox.closest('.table__row');
            row.remove();
        });
        
        showNotification(`${selectedIds.length} connections deleted successfully`, 'success');
        updateBulkActions();
    }
}

function saveConnection() {
    const form = document.getElementById('connectionForm');
    if (form.checkValidity()) {
        const button = document.querySelector('#addConnectionModal .btn--primary');
        const originalText = button.textContent;
        
        button.textContent = 'Saving...';
        button.disabled = true;
        
        setTimeout(() => {
            showNotification('Connection saved successfully!', 'success');
            closeModal('addConnectionModal');
            form.reset();
            
            // Reset modal title
            document.querySelector('#addConnectionModal .modal__title').textContent = 'Add Database Connection';
            button.textContent = originalText;
            button.disabled = false;
            
            // Refresh connections table
            refreshConnections();
        }, 1000);
    } else {
        form.reportValidity();
    }
}

function testNewConnection() {
    const button = event.target;
    const originalText = button.textContent;
    
    button.textContent = 'Testing...';
    button.disabled = true;
    
    setTimeout(() => {
        const success = Math.random() > 0.2; // 80% success rate
        
        if (success) {
            showNotification('Connection test successful!', 'success');
        } else {
            showNotification('Connection test failed. Please check your settings.', 'error');
        }
        
        button.textContent = originalText;
        button.disabled = false;
    }, 1500);
}

function refreshConnections() {
    showLoading();
    
    setTimeout(() => {
        hideLoading();
        showNotification('Connections refreshed', 'success');
        
        // Update last test times
        document.querySelectorAll('time').forEach(time => {
            time.textContent = 'Just now';
            time.setAttribute('datetime', new Date().toISOString());
        });
    }, 1000);
}

function testConnectionDetailed(id) {
    showNotification(`Running detailed connection test for connection ${id}...`, 'info');
}

function viewConnectionLogs(id) {
    showNotification(`Opening connection logs for connection ${id}...`, 'info');
}

function openRelationalView() {
    showNotification('Opening relational view... This would show the database schema and relationships.', 'info');
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification--${type}`;
    notification.innerHTML = `
        <div class="notification__content">
            <span class="notification__message">${message}</span>
            <button class="notification__close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
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

// Auto-refresh connections every 30 seconds
setInterval(function() {
    // Update connection status indicators silently
    console.log('Auto-refreshing connection status...');
}, 30000);
</script>

<style>
.metric-card {
    background: var(--color-background-secondary);
    border: 1px solid var(--color-border-primary);
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    text-align: center;
}

.metric-card__value {
    font-size: 2rem;
    font-weight: var(--font-weight-bold);
    color: var(--color-text-primary);
    margin-bottom: var(--space-xs);
}

.metric-card__label {
    color: var(--color-text-secondary);
    font-size: var(--font-size-sm);
    margin-bottom: var(--space-xs);
}

.metric-card__change {
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-medium);
}

.metric-card__change--positive {
    color: var(--color-success);
}

.metric-card__change--negative {
    color: var(--color-danger);
}

.metric-card__change--neutral {
    color: var(--color-text-tertiary);
}

.loading-state {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.loading-state__content {
    text-align: center;
}

.spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--color-border-primary);
    border-top: 3px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto var(--space-sm);
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.bulk-actions {
    margin-top: var(--space-md);
    padding: var(--space-md);
    background: var(--color-background-tertiary);
    border: 1px solid var(--color-border-primary);
    border-radius: var(--radius-md);
}

.bulk-actions__content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bulk-actions__count {
    font-weight: var(--font-weight-medium);
    color: var(--color-text-primary);
}

.bulk-actions__buttons {
    display: flex;
    gap: var(--space-sm);
}

.connection-details__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--space-lg);
    margin: var(--space-lg) 0;
}

.connection-details__section h5 {
    margin-bottom: var(--space-sm);
    color: var(--color-text-primary);
    font-weight: var(--font-weight-medium);
}

.connection-details__list {
    margin: 0;
}

.connection-details__list dt {
    font-weight: var(--font-weight-medium);
    color: var(--color-text-primary);
    margin-bottom: var(--space-xs);
}

.connection-details__list dd {
    margin: 0 0 var(--space-sm) 0;
    color: var(--color-text-secondary);
}

.connection-details__actions {
    display: flex;
    gap: var(--space-sm);
    margin-top: var(--space-lg);
    padding-top: var(--space-lg);
    border-top: 1px solid var(--color-border-primary);
}

.notification {
    position: fixed;
    top: var(--space-lg);
    right: var(--space-lg);
    z-index: 1000;
    max-width: 400px;
    background: white;
    border: 1px solid var(--color-border-primary);
    border-radius: var(--radius-md);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: slideIn 0.3s ease;
}

.notification--success {
    border-left: 4px solid var(--color-success);
}

.notification--error {
    border-left: 4px solid var(--color-danger);
}

.notification--info {
    border-left: 4px solid var(--color-info);
}

.notification--warning {
    border-left: 4px solid var(--color-warning);
}

.notification__content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-md);
}

.notification__message {
    color: var(--color-text-primary);
    font-size: var(--font-size-sm);
}

.notification__close {
    background: none;
    border: none;
    font-size: var(--font-size-lg);
    color: var(--color-text-secondary);
    cursor: pointer;
    padding: 0;
    margin-left: var(--space-sm);
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.btn--success {
    background: var(--color-success);
    border-color: var(--color-success);
    color: white;
}

.card__actions {
    display: flex;
    gap: var(--space-sm);
    margin-left: auto;
}

@media (max-width: 768px) {
    .connection-details__grid {
        grid-template-columns: 1fr;
    }
    
    .connection-details__actions {
        flex-direction: column;
    }
    
    .bulk-actions__content {
        flex-direction: column;
        gap: var(--space-sm);
        align-items: stretch;
    }
    
    .card__header {
        flex-direction: column;
        gap: var(--space-md);
    }
    
    .card__actions {
        margin-left: 0;
    }
}
</style>