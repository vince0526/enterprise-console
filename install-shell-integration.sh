#!/bin/bash

# Enterprise Management Console - Shell Integration Installer
# Automatically configures shell integration for improved command detection

set -e

EMC_GREEN='\033[0;32m'
EMC_BLUE='\033[0;34m'
EMC_CYAN='\033[0;36m'
EMC_YELLOW='\033[1;33m'
EMC_RED='\033[0;31m'
EMC_NC='\033[0m'

echo -e "${EMC_CYAN}🔧 EMC Shell Integration Installer${EMC_NC}"
echo -e "${EMC_GRAY}Setting up enhanced command detection and shell integration...${EMC_NC}"

# Detect current shell
CURRENT_SHELL=$(basename "$SHELL")
echo -e "${EMC_BLUE}Detected shell: $CURRENT_SHELL${EMC_NC}"

# Get EMC project directory
EMC_DIR="$(pwd)"
INTEGRATION_SCRIPT="$EMC_DIR/shell-integration.sh"

# Check if we're in EMC project
if [[ ! -f "composer.json" || ! -f "artisan" ]]; then
    echo -e "${EMC_RED}❌ This doesn't appear to be an EMC project directory${EMC_NC}"
    echo "Please run this script from the enterprise-console directory"
    exit 1
fi

# Make shell integration script executable
chmod +x "$INTEGRATION_SCRIPT"
chmod +x "$EMC_DIR/dev-log-tracker.sh" 2>/dev/null || true

# Function to add source line to profile
add_to_profile() {
    local profile_file="$1"
    local source_line="source \"$INTEGRATION_SCRIPT\""
    
    # Create profile file if it doesn't exist
    touch "$profile_file"
    
    # Check if already added
    if grep -q "$INTEGRATION_SCRIPT" "$profile_file"; then
        echo -e "${EMC_YELLOW}⚠️ Integration already added to $profile_file${EMC_NC}"
        return 0
    fi
    
    # Add integration
    echo "" >> "$profile_file"
    echo "# EMC Shell Integration" >> "$profile_file"
    echo "$source_line" >> "$profile_file"
    echo -e "${EMC_GREEN}✅ Added integration to $profile_file${EMC_NC}"
}

# Configure based on shell
case "$CURRENT_SHELL" in
    "bash")
        echo -e "${EMC_BLUE}Configuring Bash integration...${EMC_NC}"
        
        # Try common bash profile locations
        if [[ -f "$HOME/.bashrc" ]]; then
            add_to_profile "$HOME/.bashrc"
        elif [[ -f "$HOME/.bash_profile" ]]; then
            add_to_profile "$HOME/.bash_profile"
        else
            # Create .bashrc if neither exists
            add_to_profile "$HOME/.bashrc"
        fi
        ;;
        
    "zsh")
        echo -e "${EMC_BLUE}Configuring Zsh integration...${EMC_NC}"
        add_to_profile "$HOME/.zshrc"
        ;;
        
    "fish")
        echo -e "${EMC_BLUE}Configuring Fish integration...${EMC_NC}"
        FISH_CONFIG_DIR="$HOME/.config/fish"
        mkdir -p "$FISH_CONFIG_DIR"
        
        # Fish uses different syntax
        FISH_CONFIG="$FISH_CONFIG_DIR/config.fish"
        if ! grep -q "$INTEGRATION_SCRIPT" "$FISH_CONFIG" 2>/dev/null; then
            echo "" >> "$FISH_CONFIG"
            echo "# EMC Shell Integration" >> "$FISH_CONFIG"
            echo "source \"$INTEGRATION_SCRIPT\"" >> "$FISH_CONFIG"
            echo -e "${EMC_GREEN}✅ Added integration to $FISH_CONFIG${EMC_NC}"
        fi
        ;;
        
    *)
        echo -e "${EMC_YELLOW}⚠️ Unsupported shell: $CURRENT_SHELL${EMC_NC}"
        echo "Please manually add the following line to your shell profile:"
        echo "source \"$INTEGRATION_SCRIPT\""
        ;;
esac

# Test the integration
echo -e "${EMC_BLUE}Testing integration...${EMC_NC}"
if source "$INTEGRATION_SCRIPT"; then
    echo -e "${EMC_GREEN}✅ Shell integration loaded successfully!${EMC_NC}"
else
    echo -e "${EMC_RED}❌ Error loading shell integration${EMC_NC}"
    exit 1
fi

echo ""
echo -e "${EMC_GREEN}🎉 Shell Integration Installation Complete!${EMC_NC}"
echo ""
echo -e "${EMC_CYAN}What's been configured:${EMC_NC}"
echo -e "${EMC_GRAY}  ✅ Enhanced command detection${EMC_NC}"
echo -e "${EMC_GRAY}  ✅ EMC-specific aliases (emc-serve, emc-test, etc.)${EMC_NC}"
echo -e "${EMC_GRAY}  ✅ Automatic activity logging${EMC_NC}"
echo -e "${EMC_GRAY}  ✅ Enhanced prompt with EMC/Git status${EMC_NC}"
echo -e "${EMC_GRAY}  ✅ Command completion improvements${EMC_NC}"
echo ""
echo -e "${EMC_YELLOW}To activate immediately, run:${EMC_NC}"
echo -e "${EMC_BLUE}  source $INTEGRATION_SCRIPT${EMC_NC}"
echo ""
echo -e "${EMC_YELLOW}Or restart your terminal to load automatically.${EMC_NC}"
echo ""
echo -e "${EMC_CYAN}Available EMC commands:${EMC_NC}"
echo -e "${EMC_GRAY}  emc-serve     - Start development server${EMC_NC}"
echo -e "${EMC_GRAY}  emc-test      - Run tests${EMC_NC}"
echo -e "${EMC_GRAY}  emc-quality   - Run code quality checks${EMC_NC}"
echo -e "${EMC_GRAY}  emc-deploy    - Deploy changes to GitHub${EMC_NC}"
echo -e "${EMC_GRAY}  emc-activity  - Log development activity${EMC_NC}"