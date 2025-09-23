@extends('emc.layout')
@section('content')
@extends('emc.layout')

@section('title', 'Tables and Views - EMC')

@section('content')
<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Tables & Views</h1>
        <span class="status status--info">
            <span>●</span>
            Database: emc_production
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="openModal('createTableModal')">
            <span>+</span>
            Create Table
        </button>
        <button class="btn btn--secondary" onclick="openModal('importTableModal')">
            <span>↑</span>
            Import
        </button>
        <button class="btn btn--secondary" onclick="refreshTables()">
            <span>↻</span>
            Refresh
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-lg);">
    <!-- Tables List -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Tables</h2>
            <p class="card__subtitle">Click on a table to view structure and data</p>
        </div>
        <div class="card__content">
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Table Name</th>
                            <th class="table__header-cell">Rows</th>
                            <th class="table__header-cell">Size</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row table__row--clickable" onclick="viewTable('users')">
                            <td class="table__cell">users</td>
                            <td class="table__cell">1,247</td>
                            <td class="table__cell">156 KB</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editTable('users', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="viewRelations('users', event)">Relations</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewTable('products')">
                            <td class="table__cell">products</td>
                            <td class="table__cell">3,456</td>
                            <td class="table__cell">2.1 MB</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editTable('products', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="viewRelations('products', event)">Relations</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewTable('orders')">
                            <td class="table__cell">orders</td>
                            <td class="table__cell">8,923</td>
                            <td class="table__cell">5.7 MB</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editTable('orders', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="viewRelations('orders', event)">Relations</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewTable('categories')">
                            <td class="table__cell">categories</td>
                            <td class="table__cell">24</td>
                            <td class="table__cell">12 KB</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editTable('categories', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="viewRelations('categories', event)">Relations</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Views List -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Views</h2>
            <p class="card__subtitle">Database views and virtual tables</p>
        </div>
        <div class="card__content">
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">View Name</th>
                            <th class="table__header-cell">Type</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row table__row--clickable" onclick="viewTable('user_orders_view')">
                            <td class="table__cell">user_orders_view</td>
                            <td class="table__cell">Materialized</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editView('user_orders_view', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="refreshView('user_orders_view', event)">Refresh</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewTable('product_analytics')">
                            <td class="table__cell">product_analytics</td>
                            <td class="table__cell">Standard</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editView('product_analytics', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="refreshView('product_analytics', event)">Refresh</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Table Modal -->
<div class="modal" id="createTableModal">
    <div class="modal__backdrop" onclick="closeModal('createTableModal')"></div>
    <div class="modal__container" style="width: 800px; max-width: 90vw;">
        <div class="modal__header">
            <h3 class="modal__title">Create New Table</h3>
            <button class="modal__close" onclick="closeModal('createTableModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="createTableForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="tableName">Table Name</label>
                    <input type="text" id="tableName" class="form__input" placeholder="Enter table name" required>
                </div>
                
                <div class="form__group">
                    <label class="form__label">Columns</label>
                    <div id="columnsContainer">
                        <div class="column-row" style="display: grid; grid-template-columns: 200px 120px 200px 100px 40px; gap: var(--space-sm); align-items: end; margin-bottom: var(--space-sm);">
                            <div class="form__group" style="margin: 0;">
                                <label class="form__label" style="font-size: var(--font-size-xs);">Column Name</label>
                                <input type="text" class="form__input" placeholder="id" value="id">
                            </div>
                            <div class="form__group" style="margin: 0;">
                                <label class="form__label" style="font-size: var(--font-size-xs);">Type</label>
                                <select class="form__select">
                                    <option value="INT">INT</option>
                                    <option value="VARCHAR">VARCHAR</option>
                                    <option value="TEXT">TEXT</option>
                                    <option value="DATETIME">DATETIME</option>
                                    <option value="BOOLEAN">BOOLEAN</option>
                                    <option value="DECIMAL">DECIMAL</option>
                                </select>
                            </div>
                            <div class="form__group" style="margin: 0;">
                                <label class="form__label" style="font-size: var(--font-size-xs);">Length/Values</label>
                                <input type="text" class="form__input" placeholder="11">
                            </div>
                            <div class="form__group" style="margin: 0;">
                                <label class="form__label" style="font-size: var(--font-size-xs);">Options</label>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <label style="font-size: var(--font-size-xs); display: flex; align-items: center; gap: 4px;">
                                        <input type="checkbox" checked> PK
                                    </label>
                                    <label style="font-size: var(--font-size-xs); display: flex; align-items: center; gap: 4px;">
                                        <input type="checkbox" checked> AI
                                    </label>
                                </div>
                            </div>
                            <div style="display: flex; align-items: end;">
                                <button type="button" class="btn btn--small btn--danger" onclick="removeColumn(this)">×</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn--secondary btn--small" onclick="addColumn()">+ Add Column</button>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('createTableModal')">Cancel</button>
            <button class="btn btn--primary" onclick="createTable()">Create Table</button>
        </div>
    </div>
</div>

<!-- Import Table Modal -->
<div class="modal" id="importTableModal">
    <div class="modal__backdrop" onclick="closeModal('importTableModal')"></div>
    <div class="modal__container" style="width: 600px;">
        <div class="modal__header">
            <h3 class="modal__title">Import Table</h3>
            <button class="modal__close" onclick="closeModal('importTableModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="importTableForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="importType">Import Type</label>
                    <select id="importType" class="form__select" required>
                        <option value="">Select import type</option>
                        <option value="csv">CSV File</option>
                        <option value="excel">Excel File</option>
                        <option value="sql">SQL Script</option>
                        <option value="json">JSON Data</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="importFile">Select File</label>
                    <input type="file" id="importFile" class="form__input" required>
                    <div class="form__help">Maximum file size: 50MB</div>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="importTableName">Table Name</label>
                    <input type="text" id="importTableName" class="form__input" placeholder="Auto-generated from filename">
                </div>
                
                <div class="form__group">
                    <label class="form__label">Import Options</label>
                    <div style="display: flex; flex-direction: column; gap: var(--space-xs);">
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Create table if not exists
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Truncate existing data
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Auto-detect column types
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('importTableModal')">Cancel</button>
            <button class="btn btn--primary" onclick="importTable()">Import Table</button>
        </div>
    </div>
</div>

<!-- Table Details Modal -->
<div class="modal" id="tableDetailsModal">
    <div class="modal__backdrop" onclick="closeModal('tableDetailsModal')"></div>
    <div class="modal__container" style="width: 900px; max-width: 95vw;">
        <div class="modal__header">
            <h3 class="modal__title" id="tableDetailsTitle">Table Details</h3>
            <button class="modal__close" onclick="closeModal('tableDetailsModal')">&times;</button>
        </div>
        <div class="modal__body">
            <div id="tableDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('tableDetailsModal')">Close</button>
            <button class="btn btn--primary" onclick="exportTable()">Export Data</button>
        </div>
    </div>
</div>

<script>
// Modal functionality (reused from db.blade.php)
function openModal(modalId) {
    document.getElementById(modalId).classList.add('modal--active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('modal--active');
    document.body.style.overflow = '';
}

// Table management functions
function viewTable(tableName) {
    const tableStructures = {
        'users': {
            columns: [
                { name: 'id', type: 'INT(11)', key: 'PRI', null: 'NO', extra: 'auto_increment' },
                { name: 'username', type: 'VARCHAR(50)', key: '', null: 'NO', extra: '' },
                { name: 'email', type: 'VARCHAR(100)', key: 'UNI', null: 'NO', extra: '' },
                { name: 'created_at', type: 'TIMESTAMP', key: '', null: 'YES', extra: '' },
                { name: 'updated_at', type: 'TIMESTAMP', key: '', null: 'YES', extra: '' }
            ],
            sampleData: [
                { id: 1, username: 'john_doe', email: 'john@example.com', created_at: '2025-01-15 10:30:00' },
                { id: 2, username: 'jane_smith', email: 'jane@example.com', created_at: '2025-01-16 14:22:00' },
                { id: 3, username: 'bob_wilson', email: 'bob@example.com', created_at: '2025-01-17 09:15:00' }
            ]
        },
        'products': {
            columns: [
                { name: 'id', type: 'INT(11)', key: 'PRI', null: 'NO', extra: 'auto_increment' },
                { name: 'name', type: 'VARCHAR(200)', key: '', null: 'NO', extra: '' },
                { name: 'price', type: 'DECIMAL(10,2)', key: '', null: 'NO', extra: '' },
                { name: 'category_id', type: 'INT(11)', key: 'MUL', null: 'YES', extra: '' },
                { name: 'created_at', type: 'TIMESTAMP', key: '', null: 'YES', extra: '' }
            ],
            sampleData: [
                { id: 1, name: 'Laptop Computer', price: '999.99', category_id: 1, created_at: '2025-01-10 12:00:00' },
                { id: 2, name: 'Wireless Mouse', price: '29.99', category_id: 1, created_at: '2025-01-11 15:30:00' },
                { id: 3, name: 'Office Chair', price: '199.99', category_id: 2, created_at: '2025-01-12 11:45:00' }
            ]
        }
    };
    
    const table = tableStructures[tableName] || tableStructures['users'];
    
    const structureHtml = `
        <div style="margin-bottom: var(--space-lg);">
            <h4 style="margin-bottom: var(--space-md); color: var(--color-text-primary);">Table Structure</h4>
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Column</th>
                            <th class="table__header-cell">Type</th>
                            <th class="table__header-cell">Key</th>
                            <th class="table__header-cell">Null</th>
                            <th class="table__header-cell">Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${table.columns.map(col => `
                            <tr class="table__row">
                                <td class="table__cell">${col.name}</td>
                                <td class="table__cell">${col.type}</td>
                                <td class="table__cell">${col.key}</td>
                                <td class="table__cell">${col.null}</td>
                                <td class="table__cell">${col.extra}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
        
        <div>
            <h4 style="margin-bottom: var(--space-md); color: var(--color-text-primary);">Sample Data</h4>
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            ${table.columns.map(col => `<th class="table__header-cell">${col.name}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${table.sampleData.map(row => `
                            <tr class="table__row">
                                ${table.columns.map(col => `<td class="table__cell">${row[col.name] || ''}</td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    document.getElementById('tableDetailsTitle').textContent = `${tableName} - Table Details`;
    document.getElementById('tableDetailsContent').innerHTML = structureHtml;
    openModal('tableDetailsModal');
}

function editTable(tableName, event) {
    event.stopPropagation();
    alert(`Edit table: ${tableName}`);
}

function viewRelations(tableName, event) {
    event.stopPropagation();
    alert(`View relations for table: ${tableName}`);
}

function editView(viewName, event) {
    event.stopPropagation();
    alert(`Edit view: ${viewName}`);
}

function refreshView(viewName, event) {
    event.stopPropagation();
    alert(`Refresh view: ${viewName}`);
}

function addColumn() {
    const container = document.getElementById('columnsContainer');
    const columnRow = document.createElement('div');
    columnRow.className = 'column-row';
    columnRow.style.cssText = 'display: grid; grid-template-columns: 200px 120px 200px 100px 40px; gap: var(--space-sm); align-items: end; margin-bottom: var(--space-sm);';
    
    columnRow.innerHTML = `
        <div class="form__group" style="margin: 0;">
            <input type="text" class="form__input" placeholder="column_name">
        </div>
        <div class="form__group" style="margin: 0;">
            <select class="form__select">
                <option value="VARCHAR">VARCHAR</option>
                <option value="INT">INT</option>
                <option value="TEXT">TEXT</option>
                <option value="DATETIME">DATETIME</option>
                <option value="BOOLEAN">BOOLEAN</option>
                <option value="DECIMAL">DECIMAL</option>
            </select>
        </div>
        <div class="form__group" style="margin: 0;">
            <input type="text" class="form__input" placeholder="255">
        </div>
        <div class="form__group" style="margin: 0;">
            <div style="display: flex; flex-direction: column; gap: 2px;">
                <label style="font-size: var(--font-size-xs); display: flex; align-items: center; gap: 4px;">
                    <input type="checkbox"> PK
                </label>
                <label style="font-size: var(--font-size-xs); display: flex; align-items: center; gap: 4px;">
                    <input type="checkbox"> AI
                </label>
            </div>
        </div>
        <div style="display: flex; align-items: end;">
            <button type="button" class="btn btn--small btn--danger" onclick="removeColumn(this)">×</button>
        </div>
    `;
    
    container.appendChild(columnRow);
}

function removeColumn(button) {
    button.closest('.column-row').remove();
}

function createTable() {
    alert('Creating table... This would generate and execute the CREATE TABLE SQL statement.');
    closeModal('createTableModal');
}

function importTable() {
    alert('Importing table... This would process the uploaded file and create/populate the table.');
    closeModal('importTableModal');
}

function refreshTables() {
    alert('Refreshing table list...');
}

function exportTable() {
    alert('Exporting table data...');
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
@endsection
