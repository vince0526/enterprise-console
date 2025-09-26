# EMC Shell Integration - Quick Start

## Installation & Setup

### Windows (PowerShell)
The shell integration is automatically configured when you're in the EMC project directory. The system uses `shell-integration-fast.ps1` for optimal performance.

### Key Features
- **Instant Loading**: Optimized for speed (<100ms load time)
- **VS Code Integration**: Full command detection support
- **EMC Commands**: All development workflow commands available

## Available Commands

| Command | Description |
|---------|-------------|
| `emc-serve` | Start Laravel development server |
| `emc-test` | Run PHPUnit tests |
| `emc-pint` | Run Laravel Pint code formatter |
| `emc-stan` | Run PHPStan static analysis |
| `emc-migrate` | Run database migrations |
| `emc-status` | Check git repository status |
| `emc-activity` | Log development activity |

## Quick Test
```powershell
# Test the integration
. .\shell-integration-fast.ps1

# Test a command
emc-status
```

## Documentation
- See `SHELL_INTEGRATION_FINAL_SOLUTION.md` for complete technical details
- See `SHELL_INTEGRATION_GUIDE.md` for comprehensive usage guide

## Status
✅ **ACTIVE** - VS Code shell integration warning eliminated  
✅ **OPTIMIZED** - Fast loading, zero errors  
✅ **DEPLOYED** - Ready for all development computers