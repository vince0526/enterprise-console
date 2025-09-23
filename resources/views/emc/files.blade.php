@extends('emc.layout')

@section('title', 'File Management - EMC')

@section('content')
<div class="toolbar">
    <div class="toolbar__left">
        <h1 class="toolbar__title">File Management</h1>
        <span class="status status--success">
            <span>●</span>
            Connected: /var/www/enterprise-console
        </span>
    </div>
    <div class="toolbar__right">
        <button class="btn btn--primary" onclick="openModal('uploadModal')">
            <span>↑</span>
            Upload
        </button>
        <button class="btn btn--secondary" onclick="openModal('createFolderModal')">
            <span>+</span>
            New Folder
        </button>
        <button class="btn btn--secondary" onclick="openModal('ftpModal')">
            <span>🌐</span>
            FTP
        </button>
        <button class="btn btn--secondary" onclick="refreshFiles()">
            <span>↻</span>
            Refresh
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 300px 1fr; gap: var(--space-lg);">
    <!-- Directory Tree -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Directory Tree</h2>
        </div>
        <div class="card__content">
            <div id="directoryTree" style="padding: var(--space-sm);">
                <div class="tree-item tree-item--folder tree-item--open" onclick="toggleFolder(this)">
                    <span class="tree-icon">📁</span>
                    <span>enterprise-console</span>
                    <div class="tree-children" style="margin-left: var(--space-md);">
                        <div class="tree-item tree-item--folder" onclick="toggleFolder(this); loadDirectory('app')">
                            <span class="tree-icon">📁</span>
                            <span>app</span>
                            <div class="tree-children" style="margin-left: var(--space-md); display: none;">
                                <div class="tree-item tree-item--file" onclick="selectFile('app/Http/Controllers/Controller.php')">
                                    <span class="tree-icon">📄</span>
                                    <span>Controller.php</span>
                                </div>
                            </div>
                        </div>
                        <div class="tree-item tree-item--folder" onclick="toggleFolder(this); loadDirectory('config')">
                            <span class="tree-icon">📁</span>
                            <span>config</span>
                        </div>
                        <div class="tree-item tree-item--folder" onclick="toggleFolder(this); loadDirectory('resources')">
                            <span class="tree-icon">📁</span>
                            <span>resources</span>
                            <div class="tree-children" style="margin-left: var(--space-md); display: none;">
                                <div class="tree-item tree-item--folder" onclick="toggleFolder(this); loadDirectory('resources/views')">
                                    <span class="tree-icon">📁</span>
                                    <span>views</span>
                                </div>
                                <div class="tree-item tree-item--folder" onclick="toggleFolder(this); loadDirectory('resources/css')">
                                    <span class="tree-icon">📁</span>
                                    <span>css</span>
                                </div>
                            </div>
                        </div>
                        <div class="tree-item tree-item--folder" onclick="toggleFolder(this); loadDirectory('public')">
                            <span class="tree-icon">📁</span>
                            <span>public</span>
                        </div>
                        <div class="tree-item tree-item--file" onclick="selectFile('composer.json')">
                            <span class="tree-icon">📄</span>
                            <span>composer.json</span>
                        </div>
                        <div class="tree-item tree-item--file" onclick="selectFile('package.json')">
                            <span class="tree-icon">📄</span>
                            <span>package.json</span>
                        </div>
                        <div class="tree-item tree-item--file" onclick="selectFile('README.md')">
                            <span class="tree-icon">📄</span>
                            <span>README.md</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Browser -->
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Files</h2>
            <p class="card__subtitle" id="currentPath">/var/www/enterprise-console</p>
        </div>
        <div class="card__content">
            <!-- Breadcrumb Navigation -->
            <div style="margin-bottom: var(--space-md); padding: var(--space-sm); background: var(--color-surface-secondary); border-radius: var(--border-radius); display: flex; align-items: center; gap: var(--space-xs); font-size: var(--font-size-sm);">
                <span onclick="loadDirectory('')" style="cursor: pointer; color: var(--color-primary);">📁 Root</span>
                <span>/</span>
                <span onclick="loadDirectory('enterprise-console')" style="cursor: pointer; color: var(--color-primary);">enterprise-console</span>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead class="table__header">
                        <tr>
                            <th class="table__header-cell">Name</th>
                            <th class="table__header-cell">Type</th>
                            <th class="table__header-cell">Size</th>
                            <th class="table__header-cell">Modified</th>
                            <th class="table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="fileList">
                        <tr class="table__row table__row--clickable" onclick="loadDirectory('app')">
                            <td class="table__cell">
                                <span>📁</span>
                                <span style="margin-left: var(--space-xs);">app</span>
                            </td>
                            <td class="table__cell">Folder</td>
                            <td class="table__cell">-</td>
                            <td class="table__cell">2025-01-15 10:30</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="renameItem('app', 'folder', event)">Rename</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteItem('app', 'folder', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="loadDirectory('config')">
                            <td class="table__cell">
                                <span>📁</span>
                                <span style="margin-left: var(--space-xs);">config</span>
                            </td>
                            <td class="table__cell">Folder</td>
                            <td class="table__cell">-</td>
                            <td class="table__cell">2025-01-14 16:45</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="renameItem('config', 'folder', event)">Rename</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteItem('config', 'folder', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="loadDirectory('resources')">
                            <td class="table__cell">
                                <span>📁</span>
                                <span style="margin-left: var(--space-xs);">resources</span>
                            </td>
                            <td class="table__cell">Folder</td>
                            <td class="table__cell">-</td>
                            <td class="table__cell">2025-01-17 09:20</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="renameItem('resources', 'folder', event)">Rename</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteItem('resources', 'folder', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewFile('composer.json')">
                            <td class="table__cell">
                                <span>📄</span>
                                <span style="margin-left: var(--space-xs);">composer.json</span>
                            </td>
                            <td class="table__cell">JSON</td>
                            <td class="table__cell">2.3 KB</td>
                            <td class="table__cell">2025-01-16 14:22</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editFile('composer.json', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="downloadFile('composer.json', event)">Download</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteItem('composer.json', 'file', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewFile('package.json')">
                            <td class="table__cell">
                                <span>📄</span>
                                <span style="margin-left: var(--space-xs);">package.json</span>
                            </td>
                            <td class="table__cell">JSON</td>
                            <td class="table__cell">1.8 KB</td>
                            <td class="table__cell">2025-01-15 11:15</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editFile('package.json', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="downloadFile('package.json', event)">Download</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteItem('package.json', 'file', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="table__row table__row--clickable" onclick="viewFile('README.md')">
                            <td class="table__cell">
                                <span>📄</span>
                                <span style="margin-left: var(--space-xs);">README.md</span>
                            </td>
                            <td class="table__cell">Markdown</td>
                            <td class="table__cell">4.2 KB</td>
                            <td class="table__cell">2025-01-17 08:30</td>
                            <td class="table__cell">
                                <div class="table__actions">
                                    <button class="btn btn--small btn--secondary" onclick="editFile('README.md', event)">Edit</button>
                                    <button class="btn btn--small btn--secondary" onclick="downloadFile('README.md', event)">Download</button>
                                    <button class="btn btn--small btn--danger" onclick="deleteItem('README.md', 'file', event)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal" id="uploadModal">
    <div class="modal__backdrop" onclick="closeModal('uploadModal')"></div>
    <div class="modal__container" style="width: 600px;">
        <div class="modal__header">
            <h3 class="modal__title">Upload Files</h3>
            <button class="modal__close" onclick="closeModal('uploadModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="uploadForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="uploadFiles">Select Files</label>
                    <input type="file" id="uploadFiles" class="form__input" multiple required>
                    <div class="form__help">You can select multiple files. Maximum size per file: 50MB</div>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="uploadPath">Upload Path</label>
                    <input type="text" id="uploadPath" class="form__input" value="/var/www/enterprise-console" readonly>
                    <div class="form__help">Files will be uploaded to the current directory</div>
                </div>
                
                <div class="form__group">
                    <label class="form__label">Upload Options</label>
                    <div style="display: flex; flex-direction: column; gap: var(--space-xs);">
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Overwrite existing files
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox"> Create backup of existing files
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="checkbox" checked> Preserve file permissions
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('uploadModal')">Cancel</button>
            <button class="btn btn--primary" onclick="uploadFiles()">Upload Files</button>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal" id="createFolderModal">
    <div class="modal__backdrop" onclick="closeModal('createFolderModal')"></div>
    <div class="modal__container" style="width: 500px;">
        <div class="modal__header">
            <h3 class="modal__title">Create New Folder</h3>
            <button class="modal__close" onclick="closeModal('createFolderModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="createFolderForm">
                <div class="form__group">
                    <label class="form__label form__label--required" for="folderName">Folder Name</label>
                    <input type="text" id="folderName" class="form__input" placeholder="Enter folder name" required>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="folderPath">Create In</label>
                    <input type="text" id="folderPath" class="form__input" value="/var/www/enterprise-console" readonly>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('createFolderModal')">Cancel</button>
            <button class="btn btn--primary" onclick="createFolder()">Create Folder</button>
        </div>
    </div>
</div>

<!-- FTP Connection Modal -->
<div class="modal" id="ftpModal">
    <div class="modal__backdrop" onclick="closeModal('ftpModal')"></div>
    <div class="modal__container" style="width: 700px;">
        <div class="modal__header">
            <h3 class="modal__title">FTP Connection</h3>
            <button class="modal__close" onclick="closeModal('ftpModal')">&times;</button>
        </div>
        <div class="modal__body">
            <form class="form" id="ftpForm">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="ftpHost">Host</label>
                        <input type="text" id="ftpHost" class="form__input" placeholder="ftp.example.com" required>
                    </div>
                    
                    <div class="form__group">
                        <label class="form__label form__label--required" for="ftpPort">Port</label>
                        <input type="number" id="ftpPort" class="form__input" value="21" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="ftpUsername">Username</label>
                        <input type="text" id="ftpUsername" class="form__input" placeholder="Enter username" required>
                    </div>
                    
                    <div class="form__group">
                        <label class="form__label form__label--required" for="ftpPassword">Password</label>
                        <input type="password" id="ftpPassword" class="form__input" placeholder="Enter password" required>
                    </div>
                </div>
                
                <div class="form__group">
                    <label class="form__label" for="ftpDirectory">Remote Directory</label>
                    <input type="text" id="ftpDirectory" class="form__input" placeholder="/public_html" value="/">
                </div>
                
                <div class="form__group">
                    <label class="form__label">Connection Type</label>
                    <div style="display: flex; gap: var(--space-md);">
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="radio" name="ftpType" value="ftp" checked> FTP
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="radio" name="ftpType" value="ftps"> FTPS
                        </label>
                        <label style="display: flex; align-items: center; gap: var(--space-xs);">
                            <input type="radio" name="ftpType" value="sftp"> SFTP
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('ftpModal')">Cancel</button>
            <button class="btn btn--secondary" onclick="testFtpConnection()">Test Connection</button>
            <button class="btn btn--primary" onclick="connectFtp()">Connect</button>
        </div>
    </div>
</div>

<!-- File Viewer Modal -->
<div class="modal" id="fileViewerModal">
    <div class="modal__backdrop" onclick="closeModal('fileViewerModal')"></div>
    <div class="modal__container" style="width: 900px; max-width: 95vw; height: 80vh;">
        <div class="modal__header">
            <h3 class="modal__title" id="fileViewerTitle">File Viewer</h3>
            <button class="modal__close" onclick="closeModal('fileViewerModal')">&times;</button>
        </div>
        <div class="modal__body" style="height: calc(80vh - 120px); overflow: auto;">
            <div id="fileContent" style="font-family: monospace; white-space: pre-wrap; font-size: var(--font-size-sm); line-height: 1.5; background: var(--color-surface-secondary); padding: var(--space-md); border-radius: var(--border-radius);">
                <!-- File content will be loaded here -->
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--secondary" onclick="closeModal('fileViewerModal')">Close</button>
            <button class="btn btn--secondary" onclick="downloadCurrentFile()">Download</button>
            <button class="btn btn--primary" onclick="editCurrentFile()">Edit</button>
        </div>
    </div>
</div>

<style>
.tree-item {
    padding: 4px 8px;
    cursor: pointer;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    font-size: var(--font-size-sm);
    line-height: 1.4;
}

.tree-item:hover {
    background: var(--color-surface-secondary);
}

.tree-item--selected {
    background: var(--color-primary);
    color: var(--color-text-inverse);
}

.tree-item--folder {
    font-weight: 500;
}

.tree-icon {
    font-size: 14px;
    width: 16px;
    text-align: center;
}

.tree-children {
    margin-left: var(--space-md);
}

.tree-item--open > .tree-children {
    display: block !important;
}
</style>

<script>
let currentFile = '';

// Modal functionality
function openModal(modalId) {
    document.getElementById(modalId).classList.add('modal--active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('modal--active');
    document.body.style.overflow = '';
}

// Directory tree functions
function toggleFolder(element) {
    event.stopPropagation();
    const children = element.querySelector('.tree-children');
    if (children) {
        children.style.display = children.style.display === 'none' ? 'block' : 'none';
        element.classList.toggle('tree-item--open');
    }
}

function selectFile(filePath) {
    // Remove previous selection
    document.querySelectorAll('.tree-item--selected').forEach(item => {
        item.classList.remove('tree-item--selected');
    });
    // Add selection to current item
    event.currentTarget.classList.add('tree-item--selected');
    viewFile(filePath);
}

// File operations
function loadDirectory(path) {
    document.getElementById('currentPath').textContent = `/var/www/enterprise-console/${path}`;
    // Simulate loading directory contents
    alert(`Loading directory: ${path}`);
}

function viewFile(fileName) {
    currentFile = fileName;
    const fileContents = {
        'composer.json': `{
    "name": "enterprise/console",
    "type": "project",
    "description": "Enterprise Management Console",
    "keywords": ["framework", "laravel"],
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.10",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "laravel/sail": "^1.18",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.1",
        "spatie/laravel-ignition": "^2.0"
    }
}`,
        'package.json': `{
  "name": "enterprise-console",
  "version": "1.0.0",
  "description": "Enterprise Management Console Frontend",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "watch": "vite build --watch"
  },
  "devDependencies": {
    "laravel-vite-plugin": "^0.8.0",
    "vite": "^4.0.0"
  }
}`,
        'README.md': `# Enterprise Management Console

A comprehensive enterprise management solution built with Laravel.

## Features

- Database Management
- File Management
- Report Generation
- AI Integration
- Team Communications
- System Settings

## Installation

1. Clone the repository
2. Run \`composer install\`
3. Run \`npm install\`
4. Configure your \`.env\` file
5. Run \`php artisan migrate\`

## Usage

Visit \`/emc\` to access the management console.

## License

MIT License`
    };
    
    document.getElementById('fileViewerTitle').textContent = fileName;
    document.getElementById('fileContent').textContent = fileContents[fileName] || 'File content not available in demo.';
    openModal('fileViewerModal');
}

function editFile(fileName, event) {
    event.stopPropagation();
    alert(`Edit file: ${fileName}`);
}

function downloadFile(fileName, event) {
    event.stopPropagation();
    alert(`Download file: ${fileName}`);
}

function renameItem(itemName, type, event) {
    event.stopPropagation();
    const newName = prompt(`Enter new name for ${type}:`, itemName);
    if (newName && newName !== itemName) {
        alert(`Rename ${type} "${itemName}" to "${newName}"`);
    }
}

function deleteItem(itemName, type, event) {
    event.stopPropagation();
    if (confirm(`Are you sure you want to delete ${type} "${itemName}"?`)) {
        alert(`Delete ${type}: ${itemName}`);
    }
}

// Modal form functions
function uploadFiles() {
    const files = document.getElementById('uploadFiles').files;
    if (files.length > 0) {
        alert(`Uploading ${files.length} file(s)...`);
        closeModal('uploadModal');
    }
}

function createFolder() {
    const folderName = document.getElementById('folderName').value;
    if (folderName) {
        alert(`Creating folder: ${folderName}`);
        closeModal('createFolderModal');
    }
}

function testFtpConnection() {
    const host = document.getElementById('ftpHost').value;
    if (host) {
        alert(`Testing connection to ${host}...`);
    }
}

function connectFtp() {
    const host = document.getElementById('ftpHost').value;
    const username = document.getElementById('ftpUsername').value;
    if (host && username) {
        alert(`Connecting to FTP: ${username}@${host}`);
        closeModal('ftpModal');
    }
}

function downloadCurrentFile() {
    if (currentFile) {
        alert(`Downloading: ${currentFile}`);
    }
}

function editCurrentFile() {
    if (currentFile) {
        alert(`Opening editor for: ${currentFile}`);
        closeModal('fileViewerModal');
    }
}

function refreshFiles() {
    alert('Refreshing file list...');
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
