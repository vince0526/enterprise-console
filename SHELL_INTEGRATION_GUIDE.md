# EMC Shell Integration Guide

## Overview

The EMC Shell Integration provides enhanced command detection, aliases, and development workflow automation for the Enterprise Management Console project.

## Installation

### Windows (PowerShell)

1. **Automatic Installation (Recommended)**
   ```powershell
   .\setup-shell-integration.bat
   ```
   
   This will:
   - Check and configure PowerShell execution policy
   - Add integration to your PowerShell profile
   - Test the installation
   - Provide activation instructions

2. **Manual Installation**
   ```powershell
   # Add to your PowerShell profile
   echo ". '$PWD\shell-integration.ps1'" >> $PROFILE
   
   # Or add manually to: $PROFILE
   . "C:\path\to\enterprise-console\shell-integration.ps1"
   ```

### Linux/macOS (Bash/Zsh)

1. **Automatic Installation**
   ```bash
   ./install-shell-integration.sh
   ```

2. **Manual Installation**
   ```bash
   # Add to ~/.bashrc or ~/.zshrc
   echo "source '/path/to/enterprise-console/shell-integration.sh'" >> ~/.bashrc
   ```

## Available Commands

### Core Development Commands

| Command | Description | Example |
|---------|-------------|---------|
| `emc-serve` | Start Laravel development server | `emc-serve` |
| `emc-test` | Run PHPUnit tests | `emc-test` |
| `emc-migrate` | Run database migrations | `emc-migrate` |
| `emc-fresh` | Fresh migration with seeders | `emc-fresh` |

### Code Quality Commands

| Command | Description | Example |
|---------|-------------|---------|
| `emc-pint` | Run Laravel Pint formatter | `emc-pint` |
| `emc-stan` | Run PHPStan static analysis | `emc-stan` |
| `emc-quality` | Run all quality checks (Pint, PHPStan, Tests) | `emc-quality` |

### Git & Deployment Commands

| Command | Description | Example |
|---------|-------------|---------|
| `emc-status` | Check git status | `emc-status` |
| `emc-deploy` | Complete deployment workflow | `emc-deploy` |

### Activity Logging Commands

| Command | Description | Example |
|---------|-------------|---------|
| `emc-activity` | Log development activity | `emc-activity FEATURE "Added user authentication"` |
| `emc-log-view` | View recent activity logs | `emc-log-view` |

## Activity Types

When using `emc-activity`, use one of these activity types:

- **FEATURE** - New functionality
- **BUGFIX** - Bug fixes
- **REFACTOR** - Code refactoring
- **TESTING** - Test-related changes
- **DEPLOYMENT** - Deployment activities
- **SETUP** - Environment or configuration setup
- **DOCUMENTATION** - Documentation updates

## Usage Examples

### Basic Development Workflow

```powershell
# Start development server
emc-serve

# Make code changes...
# Run quality checks
emc-quality

# Log your activity
emc-activity FEATURE "Added Database Management module"

# Deploy changes
emc-deploy
```

### Quality Assurance Workflow

```powershell
# Run individual tools
emc-pint        # Format code
emc-stan        # Static analysis
emc-test        # Run tests

# Or run all at once
emc-quality
```

### Activity Tracking

```powershell
# Log different types of activities
emc-activity FEATURE "Implemented user roles"
emc-activity BUGFIX "Fixed login validation"
emc-activity REFACTOR "Optimized database queries"
emc-activity TESTING "Added integration tests"

# View recent activities
emc-log-view
```

## Features

### Enhanced Command Detection
- All EMC commands are properly aliased and detected
- Cross-platform compatibility (Windows PowerShell, Bash, Zsh)
- Command completion and suggestions

### Automatic Activity Logging
- Integrated with the development logging system
- Tracks activities across multiple computers
- Maintains development history with timestamps

### Visual Feedback
- Color-coded output for better readability
- Progress indicators for long-running tasks
- Clear success/error messages

### Git Integration
- Enhanced git commands specific to EMC workflow
- Streamlined deployment process
- Activity logging for git operations

## Troubleshooting

### Windows PowerShell Issues

1. **Execution Policy Errors**
   ```powershell
   Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```

2. **Profile Not Loading**
   - Check if profile exists: `Test-Path $PROFILE`
   - Create if missing: `New-Item $PROFILE -Force`
   - Add integration manually

3. **Command Not Found**
   - Reload profile: `. $PROFILE`
   - Or restart PowerShell

### Linux/macOS Issues

1. **Permission Denied**
   ```bash
   chmod +x shell-integration.sh
   chmod +x install-shell-integration.sh
   ```

2. **Commands Not Available**
   ```bash
   source ~/.bashrc  # or ~/.zshrc
   ```

## Customization

### Adding New Commands

Edit `shell-integration.ps1` (Windows) or `shell-integration.sh` (Linux/macOS):

```powershell
# Add new function
function emc-custom {
    Write-Host "Running custom command..." -ForegroundColor Green
    # Your command here
}
```

### Modifying Activity Types

Update the activity list in the `emc-activity` function to include your custom types.

## Support

If you encounter issues:

1. Check that you're in the EMC project directory
2. Verify the integration files exist and are executable
3. Restart your terminal/shell
4. Check the activity logs for error details

For additional help, refer to the main EMC documentation or project README.