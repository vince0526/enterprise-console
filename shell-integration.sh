#!/bin/bash

# Enterprise Management Console - Shell Integration Configuration
# Bash/Zsh Profile Enhancement for Improved Command Detection

# =============================================================================
# SHELL INTEGRATION CONFIGURATION
# =============================================================================

# Colors for output
export EMC_GREEN='\033[0;32m'
export EMC_BLUE='\033[0;34m'
export EMC_CYAN='\033[0;36m'
export EMC_YELLOW='\033[1;33m'
export EMC_RED='\033[0;31m'
export EMC_GRAY='\033[0;37m'
export EMC_NC='\033[0m' # No Color

# Function to detect if we're in EMC project directory
is_emc_project() {
    [[ -f "composer.json" && -f "artisan" ]] && grep -q "enterprise.*console" composer.json 2>/dev/null
}

# Enhanced command detection with better error handling
emc_command() {
    local cmd="$1"
    local description="$2"
    local log_activity="${3:-false}"
    
    if is_emc_project; then
        echo -e "${EMC_CYAN}🚀 EMC: $cmd${EMC_NC}"
        
        if [[ "$log_activity" == "true" && -n "$description" ]]; then
            if [[ -x "./dev-log-tracker.sh" ]]; then
                ./dev-log-tracker.sh -a "COMMAND" -d "Executed: $cmd - $description"
            fi
        fi
        
        if eval "$cmd"; then
            return 0
        else
            echo -e "${EMC_RED}❌ Command failed: $cmd${EMC_NC}"
            if [[ "$log_activity" == "true" ]]; then
                ./dev-log-tracker.sh -a "ERROR" -d "Command failed: $cmd" 2>/dev/null
            fi
            return 1
        fi
    else
        echo -e "${EMC_YELLOW}⚠️ Not in EMC project directory${EMC_NC}"
        eval "$cmd"
    fi
}

# =============================================================================
# ENHANCED ALIASES FOR BETTER COMMAND DETECTION
# =============================================================================

# Laravel/EMC specific aliases
alias emc-serve='emc_command "php artisan serve" "Start EMC development server" true'
alias emc-migrate='emc_command "php artisan migrate" "Run database migrations" true'
alias emc-fresh='emc_command "php artisan migrate:fresh --seed" "Fresh migration with seeders" true'
alias emc-test='emc_command "php artisan test" "Run EMC tests" true'
alias emc-pint='emc_command "vendor/bin/pint" "Run Laravel Pint formatter" true'
alias emc-stan='emc_command "vendor/bin/phpstan analyse" "Run PHPStan analysis" true'
alias emc-queue='emc_command "php artisan queue:work" "Start queue worker" true'

# Development workflow functions
emc-setup() {
    echo -e "${EMC_GREEN}🔧 Setting up EMC development environment...${EMC_NC}"
    emc_command "composer install" "Install dependencies" true &&
    emc_command "php artisan key:generate" "Generate app key" true &&
    emc_command "php artisan migrate" "Setup database" true
    echo -e "${EMC_GREEN}✅ EMC setup complete!${EMC_NC}"
}

emc-quality() {
    echo -e "${EMC_GREEN}🎯 Running EMC quality checks...${EMC_NC}"
    emc_command "vendor/bin/pint" "Code formatting" true &&
    emc_command "vendor/bin/phpstan analyse" "Static analysis" true &&
    emc_command "php artisan test" "Run tests" true
    echo -e "${EMC_GREEN}✅ Quality checks complete!${EMC_NC}"
}

emc-deploy() {
    echo -e "${EMC_GREEN}🚀 Deploying EMC changes...${EMC_NC}"
    emc_command "git add ." "Stage changes" true
    echo -n "Enter commit message: "
    read commit_msg
    emc_command "git commit -m \"$commit_msg\"" "Commit changes" true &&
    emc_command "git push origin main" "Push to GitHub" true
    echo -e "${EMC_GREEN}✅ Deployment complete!${EMC_NC}"
}

# Git aliases with EMC integration
alias emc-status='emc_command "git status" "Check git status"'
alias emc-log='emc_command "git log --oneline -10" "View recent commits"'
alias emc-pull='emc_command "git pull origin main" "Pull latest changes" true'

# Development logging functions
emc-activity() {
    local activity="$1"
    local description="$2"
    
    if [[ -n "$activity" && -n "$description" ]]; then
        if [[ -x "./dev-log-tracker.sh" ]]; then
            ./dev-log-tracker.sh -a "$activity" -d "$description"
        else
            echo -e "${EMC_RED}dev-log-tracker.sh not found or not executable${EMC_NC}"
        fi
    else
        echo -e "${EMC_YELLOW}Usage: emc-activity ACTIVITY 'Description'${EMC_NC}"
        echo -e "${EMC_GRAY}Activities: FEATURE, BUGFIX, REFACTOR, TESTING, DEPLOYMENT, SETUP, DOCUMENTATION${EMC_NC}"
    fi
}

emc-log-view() {
    local log_file="logs/computer-profiles/$(hostname)-activity.log"
    if [[ -f "$log_file" ]]; then
        echo -e "${EMC_CYAN}📋 Recent EMC Activity on $(hostname)${EMC_NC}"
        tail -10 "$log_file"
    else
        echo -e "${EMC_YELLOW}No activity log found${EMC_NC}"
    fi
}

# =============================================================================
# ENHANCED PROMPT FOR EMC PROJECTS
# =============================================================================

# Function to get git branch
get_git_branch() {
    git rev-parse --abbrev-ref HEAD 2>/dev/null
}

# Enhanced prompt function
emc_prompt() {
    local last_exit_code=$?
    local prompt_symbol="$ "
    
    # Color prompt symbol based on last command success
    if [[ $last_exit_code -eq 0 ]]; then
        prompt_symbol="${EMC_GREEN}$ ${EMC_NC}"
    else
        prompt_symbol="${EMC_RED}$ ${EMC_NC}"
    fi
    
    # Check if in EMC project
    if is_emc_project; then
        local git_branch=$(get_git_branch)
        local emc_indicator="${EMC_GREEN}[EMC]${EMC_NC}"
        
        if [[ -n "$git_branch" ]]; then
            emc_indicator="${EMC_GREEN}[EMC:$git_branch]${EMC_NC}"
        fi
        
        PS1="$emc_indicator ${EMC_BLUE}$(basename "$PWD")${EMC_NC} $prompt_symbol"
    else
        PS1="${EMC_BLUE}$(basename "$PWD")${EMC_NC} $prompt_symbol"
    fi
}

# =============================================================================
# SHELL INTEGRATION INITIALIZATION
# =============================================================================

# Set up the prompt
if [[ "$SHELL" == *"bash"* ]]; then
    PROMPT_COMMAND="emc_prompt"
elif [[ "$SHELL" == *"zsh"* ]]; then
    setopt PROMPT_SUBST
    precmd() { emc_prompt; }
fi

# Enable better history settings
export HISTSIZE=10000
export HISTFILESIZE=20000
export HISTCONTROL=ignoreboth:erasedups
shopt -s histappend 2>/dev/null || true # bash only

# Improved command completion
if [[ -f /etc/bash_completion ]] && [[ "$SHELL" == *"bash"* ]]; then
    source /etc/bash_completion
fi

# EMC activity completion
_emc_activity_completion() {
    local cur prev activities
    COMPREPLY=()
    cur="${COMP_WORDS[COMP_CWORD]}"
    prev="${COMP_WORDS[COMP_CWORD-1]}"
    activities="FEATURE BUGFIX REFACTOR TESTING DEPLOYMENT SETUP DOCUMENTATION OTHER"
    
    if [[ ${prev} == "emc-activity" ]]; then
        COMPREPLY=( $(compgen -W "${activities}" -- ${cur}) )
        return 0
    fi
}

complete -F _emc_activity_completion emc-activity 2>/dev/null || true

echo -e "${EMC_GREEN}🔧 EMC Shell Integration Loaded!${EMC_NC}"
echo -e "${EMC_CYAN}Available commands:${EMC_NC}"
echo -e "${EMC_GRAY}  emc-serve, emc-test, emc-pint, emc-stan${EMC_NC}"
echo -e "${EMC_GRAY}  emc-setup, emc-quality, emc-deploy${EMC_NC}"
echo -e "${EMC_GRAY}  emc-activity, emc-log-view${EMC_NC}"
echo -e "${EMC_GRAY}  emc-status, emc-log, emc-pull${EMC_NC}"

# Auto-detect EMC project on startup
if is_emc_project; then
    echo -e "${EMC_GREEN}✅ Enterprise Management Console detected!${EMC_NC}"
    echo -e "${EMC_GRAY}🏠 Project: $(basename "$PWD")${EMC_NC}"
    
    # Show recent activity
    local log_file="logs/computer-profiles/$(hostname)-activity.log"
    if [[ -f "$log_file" ]]; then
        local last_activity=$(tail -1 "$log_file" | cut -c23-)
        echo -e "${EMC_GRAY}📋 Last activity: $last_activity${EMC_NC}"
    fi
fi