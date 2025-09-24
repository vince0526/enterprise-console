#!/bin/bash

# Enterprise Management Console - Development Activity Logger
# Linux/Mac Bash Version

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
GRAY='\033[0;37m'
NC='\033[0m' # No Color

# Get computer and system information
COMPUTER_NAME=$(hostname)
USER_NAME=$(whoami)
CURRENT_DATE=$(date '+%Y-%m-%d %H:%M:%S')
CURRENT_PATH=$(pwd)

# System information
OS_INFO=$(uname -s)
OS_VERSION=""
if [[ "$OSTYPE" == "darwin"* ]]; then
    OS_VERSION=$(sw_vers -productVersion)
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    if [ -f /etc/os-release ]; then
        OS_VERSION=$(grep PRETTY_NAME /etc/os-release | cut -d'"' -f2)
    fi
fi

# Get RAM info
if [[ "$OSTYPE" == "darwin"* ]]; then
    RAM_GB=$(( $(sysctl hw.memsize | awk '{print $2}') / 1024 / 1024 / 1024 ))
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    RAM_GB=$(grep MemTotal /proc/meminfo | awk '{print int($2/1024/1024)}')
fi

# Get development tools versions
PHP_VERSION=""
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2)
else
    PHP_VERSION="Not installed"
fi

COMPOSER_VERSION=""
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | cut -d' ' -f3)
else
    COMPOSER_VERSION="Not installed"
fi

# Create directories if they don't exist
mkdir -p logs/computer-profiles

# Profile and log paths
PROFILE_PATH="logs/computer-profiles/$COMPUTER_NAME.md"
LOG_PATH="logs/computer-profiles/$COMPUTER_NAME-activity.log"

# Initialize computer profile if it doesn't exist
if [ ! -f "$PROFILE_PATH" ]; then
    cat > "$PROFILE_PATH" << EOF
# Development Profile: $COMPUTER_NAME

## Computer Information
- **Computer Name**: $COMPUTER_NAME
- **User**: $USER_NAME  
- **OS**: $OS_INFO $OS_VERSION
- **RAM**: ${RAM_GB} GB
- **First Activity**: $CURRENT_DATE
- **Project Path**: $CURRENT_PATH

## Development Environment
- **PHP Version**: $PHP_VERSION
- **Composer Version**: $COMPOSER_VERSION
- **Git User**: $(git config user.name) <$(git config user.email)>

## Activity Summary
This computer has been used for Enterprise Management Console development.

### Recent Activities
See [$COMPUTER_NAME-activity.log]($COMPUTER_NAME-activity.log) for detailed activity log.

---
*Profile created on $CURRENT_DATE*
EOF
    echo -e "${GREEN}✅ Created computer profile: $PROFILE_PATH${NC}"
fi

# Function to log activity
log_activity() {
    local activity="$1"
    local description="$2"
    
    local log_entry="[$CURRENT_DATE] [$COMPUTER_NAME] [$USER_NAME] $activity"
    if [ -n "$description" ]; then
        log_entry="$log_entry - $description"
    fi
    
    # Add to computer-specific log
    echo "$log_entry" >> "$LOG_PATH"
    
    # Add to consolidated log
    local consolidated_log="logs/activity-summary.md"
    if [ ! -f "$consolidated_log" ]; then
        echo "# Enterprise Management Console - Development Activity Log" > "$consolidated_log"
        echo "" >> "$consolidated_log"
        echo "## Activity Summary Across All Computers" >> "$consolidated_log"
        echo "" >> "$consolidated_log"
    fi
    
    # Update consolidated log
    echo "$log_entry" >> "$consolidated_log"
    
    echo -e "${GREEN}✅ Logged: $activity${NC}"
}

# Function to show recent activity
show_recent_activity() {
    if [ -f "$LOG_PATH" ]; then
        echo -e "\n${CYAN}📋 Recent Activity on $COMPUTER_NAME${NC}"
        echo -e "${CYAN}═══════════════════════════════════════════════${NC}"
        tail -10 "$LOG_PATH"
    else
        echo -e "${YELLOW}No activity log found for $COMPUTER_NAME${NC}"
    fi
}

# Function to show summary across all computers
show_summary() {
    echo -e "\n${CYAN}📊 Development Summary Across All Computers${NC}"
    echo -e "${CYAN}═══════════════════════════════════════════════${NC}"
    
    # List all computer profiles
    for profile in logs/computer-profiles/*.md; do
        if [ -f "$profile" ]; then
            computer_name=$(basename "$profile" .md)
            echo -e "\n${WHITE}🖥️  $computer_name${NC}"
            
            log_file="logs/computer-profiles/$computer_name-activity.log"
            if [ -f "$log_file" ]; then
                activity_count=$(wc -l < "$log_file")
                last_activity=$(tail -1 "$log_file")
                echo -e "   ${GRAY}Activities: $activity_count${NC}"
                echo -e "   ${GRAY}Last: $last_activity${NC}"
            fi
        fi
    done
}

# Parse command line arguments
ACTIVITY=""
DESCRIPTION=""
SHOW_LOG=false
SUMMARY=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -a|--activity)
            ACTIVITY="$2"
            shift 2
            ;;
        -d|--description)
            DESCRIPTION="$2"
            shift 2
            ;;
        -l|--log)
            SHOW_LOG=true
            shift
            ;;
        -s|--summary)
            SUMMARY=true
            shift
            ;;
        -h|--help)
            echo "Usage: $0 [options]"
            echo "Options:"
            echo "  -a, --activity ACTIVITY      Activity type"
            echo "  -d, --description DESC       Activity description"
            echo "  -l, --log                    Show recent activity log"
            echo "  -s, --summary                Show summary across all computers"
            echo "  -h, --help                   Show this help message"
            exit 0
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Main script logic
if [ "$SHOW_LOG" = true ]; then
    show_recent_activity
    exit 0
fi

if [ "$SUMMARY" = true ]; then
    show_summary
    exit 0
fi

# If no activity specified, prompt for it
if [ -z "$ACTIVITY" ]; then
    echo -e "\n${CYAN}🔧 Enterprise Management Console - Development Logger${NC}"
    echo -e "${GRAY}Computer: $COMPUTER_NAME | User: $USER_NAME | Time: $CURRENT_DATE${NC}\n"
    
    echo -e "${WHITE}Select activity type:${NC}"
    echo -e "${YELLOW}1. Feature Development${NC}"
    echo -e "${YELLOW}2. Bug Fix${NC}"
    echo -e "${YELLOW}3. Code Refactoring${NC}"
    echo -e "${YELLOW}4. Testing${NC}"
    echo -e "${YELLOW}5. Deployment/Sync${NC}"
    echo -e "${YELLOW}6. Environment Setup${NC}"
    echo -e "${YELLOW}7. Documentation${NC}"
    echo -e "${YELLOW}8. Other${NC}"
    
    read -p $'\nEnter choice (1-8): ' choice
    
    case $choice in
        1) ACTIVITY="FEATURE" ;;
        2) ACTIVITY="BUGFIX" ;;
        3) ACTIVITY="REFACTOR" ;;
        4) ACTIVITY="TESTING" ;;
        5) ACTIVITY="DEPLOYMENT" ;;
        6) ACTIVITY="SETUP" ;;
        7) ACTIVITY="DOCUMENTATION" ;;
        8) ACTIVITY="OTHER" ;;
        *) ACTIVITY="OTHER" ;;
    esac
    
    read -p "Enter description: " DESCRIPTION
fi

# Log the activity
log_activity "$ACTIVITY" "$DESCRIPTION"

# Show recent activity
show_recent_activity

echo -e "\n${CYAN}💡 Usage Examples:${NC}"
echo -e "${GRAY}./dev-log-tracker.sh -a FEATURE -d 'Added Database Management module'${NC}"
echo -e "${GRAY}./dev-log-tracker.sh --log${NC}"
echo -e "${GRAY}./dev-log-tracker.sh --summary${NC}"