param($Activity="OTHER", $Description="Development activity")

$ComputerName = $env:COMPUTERNAME
$UserName = $env:USERNAME  
$Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

# Create directory if needed
$LogDir = "logs\computer-profiles"
if (!(Test-Path $LogDir)) { New-Item -ItemType Directory -Path $LogDir -Force }

# Create log entry
$LogEntry = "[$Timestamp] [$ComputerName] [$UserName] $Activity - $Description"

# Log to file
$LogFile = "$LogDir\$ComputerName-activity.log" 
$LogEntry | Out-File -FilePath $LogFile -Append -Encoding UTF8

Write-Host "✅ Logged: $Activity on $ComputerName"
Write-Host "Entry: $LogEntry"