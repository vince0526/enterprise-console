<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Performance Monitoring</h1>
        <span class="status status--success">
            <span>●</span>
            All Systems Healthy
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="openModal('alertModal')">
            <span>🔔</span>
            Configure Alerts
        </button>
        <button class="btn btn--secondary" onclick="exportMetrics()">
            <span>📊</span>
            Export Report
        </button>
        <button class="btn btn--secondary" onclick="refreshMetrics()">
            <span>↻</span>
            Refresh
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-lg); margin-bottom: var(--space-lg);">
    <!-- System Overview -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">System Overview</h2>
            <p class="card__subtitle">Real-time performance metrics</p>
        </div>
        <div class="card__content">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                <div style="text-align: center; padding: var(--space-lg); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--color-success);">87%</div>
                    <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">CPU Usage</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Normal</div>
                </div>
                <div style="text-align: center; padding: var(--space-lg); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--color-warning);">64%</div>
                    <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Memory Usage</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Moderate</div>
                </div>
                <div style="text-align: center; padding: var(--space-lg); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--color-info);">23</div>
                    <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Active Connections</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Normal</div>
                </div>
                <div style="text-align: center; padding: var(--space-lg); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                    <div style="font-size: 2rem; font-weight: 600; color: var(--color-success);">156ms</div>
                    <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Avg Query Time</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Good</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Health -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Database Health</h2>
            <p class="card__subtitle">Per-database performance status</p>
        </div>
        <div class="card__content">
            <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                <div style="padding: var(--space-md); border-left: 3px solid var(--color-success); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 500;">emc_production</div>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">15 active connections</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: var(--font-size-sm); color: var(--color-success);">98.7%</div>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Uptime</div>
                        </div>
                    </div>
                </div>
                
                <div style="padding: var(--space-md); border-left: 3px solid var(--color-warning); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 500;">emc_development</div>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">5 active connections</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: var(--font-size-sm); color: var(--color-warning);">76.2%</div>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Uptime</div>
                        </div>
                    </div>
                </div>
                
                <div style="padding: var(--space-md); border-left: 3px solid var(--color-error); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 500;">analytics</div>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Connection issues</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: var(--font-size-sm); color: var(--color-error);">12.8%</div>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Uptime</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Charts -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-lg); margin-bottom: var(--space-lg);">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Performance Trends</h2>
            <p class="card__subtitle">24-hour performance history</p>
        </div>
        <div class="card__content">
            <div style="height: 300px; background: var(--color-background-tertiary); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--color-text-secondary);">
                📈 Interactive performance charts would appear here<br>
                <small>CPU, Memory, Connections, and Query Response Time over time</small>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Alert Summary</h2>
        </div>
        <div class="card__content">
            <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                <div style="padding: var(--space-sm); background: rgba(239, 68, 68, 0.1); border-radius: var(--radius-md); border-left: 3px solid var(--color-error);">
                    <div style="font-weight: 500; font-size: var(--font-size-sm); color: var(--color-error);">High Memory Usage</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">2 minutes ago</div>
                </div>
                
                <div style="padding: var(--space-sm); background: rgba(245, 158, 11, 0.1); border-radius: var(--radius-md); border-left: 3px solid var(--color-warning);">
                    <div style="font-weight: 500; font-size: var(--font-size-sm); color: var(--color-warning);">Slow Query Detected</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">15 minutes ago</div>
                </div>
                
                <div style="padding: var(--space-sm); background: rgba(16, 185, 129, 0.1); border-radius: var(--radius-md); border-left: 3px solid var(--color-success);">
                    <div style="font-weight: 500; font-size: var(--font-size-sm); color: var(--color-success);">Backup Completed</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">1 hour ago</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Slow Queries & Active Connections -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-lg);">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Slow Queries</h2>
            <p class="card__subtitle">Queries taking longer than 1 second</p>
        </div>
        <div class="card__content">
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Query</th>
                            <th class="table__header-cell">Duration</th>
                            <th class="table__header-cell">Database</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row">
                            <td class="table__cell">
                                <code style="font-size: var(--font-size-xs);">SELECT * FROM orders WHERE...</code>
                            </td>
                            <td class="table__cell">
                                <span style="color: var(--color-error); font-weight: 500;">3.2s</span>
                            </td>
                            <td class="table__cell">emc_production</td>
                            <td class="table__cell">
                                <button class="btn btn--small btn--secondary" onclick="explainQuery(1)">Explain</button>
                            </td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">
                                <code style="font-size: var(--font-size-xs);">UPDATE products SET stock...</code>
                            </td>
                            <td class="table__cell">
                                <span style="color: var(--color-warning); font-weight: 500;">1.8s</span>
                            </td>
                            <td class="table__cell">emc_production</td>
                            <td class="table__cell">
                                <button class="btn btn--small btn--secondary" onclick="explainQuery(2)">Explain</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Active Connections</h2>
            <p class="card__subtitle">Current database connections</p>
        </div>
        <div class="card__content">
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">User</th>
                            <th class="table__header-cell">Database</th>
                            <th class="table__header-cell">Duration</th>
                            <th class="table__header-cell">Status</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row">
                            <td class="table__cell">app_user</td>
                            <td class="table__cell">emc_production</td>
                            <td class="table__cell">45m 12s</td>
                            <td class="table__cell">
                                <span class="status status--success">Active</span>
                            </td>
                            <td class="table__cell">
                                <button class="btn btn--small btn--danger" onclick="killConnection(1)">Kill</button>
                            </td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">report_user</td>
                            <td class="table__cell">analytics</td>
                            <td class="table__cell">2h 15m</td>
                            <td class="table__cell">
                                <span class="status status--warning">Idle</span>
                            </td>
                            <td class="table__cell">
                                <button class="btn btn--small btn--danger" onclick="killConnection(2)">Kill</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Configure Alerts Modal -->
<div class="modal" id="alertModal">
    <div class="modal__backdrop" onclick="closeModal('alertModal')"></div>
    <div class="modal__container" style="width: 700px;">
        <div class="modal__header">
            <h3 class="modal__title">Configure Performance Alerts</h3>
            <button class="modal__close" onclick="closeModal('alertModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="alertForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="alertType">Alert Type</label>
                    <select id="alertType" class="form__select" required>
                        <option value="">Select alert type</option>
                        <option value="cpu">CPU Usage</option>
                        <option value="memory">Memory Usage</option>
                        <option value="connections">Connection Count</option>
                        <option value="query_time">Query Response Time</option>
                        <option value="disk_space">Disk Space</option>
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="threshold">Threshold</label>
                        <input type="number" id="threshold" class="form__input" placeholder="80" required>
                    </div>
                    
                    <div class="form__group">
                        <label class="form__label form__label--required" for="unit">Unit</label>
                        <select id="unit" class="form__select" required>
                            <option value="percent">Percentage (%)</option>
                            <option value="mb">Megabytes (MB)</option>
                            <option value="seconds">Seconds</option>
                            <option value="count">Count</option>
                        </select>
                    </div>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="condition">Condition</label>
                    <select id="condition" class="form__select" required>
                        <option value="greater">Greater than</option>
                        <option value="less">Less than</option>
                        <option value="equal">Equal to</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="notificationEmail">Notification Email</label>
                    <input type="email" id="notificationEmail" class="form__input" placeholder="admin@company.com">
                </div>
                
                <div class="form__group">
                    <label class="form__label">Alert Options</label>
                    <div style="display: flex; flex-direction: column; gap: var(--space-xs);">
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Send email notification
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Log to system log
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Auto-escalate if not acknowledged
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('alertModal')">Cancel</button>
            <button class="btn btn--primary" onclick="saveAlert()">Save Alert</button>
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

// Performance monitoring functions
function explainQuery(queryId) {
    alert(`Showing query execution plan for query ${queryId}...`);
}

function killConnection(connectionId) {
    if (confirm('Are you sure you want to kill this connection?')) {
        alert(`Killing connection ${connectionId}...`);
    }
}

function exportMetrics() {
    alert('Exporting performance metrics report...');
}

function refreshMetrics() {
    alert('Refreshing performance metrics...');
}

function saveAlert() {
    const alertType = document.getElementById('alertType').value;
    const threshold = document.getElementById('threshold').value;
    
    if (alertType && threshold) {
        alert(`Saving ${alertType} alert with threshold ${threshold}...`);
        closeModal('alertModal');
    }
}

// Auto-refresh metrics every 30 seconds (in a real app)
// setInterval(refreshMetrics, 30000);

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