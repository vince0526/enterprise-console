<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Query Runner</h1>
        <span class="status status--info" id="connectionStatus">
            <span>●</span>
            <select id="activeDatabase" style="background: none; border: none; color: inherit; font-size: inherit;">
                <option value="emc_production">emc_production</option>
                <option value="emc_development">emc_development</option>
                <option value="analytics">analytics</option>
            </select>
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="executeQuery()" title="Ctrl+Enter">
            <span>▶</span>
            Execute Query
        </button>
        <button class="btn btn--secondary" onclick="saveQuery()">
            <span>💾</span>
            Save
        </button>
        <button class="btn btn--secondary" onclick="openModal('savedQueriesModal')">
            <span>📝</span>
            Saved Queries
        </button>
        <button class="btn btn--secondary" onclick="exportResults()">
            <span>📊</span>
            Export
        </button>
    </div>
</div>

<div style="display: grid; grid-template-rows: 1fr 1fr; gap: var(--space-lg); height: calc(100vh - 200px);">
    <!-- Query Editor -->
    <div class="card" style="display: flex; flex-direction: column;">
        <div class="card__header">
            <h2 class="card__title">SQL Editor</h2>
            <div style="display: flex; gap: var(--space-sm); align-items: center;">
                <span style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                    Line: <span id="currentLine">1</span>, Col: <span id="currentCol">1</span>
                </span>
                <button class="btn btn--small btn--secondary" onclick="formatQuery()">Format</button>
                <button class="btn btn--small btn--secondary" onclick="clearEditor()">Clear</button>
            </div>
        </div>
        <div class="card__content" style="flex: 1; display: flex; flex-direction: column;">
            <textarea id="sqlEditor" style="
                flex: 1; 
                font-family: 'Courier New', monospace; 
                font-size: var(--font-size-sm); 
                background: var(--color-background-tertiary); 
                color: var(--color-text-primary); 
                border: 1px solid var(--color-border-primary); 
                border-radius: var(--radius-md); 
                padding: var(--space-md); 
                resize: none; 
                line-height: 1.5;
            " placeholder="-- Enter your SQL query here
SELECT 
    id,
    name,
    email,
    created_at
FROM users
WHERE status = 'active'
ORDER BY created_at DESC
LIMIT 10;">-- Sample query to get started
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(o.id) as order_count,
    SUM(o.total) as total_spent
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY u.id, u.name, u.email
HAVING total_spent > 100
ORDER BY total_spent DESC
LIMIT 20;</textarea>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-sm); padding-top: var(--space-sm); border-top: 1px solid var(--color-border-primary);">
                <div style="display: flex; gap: var(--space-md); align-items: center;">
                    <label style="display: flex; align-items: center; gap: var(--space-xs); font-size: var(--font-size-sm);">
                        <input type="checkbox" id="autoFormat"> Auto-format
                    </label>
                    <label style="display: flex; align-items: center; gap: var(--space-xs); font-size: var(--font-size-sm);">
                        <input type="checkbox" id="autoComplete" checked> Auto-complete
                    </label>
                    <label style="display: flex; align-items: center; gap: var(--space-xs); font-size: var(--font-size-sm);">
                        Limit: 
                        <input type="number" id="resultLimit" value="1000" min="1" max="10000" style="width: 80px; padding: 2px 4px; border: 1px solid var(--color-border-primary); border-radius: 3px; background: var(--color-background-secondary); color: var(--color-text-primary);">
                    </label>
                </div>
                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">
                    Execution time: <span id="executionTime">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Query Results -->
    <div class="card" style="display: flex; flex-direction: column;">
        <div class="card__header">
            <h2 class="card__title">Results</h2>
            <div style="display: flex; gap: var(--space-sm); align-items: center;">
                <span style="font-size: var(--font-size-xs); color: var(--color-text-secondary);" id="resultInfo">
                    No query executed
                </span>
                <button class="btn btn--small btn--secondary" onclick="downloadResults()" id="downloadBtn" disabled>
                    Download CSV
                </button>
            </div>
        </div>
        <div class="card__content" style="flex: 1; overflow: auto;">
            <div id="queryResults">
                <div style="display: flex; align-items: center; justify-content: center; height: 200px; color: var(--color-text-secondary); text-align: center;">
                    <div>
                        <div style="font-size: 2rem; margin-bottom: var(--space-sm);">⚡</div>
                        <div>Execute a query to see results here</div>
                        <div style="font-size: var(--font-size-sm); margin-top: var(--space-xs);">Press Ctrl+Enter or click Execute Query</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Saved Queries Modal -->
<div class="modal" id="savedQueriesModal">
    <div class="modal__backdrop" onclick="closeModal('savedQueriesModal')"></div>
    <div class="modal__container" style="width: 800px;">
        <div class="modal__header">
            <h3 class="modal__title">Saved Queries</h3>
            <button class="modal__close" onclick="closeModal('savedQueriesModal')">&times;</button>
        </div>
        <div class="modal__body">
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Name</th>
                            <th class="table__header-cell">Database</th>
                            <th class="table__header-cell">Created</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row">
                            <td class="table__cell">Monthly Sales Report</td>
                            <td class="table__cell">emc_production</td>
                            <td class="table__cell">2025-09-20</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="loadSavedQuery('monthly_sales')">Load</button>
                                    <button class="btn btn--small btn--secondary" onclick="editSavedQuery('monthly_sales')">Edit</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteSavedQuery('monthly_sales')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">User Activity Analysis</td>
                            <td class="table__cell">analytics</td>
                            <td class="table__cell">2025-09-18</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="loadSavedQuery('user_activity')">Load</button>
                                    <button class="btn btn--small btn--secondary" onclick="editSavedQuery('user_activity')">Edit</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteSavedQuery('user_activity')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">Inventory Low Stock</td>
                            <td class="table__cell">emc_production</td>
                            <td class="table__cell">2025-09-15</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="loadSavedQuery('inventory_low')">Load</button>
                                    <button class="btn btn--small btn--secondary" onclick="editSavedQuery('inventory_low')">Edit</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteSavedQuery('inventory_low')">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('savedQueriesModal')">Close</button>
        </div>
    </div>
</div>

<!-- Save Query Modal -->
<div class="modal" id="saveQueryModal">
    <div class="modal__backdrop" onclick="closeModal('saveQueryModal')"></div>
    <div class="modal__container" style="width: 500px;">
        <div class="modal__header">
            <h3 class="modal__title">Save Query</h3>
            <button class="modal__close" onclick="closeModal('saveQueryModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="saveQueryForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="queryName">Query Name</label>
                    <input type="text" id="queryName" class="form__input" placeholder="Enter query name" required>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="queryDescription">Description</label>
                    <textarea id="queryDescription" class="form__input" rows="3" placeholder="Optional description"></textarea>
                </div>
                
                <div class="form__group">
                    <label class="form__label">Preview</label>
                    <textarea id="queryPreview" class="form__input" rows="6" readonly style="font-family: monospace; font-size: var(--font-size-sm);"></textarea>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('saveQueryModal')">Cancel</button>
            <button class="btn btn--primary" onclick="confirmSaveQuery()">Save Query</button>
        </div>
    </div>
</div>

<script>
// Mock query results for demo
const mockResults = {
    columns: ['id', 'name', 'email', 'order_count', 'total_spent'],
    rows: [
        [1, 'John Doe', 'john@example.com', 5, 250.75],
        [2, 'Jane Smith', 'jane@example.com', 8, 420.50],
        [3, 'Bob Wilson', 'bob@example.com', 3, 185.25],
        [4, 'Alice Johnson', 'alice@example.com', 12, 680.90],
        [5, 'Charlie Brown', 'charlie@example.com', 6, 315.40]
    ]
};

// Modal functionality
function openModal(modalId) {
    document.getElementById(modalId).classList.add('modal--active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('modal--active');
    document.body.style.overflow = '';
}

// Query execution
function executeQuery() {
    const startTime = Date.now();
    const query = document.getElementById('sqlEditor').value.trim();
    
    if (!query) {
        alert('Please enter a query to execute.');
        return;
    }
    
    // Show loading state
    document.getElementById('resultInfo').textContent = 'Executing query...';
    document.getElementById('queryResults').innerHTML = '<div style="text-align: center; padding: var(--space-xl); color: var(--color-text-secondary);">⏳ Executing query...</div>';
    
    // Simulate query execution
    setTimeout(() => {
        const endTime = Date.now();
        const executionTime = endTime - startTime;
        
        // Display results
        displayResults(mockResults, executionTime);
        
        // Update execution time
        document.getElementById('executionTime').textContent = `${executionTime}ms`;
        document.getElementById('downloadBtn').disabled = false;
    }, 1000 + Math.random() * 2000);
}

function displayResults(results, executionTime) {
    const resultInfo = document.getElementById('resultInfo');
    const queryResults = document.getElementById('queryResults');
    
    resultInfo.textContent = `${results.rows.length} rows returned in ${executionTime}ms`;
    
    let html = '<div class="table-container"><table class="table"><thead class="table__header"><tr>';
    
    // Add headers
    results.columns.forEach(col => {
        html += `<th class="table__header-cell">${col}</th>`;
    });
    html += '</tr></thead><tbody>';
    
    // Add rows
    results.rows.forEach(row => {
        html += '<tr class="table__row">';
        row.forEach(cell => {
            html += `<td class="table__cell">${cell}</td>`;
        });
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    queryResults.innerHTML = html;
}

// Query management
function saveQuery() {
    const query = document.getElementById('sqlEditor').value.trim();
    if (!query) {
        alert('No query to save.');
        return;
    }
    
    document.getElementById('queryPreview').value = query;
    openModal('saveQueryModal');
}

function confirmSaveQuery() {
    const name = document.getElementById('queryName').value;
    if (name) {
        alert(`Query "${name}" saved successfully!`);
        closeModal('saveQueryModal');
        document.getElementById('saveQueryForm').reset();
    }
}

function loadSavedQuery(queryId) {
    const queries = {
        monthly_sales: `SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as order_count,
    SUM(total) as total_sales,
    AVG(total) as avg_order_value
FROM orders 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY month
ORDER BY month DESC;`,
        user_activity: `SELECT 
    u.id,
    u.name,
    u.last_login,
    COUNT(DISTINCT o.id) as orders,
    COUNT(DISTINCT s.id) as sessions
FROM users u
LEFT JOIN orders o ON u.id = o.user_id AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
LEFT JOIN user_sessions s ON u.id = s.user_id AND s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY u.id, u.name, u.last_login
ORDER BY orders DESC;`,
        inventory_low: `SELECT 
    p.id,
    p.name,
    p.current_stock,
    p.min_stock_level,
    c.name as category
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.current_stock <= p.min_stock_level
ORDER BY (p.current_stock / p.min_stock_level) ASC;`
    };
    
    document.getElementById('sqlEditor').value = queries[queryId] || '';
    closeModal('savedQueriesModal');
}

function deleteSavedQuery(queryId) {
    if (confirm('Are you sure you want to delete this saved query?')) {
        alert(`Query ${queryId} deleted.`);
    }
}

function editSavedQuery(queryId) {
    loadSavedQuery(queryId);
    closeModal('savedQueriesModal');
    saveQuery();
}

// Editor functions
function formatQuery() {
    const editor = document.getElementById('sqlEditor');
    // Basic formatting - in a real app, you'd use a proper SQL formatter
    let formatted = editor.value
        .replace(/\s+/g, ' ')
        .replace(/,/g, ',\n    ')
        .replace(/SELECT/gi, 'SELECT\n    ')
        .replace(/FROM/gi, '\nFROM ')
        .replace(/WHERE/gi, '\nWHERE ')
        .replace(/ORDER BY/gi, '\nORDER BY ')
        .replace(/GROUP BY/gi, '\nGROUP BY ')
        .replace(/HAVING/gi, '\nHAVING ')
        .replace(/LIMIT/gi, '\nLIMIT ');
    
    editor.value = formatted;
}

function clearEditor() {
    if (confirm('Are you sure you want to clear the editor?')) {
        document.getElementById('sqlEditor').value = '';
        document.getElementById('queryResults').innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 200px; color: var(--color-text-secondary); text-align: center;"><div><div style="font-size: 2rem; margin-bottom: var(--space-sm);">⚡</div><div>Execute a query to see results here</div></div></div>';
        document.getElementById('resultInfo').textContent = 'No query executed';
        document.getElementById('executionTime').textContent = '-';
        document.getElementById('downloadBtn').disabled = true;
    }
}

function exportResults() {
    alert('Exporting results to CSV...');
}

function downloadResults() {
    alert('Downloading query results as CSV...');
}

// Keyboard shortcuts
document.getElementById('sqlEditor').addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        executeQuery();
    }
    
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        saveQuery();
    }
    
    // Update cursor position
    setTimeout(() => {
        const textarea = this;
        const text = textarea.value;
        const cursorPos = textarea.selectionStart;
        const textBeforeCursor = text.substring(0, cursorPos);
        const lines = textBeforeCursor.split('\n');
        const currentLine = lines.length;
        const currentCol = lines[lines.length - 1].length + 1;
        
        document.getElementById('currentLine').textContent = currentLine;
        document.getElementById('currentCol').textContent = currentCol;
    }, 0);
});

// Database selection
document.getElementById('activeDatabase').addEventListener('change', function() {
    const database = this.value;
    document.getElementById('connectionStatus').innerHTML = `<span>●</span> ${database}`;
});

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