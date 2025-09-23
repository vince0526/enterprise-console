<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Replication & Clustering</h1>
        <span class="status status--success">
            <span>●</span>
            3 clusters active
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="openModal('createClusterModal')">
            <span>+</span>
            Create Cluster
        </button>
        <button class="btn btn--secondary" onclick="openModal('replicationSetupModal')">
            <span>🔄</span>
            Setup Replication
        </button>
        <button class="btn btn--secondary" onclick="refreshClusterStatus()">
            <span>↻</span>
            Refresh
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-lg); margin-bottom: var(--space-lg);">
    <!-- Cluster Overview -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Cluster Overview</h2>
            <span class="badge badge--success">All Healthy</span>
        </div>
        <div class="card__content">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-md); margin-bottom: var(--space-lg);">
                <div class="metric">
                    <div class="metric__value">3</div>
                    <div class="metric__label">Active Clusters</div>
                </div>
                <div class="metric">
                    <div class="metric__value">12</div>
                    <div class="metric__label">Total Nodes</div>
                </div>
                <div class="metric">
                    <div class="metric__value">99.9%</div>
                    <div class="metric__label">Uptime</div>
                </div>
            </div>
            
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Cluster</th>
                            <th class="table__header-cell">Status</th>
                            <th class="table__header-cell">Nodes</th>
                            <th class="table__header-cell">Lag</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row">
                            <td class="table__cell">
                                <strong>Production Primary</strong>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">mysql-prod-cluster</div>
                            </td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Healthy
                                </span>
                            </td>
                            <td class="table__cell">5 active</td>
                            <td class="table__cell">0.2ms</td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">
                                <strong>Analytics Cluster</strong>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">postgres-analytics</div>
                            </td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Healthy
                                </span>
                            </td>
                            <td class="table__cell">4 active</td>
                            <td class="table__cell">1.1ms</td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">
                                <strong>Development</strong>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">mysql-dev-cluster</div>
                            </td>
                            <td class="table__cell">
                                <span class="status status--warning">
                                    <span>●</span>
                                    Warning
                                </span>
                            </td>
                            <td class="table__cell">3 active</td>
                            <td class="table__cell">5.8ms</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Replication Status -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Replication Status</h2>
            <button class="btn btn--small btn--secondary" onclick="openModal('replicationConfigModal')">Configure</button>
        </div>
        <div class="card__content">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-md); margin-bottom: var(--space-lg);">
                <div class="metric">
                    <div class="metric__value">8</div>
                    <div class="metric__label">Active Replicas</div>
                </div>
                <div class="metric">
                    <div class="metric__value">0.8s</div>
                    <div class="metric__label">Avg Lag</div>
                </div>
            </div>
            
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Master</th>
                            <th class="table__header-cell">Replica</th>
                            <th class="table__header-cell">Status</th>
                            <th class="table__header-cell">Lag</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row">
                            <td class="table__cell">prod-master-01</td>
                            <td class="table__cell">prod-replica-01</td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Syncing
                                </span>
                            </td>
                            <td class="table__cell">0.1s</td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">prod-master-01</td>
                            <td class="table__cell">prod-replica-02</td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Syncing
                                </span>
                            </td>
                            <td class="table__cell">0.3s</td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">analytics-master</td>
                            <td class="table__cell">analytics-replica</td>
                            <td class="table__cell">
                                <span class="status status--warning">
                                    <span>●</span>
                                    Lag Warning
                                </span>
                            </td>
                            <td class="table__cell">2.5s</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Node Details -->
<div class="card">
    <div class="card__header">
        <h2 class="card__title">Node Details</h2>
        <div style="display: flex; gap: var(--space-sm);">
            <select id="clusterFilter" style="padding: 6px 12px; border: 1px solid var(--color-border-primary); border-radius: var(--radius-sm); background: var(--color-background-secondary); color: var(--color-text-primary);">
                <option value="all">All Clusters</option>
                <option value="mysql-prod">Production Primary</option>
                <option value="postgres-analytics">Analytics Cluster</option>
                <option value="mysql-dev">Development</option>
            </select>
            <button class="btn btn--small btn--secondary" onclick="openModal('nodeMaintenanceModal')">
                Maintenance Mode
            </button>
        </div>
    </div>
    <div class="card__content">
        <div class="table-container">
            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th class="table__header-cell">Node</th>
                        <th class="table__header-cell">Role</th>
                        <th class="table__header-cell">Status</th>
                        <th class="table__header-cell">CPU</th>
                        <th class="table__header-cell">Memory</th>
                        <th class="table__header-cell">Connections</th>
                        <th class="table__header-cell">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table__row">
                        <td class="table__cell">
                            <strong>prod-master-01</strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">10.0.1.10:3306</div>
                        </td>
                        <td class="table__cell">
                            <span class="badge badge--primary">Master</span>
                        </td>
                        <td class="table__cell">
                            <span class="status status--success">
                                <span>●</span>
                                Online
                            </span>
                        </td>
                        <td class="table__cell">
                            <div class="progress-bar" style="width: 100px;">
                                <div class="progress-bar__fill" style="width: 45%; background: var(--color-success);"></div>
                            </div>
                            45%
                        </td>
                        <td class="table__cell">
                            <div class="progress-bar" style="width: 100px;">
                                <div class="progress-bar__fill" style="width: 68%; background: var(--color-warning);"></div>
                            </div>
                            68%
                        </td>
                        <td class="table__cell">234/1000</td>
                        <td class="table__cell">
                            <div class="table__actions">
                                <button class="btn btn--small btn--secondary" onclick="viewNodeDetails('prod-master-01')">Details</button>
                                <button class="btn btn--small btn--warning" onclick="failoverNode('prod-master-01')">Failover</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table__row">
                        <td class="table__cell">
                            <strong>prod-replica-01</strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">10.0.1.11:3306</div>
                        </td>
                        <td class="table__cell">
                            <span class="badge badge--secondary">Replica</span>
                        </td>
                        <td class="table__cell">
                            <span class="status status--success">
                                <span>●</span>
                                Online
                            </span>
                        </td>
                        <td class="table__cell">
                            <div class="progress-bar" style="width: 100px;">
                                <div class="progress-bar__fill" style="width: 32%; background: var(--color-success);"></div>
                            </div>
                            32%
                        </td>
                        <td class="table__cell">
                            <div class="progress-bar" style="width: 100px;">
                                <div class="progress-bar__fill" style="width: 54%; background: var(--color-success);"></div>
                            </div>
                            54%
                        </td>
                        <td class="table__cell">187/1000</td>
                        <td class="table__cell">
                            <div class="table__actions">
                                <button class="btn btn--small btn--secondary" onclick="viewNodeDetails('prod-replica-01')">Details</button>
                                <button class="btn btn--small btn--primary" onclick="promoteReplica('prod-replica-01')">Promote</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="table__row">
                        <td class="table__cell">
                            <strong>analytics-master</strong>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">10.0.2.10:5432</div>
                        </td>
                        <td class="table__cell">
                            <span class="badge badge--primary">Master</span>
                        </td>
                        <td class="table__cell">
                            <span class="status status--success">
                                <span>●</span>
                                Online
                            </span>
                        </td>
                        <td class="table__cell">
                            <div class="progress-bar" style="width: 100px;">
                                <div class="progress-bar__fill" style="width: 78%; background: var(--color-warning);"></div>
                            </div>
                            78%
                        </td>
                        <td class="table__cell">
                            <div class="progress-bar" style="width: 100px;">
                                <div class="progress-bar__fill" style="width: 82%; background: var(--color-danger);"></div>
                            </div>
                            82%
                        </td>
                        <td class="table__cell">456/500</td>
                        <td class="table__cell">
                            <div class="table__actions">
                                <button class="btn btn--small btn--secondary" onclick="viewNodeDetails('analytics-master')">Details</button>
                                <button class="btn btn--small btn--warning" onclick="scaleNode('analytics-master')">Scale</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Cluster Modal -->
<div class="modal" id="createClusterModal">
    <div class="modal__backdrop" onclick="closeModal('createClusterModal')"></div>
    <div class="modal__container" style="width: 600px;">
        <div class="modal__header">
            <h3 class="modal__title">Create New Cluster</h3>
            <button class="modal__close" onclick="closeModal('createClusterModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="createClusterForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="clusterName">Cluster Name</label>
                    <input type="text" id="clusterName" class="form__input" placeholder="e.g., mysql-prod-cluster" required>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="dbType">Database Type</label>
                    <select id="dbType" class="form__input" required>
                        <option value="">Select database type</option>
                        <option value="mysql">MySQL</option>
                        <option value="postgresql">PostgreSQL</option>
                        <option value="mongodb">MongoDB</option>
                        <option value="redis">Redis</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="nodeCount">Number of Nodes</label>
                    <select id="nodeCount" class="form__input" required>
                        <option value="3">3 nodes (Minimum HA)</option>
                        <option value="5">5 nodes (Recommended)</option>
                        <option value="7">7 nodes (High Availability)</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="clusterTopology">Topology</label>
                    <select id="clusterTopology" class="form__input" required>
                        <option value="master-slave">Master-Slave</option>
                        <option value="master-master">Master-Master</option>
                        <option value="galera">Galera Cluster</option>
                        <option value="sharded">Sharded Cluster</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="autoFailover">
                        <input type="checkbox" id="autoFailover" checked>
                        Enable Auto-Failover
                    </label>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('createClusterModal')">Cancel</button>
            <button class="btn btn--primary" onclick="createCluster()">Create Cluster</button>
        </div>
    </div>
</div>

<!-- Replication Setup Modal -->
<div class="modal" id="replicationSetupModal">
    <div class="modal__backdrop" onclick="closeModal('replicationSetupModal')"></div>
    <div class="modal__container" style="width: 700px;">
        <div class="modal__header">
            <h3 class="modal__title">Setup Database Replication</h3>
            <button class="modal__close" onclick="closeModal('replicationSetupModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="replicationSetupForm">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-lg);">
                    <div>
                        <h4>Master Database</h4>
                        <div class="form__group">
                            <label class="form__label" for="masterHost">Host</label>
                            <input type="text" id="masterHost" class="form__input" placeholder="10.0.1.10">
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="masterPort">Port</label>
                            <input type="number" id="masterPort" class="form__input" value="3306">
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="masterUser">Username</label>
                            <input type="text" id="masterUser" class="form__input" placeholder="replication_user">
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="masterPassword">Password</label>
                            <input type="password" id="masterPassword" class="form__input">
                        </div>
                    </div>
                    
                    <div>
                        <h4>Replica Database</h4>
                        <div class="form__group">
                            <label class="form__label" for="replicaHost">Host</label>
                            <input type="text" id="replicaHost" class="form__input" placeholder="10.0.1.11">
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="replicaPort">Port</label>
                            <input type="number" id="replicaPort" class="form__input" value="3306">
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="replicaUser">Username</label>
                            <input type="text" id="replicaUser" class="form__input" placeholder="replica_user">
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="replicaPassword">Password</label>
                            <input type="password" id="replicaPassword" class="form__input">
                        </div>
                    </div>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="replicationMode">Replication Mode</label>
                    <select id="replicationMode" class="form__input">
                        <option value="async">Asynchronous</option>
                        <option value="semi-sync">Semi-Synchronous</option>
                        <option value="sync">Synchronous</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label">Options</label>
                    <div style="display: flex; flex-direction: column; gap: var(--space-xs);">
                        <label><input type="checkbox" checked> Enable Binary Logging</label>
                        <label><input type="checkbox" checked> GTID-based Replication</label>
                        <label><input type="checkbox"> SSL Encryption</label>
                        <label><input type="checkbox"> Automatic Failover</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('replicationSetupModal')">Cancel</button>
            <button class="btn btn--primary" onclick="setupReplication()">Setup Replication</button>
        </div>
    </div>
</div>

<!-- Node Maintenance Modal -->
<div class="modal" id="nodeMaintenanceModal">
    <div class="modal__backdrop" onclick="closeModal('nodeMaintenanceModal')"></div>
    <div class="modal__container" style="width: 500px;">
        <div class="modal__header">
            <h3 class="modal__title">Node Maintenance</h3>
            <button class="modal__close" onclick="closeModal('nodeMaintenanceModal')">&times;</button>
        </div>
        <div class="modal__body">
            <div class="form__group">
                <label class="form__label" for="maintenanceNode">Select Node</label>
                <select id="maintenanceNode" class="form__input">
                    <option value="prod-master-01">prod-master-01 (Master)</option>
                    <option value="prod-replica-01">prod-replica-01 (Replica)</option>
                    <option value="prod-replica-02">prod-replica-02 (Replica)</option>
                    <option value="analytics-master">analytics-master (Master)</option>
                </select>
            </div>
            
            <div class="form__group">
                <label class="form__label" for="maintenanceAction">Action</label>
                <select id="maintenanceAction" class="form__input">
                    <option value="enable">Enable Maintenance Mode</option>
                    <option value="disable">Disable Maintenance Mode</option>
                    <option value="restart">Restart Node</option>
                    <option value="stop">Stop Node</option>
                </select>
            </div>
            
            <div class="form__group">
                <label class="form__label" for="maintenanceReason">Reason</label>
                <textarea id="maintenanceReason" class="form__input" rows="3" placeholder="Reason for maintenance..."></textarea>
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('nodeMaintenanceModal')">Cancel</button>
            <button class="btn btn--warning" onclick="performMaintenance()">Execute</button>
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

// Cluster management
function createCluster() {
    const clusterName = document.getElementById('clusterName').value;
    const dbType = document.getElementById('dbType').value;
    const nodeCount = document.getElementById('nodeCount').value;
    
    if (clusterName && dbType && nodeCount) {
        alert(`Creating ${dbType} cluster "${clusterName}" with ${nodeCount} nodes...`);
        closeModal('createClusterModal');
        document.getElementById('createClusterForm').reset();
    }
}

function setupReplication() {
    const masterHost = document.getElementById('masterHost').value;
    const replicaHost = document.getElementById('replicaHost').value;
    
    if (masterHost && replicaHost) {
        alert(`Setting up replication from ${masterHost} to ${replicaHost}...`);
        closeModal('replicationSetupModal');
        document.getElementById('replicationSetupForm').reset();
    }
}

function performMaintenance() {
    const node = document.getElementById('maintenanceNode').value;
    const action = document.getElementById('maintenanceAction').value;
    const reason = document.getElementById('maintenanceReason').value;
    
    if (confirm(`Are you sure you want to ${action} for ${node}?`)) {
        alert(`Executing ${action} on ${node}...`);
        closeModal('nodeMaintenanceModal');
    }
}

// Node actions
function viewNodeDetails(nodeId) {
    alert(`Viewing detailed metrics for ${nodeId}...`);
}

function failoverNode(nodeId) {
    if (confirm(`Are you sure you want to initiate failover for ${nodeId}? This will promote a replica to master.`)) {
        alert(`Initiating failover for ${nodeId}...`);
    }
}

function promoteReplica(nodeId) {
    if (confirm(`Are you sure you want to promote ${nodeId} to master? This will make it the primary node.`)) {
        alert(`Promoting ${nodeId} to master...`);
    }
}

function scaleNode(nodeId) {
    alert(`Opening scaling options for ${nodeId}...`);
}

function refreshClusterStatus() {
    alert('Refreshing cluster status...');
    // In a real application, this would fetch updated status from the server
}

// Cluster filtering
document.getElementById('clusterFilter').addEventListener('change', function() {
    const selectedCluster = this.value;
    alert(`Filtering nodes for cluster: ${selectedCluster}`);
    // In a real application, this would filter the node table
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

// Auto-refresh cluster status every 30 seconds
setInterval(function() {
    // Update cluster metrics
    console.log('Auto-refreshing cluster status...');
}, 30000);
</script>