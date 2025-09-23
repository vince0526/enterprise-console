<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">Backup & Restore</h1>
        <span class="status status--success">
            <span>●</span>
            Last Backup: 2 hours ago
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="openModal('createBackupModal')">
            <span>💾</span>
            Create Backup
        </button>
        <button class="btn btn--secondary" onclick="openModal('scheduleBackupModal')">
            <span>⏰</span>
            Schedule
        </button>
        <button class="btn btn--secondary" onclick="refreshBackups()">
            <span>↻</span>
            Refresh
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: var(--space-lg);">
    <!-- Backup List -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Backup History</h2>
            <p class="card__subtitle">Database backups and restore points</p>
        </div>
        <div class="card__content">
            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Database</th>
                            <th class="table__header-cell">Backup Time</th>
                            <th class="table__header-cell">Size</th>
                            <th class="table__header-cell">Type</th>
                            <th class="table__header-cell">Status</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table__row">
                            <td class="table__cell">emc_production</td>
                            <td class="table__cell">2025-09-22 14:00:00</td>
                            <td class="table__cell">342 MB</td>
                            <td class="table__cell">
                                <span class="status status--info">Full</span>
                            </td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Completed
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="restoreBackup('backup_001', event)">Restore</button>
                                    <button class="btn btn--small btn--secondary" onclick="downloadBackup('backup_001', event)">Download</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteBackup('backup_001', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">emc_development</td>
                            <td class="table__cell">2025-09-22 13:30:00</td>
                            <td class="table__cell">125 MB</td>
                            <td class="table__cell">
                                <span class="status status--warning">Incremental</span>
                            </td>
                            <td class="table__cell">
                                <span class="status status--success">
                                    <span>●</span>
                                    Completed
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--primary" onclick="restoreBackup('backup_002', event)">Restore</button>
                                    <button class="btn btn--small btn--secondary" onclick="downloadBackup('backup_002', event)">Download</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteBackup('backup_002', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row">
                            <td class="table__cell">analytics</td>
                            <td class="table__cell">2025-09-22 12:00:00</td>
                            <td class="table__cell">-</td>
                            <td class="table__cell">
                                <span class="status status--info">Full</span>
                            </td>
                            <td class="table__cell">
                                <span class="status status--error">
                                    <span>●</span>
                                    Failed
                                </span>
                            </td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="retryBackup('backup_003', event)">Retry</button>
                                    <button class="btn btn--small btn--secondary" onclick="viewBackupLogs('backup_003', event)">Logs</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteBackup('backup_003', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Backup Settings & Stats -->
    <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
        <!-- Quick Stats -->
        <div class="card">
            <div class="card__header">
                <h2 class="card__title">Backup Statistics</h2>
            </div>
            <div class="card__content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-success);">15</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Total Backups</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-primary);">2.3 GB</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Total Size</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-warning);">3</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Scheduled</div>
                    </div>
                    <div style="text-align: center; padding: var(--space-md); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                        <div style="font-size: var(--font-size-xl); font-weight: 600; color: var(--color-error);">1</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">Failed Today</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Locations -->
        <div class="card">
            <div class="card__header">
                <h2 class="card__title">Storage Locations</h2>
            </div>
            <div class="card__content">
                <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                    <div style="padding: var(--space-sm); border-left: 3px solid var(--color-success); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                        <div style="font-weight: 500; font-size: var(--font-size-sm);">Local Storage</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">/var/backups/ - 1.2 GB used</div>
                    </div>
                    <div style="padding: var(--space-sm); border-left: 3px solid var(--color-primary); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                        <div style="font-weight: 500; font-size: var(--font-size-sm);">AWS S3</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">s3://backups-bucket/ - 850 MB used</div>
                    </div>
                    <div style="padding: var(--space-sm); border-left: 3px solid var(--color-warning); background: var(--color-background-tertiary); border-radius: var(--radius-md);">
                        <div style="font-weight: 500; font-size: var(--font-size-sm);">FTP Server</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-secondary);">ftp://backup-server/ - 320 MB used</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div class="modal" id="createBackupModal">
    <div class="modal__backdrop" onclick="closeModal('createBackupModal')"></div>
    <div class="modal__container" style="width: 600px;">
        <div class="modal__header">
            <h3 class="modal__title">Create Database Backup</h3>
            <button class="modal__close" onclick="closeModal('createBackupModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="backupForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="backupDatabase">Select Database</label>
                    <select id="backupDatabase" class="form__select" required>
                        <option value="">Choose database</option>
                        <option value="emc_production">emc_production</option>
                        <option value="emc_development">emc_development</option>
                        <option value="analytics">analytics</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="backupType">Backup Type</label>
                    <select id="backupType" class="form__select" required>
                        <option value="full">Full Backup</option>
                        <option value="incremental">Incremental Backup</option>
                        <option value="differential">Differential Backup</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="storageLocation">Storage Location</label>
                    <select id="storageLocation" class="form__select" required>
                        <option value="local">Local Storage (/var/backups/)</option>
                        <option value="s3">AWS S3 (s3://backups-bucket/)</option>
                        <option value="ftp">FTP Server (ftp://backup-server/)</option>
                    </select>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="backupName">Backup Name (Optional)</label>
                    <input type="text" id="backupName" class="form__input" placeholder="Auto-generated if empty">
                </div>
                
                <div class="form__group">
                    <label class="form__label">Backup Options</label>
                    <div style="display: flex; flex-direction: column; gap: var(--space-xs);">
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Compress backup file
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Include stored procedures
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Verify backup integrity
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Send notification on completion
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('createBackupModal')">Cancel</button>
            <button class="btn btn--primary" onclick="createBackup()">Create Backup</button>
        </div>
    </div>
</div>

<!-- Schedule Backup Modal -->
<div class="modal" id="scheduleBackupModal">
    <div class="modal__backdrop" onclick="closeModal('scheduleBackupModal')"></div>
    <div class="modal__container" style="width: 700px;">
        <div class="modal__header">
            <h3 class="modal__title">Schedule Automatic Backups</h3>
            <button class="modal__close" onclick="closeModal('scheduleBackupModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="scheduleForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="scheduleDatabase">Database</label>
                    <select id="scheduleDatabase" class="form__select" required>
                        <option value="">Select database</option>
                        <option value="emc_production">emc_production</option>
                        <option value="emc_development">emc_development</option>
                        <option value="analytics">analytics</option>
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="scheduleFrequency">Frequency</label>
                        <select id="scheduleFrequency" class="form__select" required>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="custom">Custom (Cron)</option>
                        </select>
                    </div>
                    
                    <div class="form__group">
                        <label class="form__label form__label--required" for="scheduleTime">Time</label>
                        <input type="time" id="scheduleTime" class="form__input" value="02:00" required>
                    </div>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="cronExpression">Cron Expression (for custom frequency)</label>
                    <input type="text" id="cronExpression" class="form__input" placeholder="0 2 * * *" disabled>
                    <div class="form__help">Use standard cron format: minute hour day month weekday</div>
                </div>
                
                <div class="form__group">
                    <label class="form__label form__label--required" for="retentionDays">Retention Period (days)</label>
                    <input type="number" id="retentionDays" class="form__input" value="30" min="1" required>
                    <div class="form__help">How long to keep backups before automatic deletion</div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('scheduleBackupModal')">Cancel</button>
            <button class="btn btn--primary" onclick="scheduleBackup()">Schedule Backup</button>
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

// Backup operations
function restoreBackup(backupId, event) {
    event.stopPropagation();
    if (confirm('Are you sure you want to restore this backup? This will overwrite the current database.')) {
        alert(`Restoring backup ${backupId}... This would restore the database to the backup state.`);
    }
}

function downloadBackup(backupId, event) {
    event.stopPropagation();
    alert(`Downloading backup ${backupId}...`);
}

function deleteBackup(backupId, event) {
    event.stopPropagation();
    if (confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
        alert(`Deleting backup ${backupId}...`);
    }
}

function retryBackup(backupId, event) {
    event.stopPropagation();
    alert(`Retrying failed backup ${backupId}...`);
}

function viewBackupLogs(backupId, event) {
    event.stopPropagation();
    alert(`Viewing logs for backup ${backupId}...`);
}

function createBackup() {
    const database = document.getElementById('backupDatabase').value;
    const type = document.getElementById('backupType').value;
    
    if (database && type) {
        alert(`Creating ${type} backup for ${database}...`);
        closeModal('createBackupModal');
    }
}

function scheduleBackup() {
    const database = document.getElementById('scheduleDatabase').value;
    const frequency = document.getElementById('scheduleFrequency').value;
    
    if (database && frequency) {
        alert(`Scheduling ${frequency} backup for ${database}...`);
        closeModal('scheduleBackupModal');
    }
}

function refreshBackups() {
    alert('Refreshing backup list...');
}

// Handle frequency change for cron expression
document.getElementById('scheduleFrequency').addEventListener('change', function() {
    const cronField = document.getElementById('cronExpression');
    if (this.value === 'custom') {
        cronField.disabled = false;
        cronField.required = true;
    } else {
        cronField.disabled = true;
        cronField.required = false;
    }
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