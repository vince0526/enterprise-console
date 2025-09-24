# Enterprise Management Console - Development Activity Logger
# Windows PowerShell Version

param(
    [string]$Activity = "",
    [string]$Description = "",
    [switch]$ShowLog,
    [switch]$Summary
)

# Get computer and system information
$ComputerName = $env:COMPUTERNAME
$UserName = $env:USERNAME
$CurrentDate = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
$CurrentPath = Get-Location

# System information
$SystemInfo = Get-ComputerInfo | Select-Object WindowsProductName, TotalPhysicalMemory, CsProcessors
$PHPVersion = ""
try {
    $PHPVersion = & php -v 2>$null | Select-String "^PHP" | ForEach-Object { $_.Line.Split(' ')[1] }
} catch {
    $PHPVersion = "Not installed"
}

$ComposerVersion = ""
try {
    $ComposerVersion = & composer --version 2>$null | ForEach-Object { ($_ -split ' ')[2] }
} catch {
    $ComposerVersion = "Not installed"
}

# Create computer profile file
$ProfilePath = "logs\computer-profiles\$ComputerName.md"
$LogPath = "logs\computer-profiles\$ComputerName-activity.log"

# Initialize computer profile if it doesn't exist
if (-not (Test-Path $ProfilePath)) {
    $ProfileContent = @"
# Development Profile: $ComputerName

## Computer Information
- **Computer Name**: $ComputerName
- **User**: $UserName  
- **OS**: $($SystemInfo.WindowsProductName)
- **RAM**: $([math]::Round($SystemInfo.TotalPhysicalMemory/1GB, 2)) GB
- **Processors**: $($SystemInfo.CsProcessors.Count)
- **First Activity**: $CurrentDate
- **Project Path**: $CurrentPath

## Development Environment
- **PHP Version**: $PHPVersion
- **Composer Version**: $ComposerVersion
- **Git User**: $(git config user.name) <$(git config user.email)>

## Activity Summary
This computer has been used for Enterprise Management Console development.

### Recent Activities
See [$ComputerName-activity.log]($ComputerName-activity.log) for detailed activity log.

---
*Profile created on $CurrentDate*
"@
    
    $ProfileContent | Out-File -FilePath $ProfilePath -Encoding UTF8
    Write-Host "✅ Created computer profile: $ProfilePath" -ForegroundColor Green
}

# Function to log activity
function Log-Activity {
    param($Activity, $Description)
    
    $LogEntry = "[$CurrentDate] [$ComputerName] [$UserName] $Activity"
    if ($Description) {
        $LogEntry += " - $Description"
    }
    
    # Add to computer-specific log
    $LogEntry | Out-File -FilePath $LogPath -Append -Encoding UTF8
    
    # Add to consolidated log
    $ConsolidatedLog = "logs\activity-summary.md"
    if (-not (Test-Path $ConsolidatedLog)) {
        "# Enterprise Management Console - Development Activity Log`n" | Out-File -FilePath $ConsolidatedLog -Encoding UTF8
        "`n## Activity Summary Across All Computers`n" | Out-File -FilePath $ConsolidatedLog -Append -Encoding UTF8
    }
    
    # Update consolidated log
    $LogEntry | Out-File -FilePath $ConsolidatedLog -Append -Encoding UTF8
    
    Write-Host "✅ Logged: $Activity" -ForegroundColor Green
}

# Function to show recent activity
function Show-RecentActivity {
    if (Test-Path $LogPath) {
        Write-Host "`n📋 Recent Activity on $ComputerName" -ForegroundColor Cyan
        Write-Host "═══════════════════════════════════════════════" -ForegroundColor Cyan
        Get-Content $LogPath | Select-Object -Last 10
    } else {
        Write-Host "No activity log found for $ComputerName" -ForegroundColor Yellow
    }
}

# Function to show summary across all computers
function Show-Summary {
    Write-Host "`n📊 Development Summary Across All Computers" -ForegroundColor Cyan
    Write-Host "═══════════════════════════════════════════════" -ForegroundColor Cyan
    
    # List all computer profiles
    $Profiles = Get-ChildItem "logs\computer-profiles\*.md" -ErrorAction SilentlyContinue
    foreach ($Profile in $Profiles) {
        $ComputerName = $Profile.BaseName
        Write-Host "`n🖥️  $ComputerName" -ForegroundColor White
        
        $LogFile = "logs\computer-profiles\$ComputerName-activity.log"
        if (Test-Path $LogFile) {
            $ActivityCount = (Get-Content $LogFile | Measure-Object).Count
            $LastActivity = Get-Content $LogFile | Select-Object -Last 1
            Write-Host "   Activities: $ActivityCount" -ForegroundColor Gray
            Write-Host "   Last: $LastActivity" -ForegroundColor Gray
        }
    }
}

# Main script logic
if ($ShowLog) {
    Show-RecentActivity
    return
}

if ($Summary) {
    Show-Summary
    return
}

# If no activity specified, prompt for it
if (-not $Activity) {
    Write-Host "`n🔧 Enterprise Management Console - Development Logger" -ForegroundColor Cyan
    Write-Host "Computer: $ComputerName | User: $UserName | Time: $CurrentDate`n" -ForegroundColor Gray
    
    Write-Host "Select activity type:" -ForegroundColor White
    Write-Host "1. Feature Development" -ForegroundColor Yellow
    Write-Host "2. Bug Fix" -ForegroundColor Yellow  
    Write-Host "3. Code Refactoring" -ForegroundColor Yellow
    Write-Host "4. Testing" -ForegroundColor Yellow
    Write-Host "5. Deployment/Sync" -ForegroundColor Yellow
    Write-Host "6. Environment Setup" -ForegroundColor Yellow
    Write-Host "7. Documentation" -ForegroundColor Yellow
    Write-Host "8. Other" -ForegroundColor Yellow
    
    $Choice = Read-Host "`nEnter choice (1-8)"
    
    $ActivityTypes = @{
        "1" = "FEATURE"
        "2" = "BUGFIX" 
        "3" = "REFACTOR"
        "4" = "TESTING"
        "5" = "DEPLOYMENT"
        "6" = "SETUP"
        "7" = "DOCUMENTATION"
        "8" = "OTHER"
    }
    
    $Activity = $ActivityTypes[$Choice]
    if (-not $Activity) {
        $Activity = "OTHER"
    }
    
    $Description = Read-Host "Enter description"
}

# Log the activity
Log-Activity -Activity $Activity -Description $Description

# Show recent activity
Show-RecentActivity

Write-Host "`n💡 Usage Examples:" -ForegroundColor Cyan
Write-Host ".\dev-log-tracker.ps1 -Activity 'FEATURE' -Description 'Added Database Management module'" -ForegroundColor Gray
Write-Host ".\dev-log-tracker.ps1 -ShowLog" -ForegroundColor Gray
Write-Host ".\dev-log-tracker.ps1 -Summary" -ForegroundColor Gray