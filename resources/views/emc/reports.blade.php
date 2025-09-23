@extends('emc.layout')

@section('title', 'Report Management - EMC')

@section('content')
<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Report Management</h1>
        <span class="status status--info">
            <span>●</span>
            12 Active Reports
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="openModal('createReportModal')">
            <span>+</span>
            New Report
        </button>
        <button class="btn btn--secondary" onclick="openModal('templateModal')">
            <span>📄</span>
            Templates
        </button>
        <button class="btn btn--secondary" onclick="openModal('scheduleModal')">
            <span>⏰</span>
            Schedule
        </button>
        <button class="btn btn--secondary" onclick="refreshReports()">
            <span>↻</span>
            Refresh
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: var(--space-lg);">
    <!-- Reports List -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Reports</h2>
            <p class="card__subtitle">Manage and generate your reports</p>
        </div>
        <div class="card__content">
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Report Name</th>
                            <th class="table__header-cell">Type</th>
                            <th class="table__header-cell">Last Run</th>
                            <th class="table__header-cell">Status</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row table__row--clickable" onclick="viewReport('sales_summary')">
                            <td class="table__cell">
                                <div>
                                    <div style="font-weight: 500;">Sales Summary</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Monthly sales performance</div>
                                </div>
                            </td>
                            <td class="table__cell">Dashboard</td>
                            <td class="table__cell">2025-01-17 09:00</td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Active
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="runReport('sales_summary', event)">Run</button>
                                    <button class="btn btn--small btn--secondary" onclick="editReport('sales_summary', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="exportReport('sales_summary', event)">Export</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewReport('user_analytics')">
                            <td class="table__cell">
                                <div>
                                    <div style="font-weight: 500;">User Analytics</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">User behavior and engagement</div>
                                </div>
                            </td>
                            <td class="table__cell">Analytics</td>
                            <td class="table__cell">2025-01-17 08:30</td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Active
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="runReport('user_analytics', event)">Run</button>
                                    <button class="btn btn--small btn--secondary" onclick="editReport('user_analytics', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="exportReport('user_analytics', event)">Export</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewReport('financial_report')">
                            <td class="table__cell">
                                <div>
                                    <div style="font-weight: 500;">Financial Report</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Quarterly financial analysis</div>
                                </div>
                            </td>
                            <td class="table__cell">Financial</td>
                            <td class="table__cell">2025-01-15 16:45</td>
                            <td class="table__cell">
                                <span class="status status--warning">
                                    <span>●</span>
                                    Scheduled
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="runReport('financial_report', event)">Run</button>
                                    <button class="btn btn--small btn--secondary" onclick="editReport('financial_report', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="exportReport('financial_report', event)">Export</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewReport('inventory_status')">
                            <td class="table__cell">
                                <div>
                                    <div style="font-weight: 500;">Inventory Status</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Stock levels and alerts</div>
                                </div>
                            </td>
                            <td class="table__cell">Operational</td>
                            <td class="table__cell">2025-01-17 07:00</td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Active
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="runReport('inventory_status', event)">Run</button>
                                    <button class="btn btn--small btn--secondary" onclick="editReport('inventory_status', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="exportReport('inventory_status', event)">Export</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewReport('system_performance')">
                            <td class="table__cell">
                                <div>
                                    <div style="font-weight: 500;">System Performance</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Server metrics and uptime</div>
                                </div>
                            </td>
                            <td class="table__cell">Technical</td>
                            <td class="table__cell">-</td>
                            <td class="table__cell">
                                <span class="status status--danger">
                                    <span>●</span>
                                    Error
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="runReport('system_performance', event)">Run</button>
                                    <button class="btn btn--small btn--secondary" onclick="editReport('system_performance', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="exportReport('system_performance', event)">Export</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Recent Reports -->
    <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
        <!-- Quick Stats -->
        <div class="card">
            <div class="card__header">
                <h2 class="card__title">Quick Stats</h2>
            </div>
            <div class="card__content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-success);">12</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Active Reports</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-primary);">348</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Generated Today</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-warning);">5</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Scheduled</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-danger);">2</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Failed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="card">
            <div class="card__header">
                <h2 class="card__title">Recent Runs</h2>
            </div>
            <div class="card__content">
                <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                    <div style="padding: var(--space-sm); border-left: 3px solid var(--color-success); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-weight: 500; font-size: var(--font-size-sm);">Sales Summary</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Completed - 09:00 AM</div>
                    </div>
                    <div style="padding: var(--space-sm); border-left: 3px solid var(--color-success); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-weight: 500; font-size: var(--font-size-sm);">User Analytics</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Completed - 08:30 AM</div>
                    </div>
                    <div style="padding: var(--space-sm); border-left: 3px solid var(--color-success); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-weight: 500; font-size: var(--font-size-sm);">Inventory Status</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Completed - 07:00 AM</div>
                    </div>
                    <div style="padding: var(--space-sm); border-left: 3px solid var(--color-danger); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-weight: 500; font-size: var(--font-size-sm);">System Performance</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">Failed - 06:30 AM</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Report Modal -->
<div class="modal" id="createReportModal">
    <div class="modal__backdrop" onclick="closeModal('createReportModal')"></div>
    <div class="modal__container" style="width: 700px;">
        <div class="modal__header">
            <h3 class="modal__title">Create New Report</h3>
            <button class="modal__close" onclick="closeModal('createReportModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="createReportForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="reportName">Report Name</label>
                    <input type="text" id="reportName" class="form__input" placeholder="Enter report name" required>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="reportDescription">Description</label>
                    <textarea id="reportDescription" class="form__input" rows="3" placeholder="Brief description of the report"></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="reportType">Report Type</label>
                        <select id="reportType" class="form__select" required>
                            <option value="">Select type</option>
                            <option value="dashboard">Dashboard</option>
                            <option value="analytics">Analytics</option>
                            <option value="financial">Financial</option>
                            <option value="operational">Operational</option>
                            <option value="technical">Technical</option>
                        </select>
                    </div>
                    
                    <div class="form__group">
                        <label class="form__label form__label--required" for="dataSource">Data Source</label>
                        <select id="dataSource" class="form__select" required>
                            <option value="">Select source</option>
                            <option value="mysql_main">MySQL - Main Database</option>
                            <option value="postgresql_analytics">PostgreSQL - Analytics</option>
                            <option value="api_external">External API</option>
                            <option value="csv_import">CSV Import</option>
                        </select>
                    </div>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="reportQuery">Query/Configuration</label>
                    <textarea id="reportQuery" class="form__input" rows="6" placeholder="SELECT * FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)" required style="font-family: monospace;"></textarea>
                    <div class="form__help">Enter SQL query or configuration parameters</div>
                </div>
                
                <div class="form__group">
                    <label class="form__label">Output Formats</label>
                    <div style="display: flex; gap: var(--space-md); flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> PDF
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Excel
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> CSV
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> HTML
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('createReportModal')">Cancel</button>
            <button class="btn btn--secondary" onclick="previewReport()">Preview</button>
            <button class="btn btn--primary" onclick="createReport()">Create Report</button>
        </div>
    </div>
</div>

<!-- Template Modal -->
<div class="modal" id="templateModal">
    <div class="modal__backdrop" onclick="closeModal('templateModal')"></div>
    <div class="modal__container" style="width: 800px;">
        <div class="modal__header">
            <h3 class="modal__title">Report Templates</h3>
            <button class="modal__close" onclick="closeModal('templateModal')">&times;</button>
        </div>
        <div class="modal__body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-md);">
                <div class="card" style="cursor: pointer; border: 2px solid transparent;" onclick="selectTemplate('sales_template')">
                    <div class="card__content">
                        <h4 style="margin-bottom: var(--space-sm);">📊 Sales Report</h4>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-sm);">Comprehensive sales analysis with charts and KPIs</p>
                        <div class="status status--success" style="font-size: var(--font-size-xs);">Ready to use</div>
                    </div>
                </div>
                
                <div class="card" style="cursor: pointer; border: 2px solid transparent;" onclick="selectTemplate('user_template')">
                    <div class="card__content">
                        <h4 style="margin-bottom: var(--space-sm);">👥 User Analytics</h4>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-sm);">User behavior, engagement, and activity metrics</p>
                        <div class="status status--success" style="font-size: var(--font-size-xs);">Ready to use</div>
                    </div>
                </div>
                
                <div class="card" style="cursor: pointer; border: 2px solid transparent;" onclick="selectTemplate('financial_template')">
                    <div class="card__content">
                        <h4 style="margin-bottom: var(--space-sm);">💰 Financial Summary</h4>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-sm);">Revenue, expenses, and profit analysis</p>
                        <div class="status status--success" style="font-size: var(--font-size-xs);">Ready to use</div>
                    </div>
                </div>
                
                <div class="card" style="cursor: pointer; border: 2px solid transparent;" onclick="selectTemplate('inventory_template')">
                    <div class="card__content">
                        <h4 style="margin-bottom: var(--space-sm);">📦 Inventory Report</h4>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-sm);">Stock levels, turnover, and alerts</p>
                        <div class="status status--success" style="font-size: var(--font-size-xs);">Ready to use</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('templateModal')">Close</button>
            <button class="btn btn--primary" onclick="useTemplate()">Use Template</button>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div class="modal" id="scheduleModal">
    <div class="modal__backdrop" onclick="closeModal('scheduleModal')"></div>
    <div class="modal__container" style="width: 600px;">
        <div class="modal__header">
            <h3 class="modal__title">Schedule Report</h3>
            <button class="modal__close" onclick="closeModal('scheduleModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="scheduleForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="scheduleReport">Select Report</label>
                    <select id="scheduleReport" class="form__select" required>
                        <option value="">Choose a report</option>
                        <option value="sales_summary">Sales Summary</option>
                        <option value="user_analytics">User Analytics</option>
                        <option value="financial_report">Financial Report</option>
                        <option value="inventory_status">Inventory Status</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="scheduleFrequency">Frequency</label>
                    <select id="scheduleFrequency" class="form__select" required>
                        <option value="">Select frequency</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="scheduleTime">Time</label>
                        <input type="time" id="scheduleTime" class="form__input" value="09:00" required>
                    </div>
                    
                    <div class="form__group">
                        <label class="form__label form__label--required" for="scheduleFormat">Format</label>
                        <select id="scheduleFormat" class="form__select" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="email">Email (HTML)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="scheduleRecipients">Email Recipients</label>
                    <input type="email" id="scheduleRecipients" class="form__input" placeholder="email1@company.com, email2@company.com" multiple>
                    <div class="form__help">Separate multiple emails with commas</div>
                </div>
                
                <div class="form__group">
                    <label class="form__label">Options</label>
                    <div style="display: flex; flex-direction: column; gap: var(--space-xs);">
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Send email notification
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Include charts and graphs
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Compress large files
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('scheduleModal')">Cancel</button>
            <button class="btn btn--primary" onclick="scheduleReport()">Schedule Report</button>
        </div>
    </div>
</div>

<!-- Report Viewer Modal -->
<div class="modal" id="reportViewerModal">
    <div class="modal__backdrop" onclick="closeModal('reportViewerModal')"></div>
    <div class="modal__container" style="width: 95vw; height: 90vh;">
        <div class="modal__header">
            <h3 class="modal__title" id="reportViewerTitle">Report Viewer</h3>
            <button class="modal__close" onclick="closeModal('reportViewerModal')">&times;</button>
        </div>
        <div class="modal__body" style="height: calc(90vh - 120px); overflow: auto;">
            <div id="reportContent">
                <!-- Report content will be loaded here -->
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('reportViewerModal')">Close</button>
            <button class="btn btn--secondary" onclick="exportCurrentReport()">Export</button>
            <button class="btn btn--primary" onclick="scheduleCurrentReport()">Schedule</button>
        </div>
    </div>
</div>

<script>
let currentReport = '';
let selectedTemplate = '';

// Modal functionality
function openModal(modalId) {
    document.getElementById(modalId).classList.add('modal--active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('modal--active');
    document.body.style.overflow = '';
}

// Report management functions
function viewReport(reportId) {
    currentReport = reportId;
    const reportContent = generateReportContent(reportId);
    document.getElementById('reportViewerTitle').textContent = `${reportId.replace('_', ' ').toUpperCase()} - Report`;
    document.getElementById('reportContent').innerHTML = reportContent;
    openModal('reportViewerModal');
}

function generateReportContent(reportId) {
    const reports = {
        'sales_summary': `
            <div style="padding: var(--space-lg);">
                <h2 style="margin-bottom: var(--space-lg); color: var(--color-text-primary);">Sales Summary Report</h2>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-md); margin-bottom: var(--space-lg);">
                    <div style="text-align: center; padding: var(--space-lg); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: 2rem; font-weight: 600; color: var(--color-success);">$156,789</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Total Revenue</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-lg); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: 2rem; font-weight: 600; color: var(--color-primary);">1,247</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Orders</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-lg); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: 2rem; font-weight: 600; color: var(--color-warning);">$125.87</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Avg Order Value</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-lg); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: 2rem; font-weight: 600; color: var(--color-info);">12.5%</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Growth Rate</div>
                    </div>
                </div>
                <div style="background: var(--color-surface-secondary); padding: var(--space-lg); border-radius: var(--border-radius); text-align: center; color: var(--color-text-secondary);">
                    📊 Chart visualization would appear here
                </div>
            </div>
        `,
        'user_analytics': `
            <div style="padding: var(--space-lg);">
                <h2 style="margin-bottom: var(--space-lg); color: var(--color-text-primary);">User Analytics Report</h2>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-md); margin-bottom: var(--space-lg);">
                    <div style="text-align: center; padding: var(--space-lg); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: 2rem; font-weight: 600; color: var(--color-primary);">2,847</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Active Users</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-lg); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: 2rem; font-weight: 600; color: var(--color-success);">7.2m</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Page Views</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-lg); background: var(--color-surface-secondary); border-radius: var(--border-radius);">
                        <div style="font-size: 2rem; font-weight: 600; color: var(--color-warning);">4m 32s</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Avg Session</div>
                    </div>
                </div>
                <div style="background: var(--color-surface-secondary); padding: var(--space-lg); border-radius: var(--border-radius); text-align: center; color: var(--color-text-secondary);">
                    📈 User engagement charts would appear here
                </div>
            </div>
        `
    };
    
    return reports[reportId] || '<div style="padding: var(--space-lg); text-align: center; color: var(--color-text-secondary);">Report content not available in demo.</div>';
}

function runReport(reportId, event) {
    event.stopPropagation();
    alert(`Running report: ${reportId}`);
}

function editReport(reportId, event) {
    event.stopPropagation();
    alert(`Edit report: ${reportId}`);
}

function exportReport(reportId, event) {
    event.stopPropagation();
    alert(`Export report: ${reportId}`);
}

// Template functions
function selectTemplate(templateId) {
    // Remove previous selection
    document.querySelectorAll('#templateModal .card').forEach(card => {
        card.style.borderColor = 'transparent';
    });
    // Add selection to current template
    event.currentTarget.style.borderColor = 'var(--color-primary)';
    selectedTemplate = templateId;
}

function useTemplate() {
    if (selectedTemplate) {
        alert(`Using template: ${selectedTemplate}`);
        closeModal('templateModal');
        openModal('createReportModal');
    }
}

// Form functions
function createReport() {
    const reportName = document.getElementById('reportName').value;
    if (reportName) {
        alert(`Creating report: ${reportName}`);
        closeModal('createReportModal');
    }
}

function previewReport() {
    alert('Generating report preview...');
}

function scheduleReport() {
    const reportName = document.getElementById('scheduleReport').value;
    const frequency = document.getElementById('scheduleFrequency').value;
    if (reportName && frequency) {
        alert(`Scheduling ${reportName} to run ${frequency}`);
        closeModal('scheduleModal');
    }
}

function exportCurrentReport() {
    if (currentReport) {
        alert(`Exporting report: ${currentReport}`);
    }
}

function scheduleCurrentReport() {
    if (currentReport) {
        closeModal('reportViewerModal');
        openModal('scheduleModal');
    }
}

function refreshReports() {
    alert('Refreshing reports list...');
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
