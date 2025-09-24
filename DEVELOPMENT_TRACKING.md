# 🖥️ Multi-Computer Development Tracking System

## Overview
This system automatically detects each computer and logs development activities, creating a comprehensive history of work done across all your machines.

## Current Computer Detected
- **Computer Name**: VINCEV
- **User**: vince
- **OS**: Windows 10 Home Single Language
- **Development Environment**: PHP 8.3.16, Composer 2.8.11
- **Git User**: vince0526 <vincentvillanueva@gmail.com>

## Quick Usage

### Log Development Activity
```cmd
# Windows - Simple method (works always)
powershell -ExecutionPolicy Bypass -File log-activity.ps1 -Activity "FEATURE" -Description "Your description here"

# Windows - Advanced method (if PowerShell policy allows)
.\dev-log-tracker.ps1 -Activity "FEATURE" -Description "Your description here"

# Linux/Mac
./dev-log-tracker.sh -a FEATURE -d "Your description here"
```

### View Recent Activity
```cmd
# View this computer's recent activity
type "logs\computer-profiles\VINCEV-activity.log"

# Or use the script (if PowerShell allows)
.\dev-log-tracker.ps1 -ShowLog
```

### View All Computers Summary
```cmd
# View activity across all computers
.\dev-log-tracker.ps1 -Summary

# Linux/Mac
./dev-log-tracker.sh --summary
```

## Activity Types
- **FEATURE** - New feature development
- **BUGFIX** - Bug fixes and corrections
- **REFACTOR** - Code refactoring and improvements
- **TESTING** - Testing activities
- **DEPLOYMENT** - Deployment and synchronization
- **SETUP** - Environment setup and configuration
- **DOCUMENTATION** - Documentation updates
- **OTHER** - General development activities

## Files Created
- `logs/computer-profiles/[COMPUTER_NAME].md` - Computer profile
- `logs/computer-profiles/[COMPUTER_NAME]-activity.log` - Activity log
- `logs/activity-summary.md` - Consolidated activity summary

## Cross-Platform Support
- **Windows**: PowerShell scripts with execution policy bypass
- **Linux/Mac**: Bash scripts with automatic system detection
- **Automatic Detection**: Computer name, user, OS, and development tools

## When to Log Activities
1. **Starting work** on a new computer
2. **Completing features** or major changes
3. **Before switching** between computers
4. **After deployments** or syncing
5. **When troubleshooting** to trace development history

## Benefits
✅ **Track work location** - Know which computer was used for each feature  
✅ **Development history** - Complete timeline of all development activities  
✅ **Team coordination** - Multiple developers can track their contributions  
✅ **Debugging aid** - Trace issues back to specific development sessions  
✅ **Project management** - Overview of development progress and time allocation

## Integration with Git
The logging system complements Git by providing:
- **Context**: Why changes were made and where
- **Environment**: What development setup was used
- **Timeline**: When work was done across different machines
- **Coordination**: Multiple computers working on the same project

---

**Next Steps for Other Computers:**
1. Clone this repository 
2. Run the logging script to create computer profile
3. Log activities as you develop
4. Push/pull logs with the rest of the codebase