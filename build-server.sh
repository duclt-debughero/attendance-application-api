#!/bin/bash
# Ensure script uses LF line endings

# Define color codes
RED="\033[0;31m"
GREEN="\033[0;32m"
YELLOW="\033[0;33m"
BLUE="\033[0;34m"
CYAN="\033[0;36m"
RESET="\033[0m"

# Function to print messages with color
print_message() {
  local color=$1
  local message=$2
  echo -e "${color}${message}${RESET}"
}

# Function to update all core system packages
update_core_system() {
  print_message "$CYAN" "Updating core system packages..."
  sudo dnf -y --releasever=latest update --exclude=docker-ce,docker-ce-cli
  print_message "$GREEN" "Core system packages updated successfully."
}

# Function to install required packages
install_package() {
  local packages=$1

  for package_name in $packages; do
    if ! command -v "$package_name" &>/dev/null; then
      print_message "$CYAN" "Installing $package_name..."
      sudo dnf install -y "$package_name"

      if command -v "$package_name" &>/dev/null; then
        print_message "$GREEN" "$package_name installation completed successfully."
      else
        print_message "$RED" "$package_name installation failed."
      fi
    else
      print_message "$GREEN" "$package_name is already installed."
    fi
  done
}

# Function to install Docker
install_docker() {
  local required_version="27.4"
  local latest_version
  local current_version

  # Set up the Docker repository
  sudo dnf install -y dnf-plugins-core
  sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

  # Modify repository to use CentOS 9
  sudo sed -i 's|baseurl=https://download.docker.com/linux/centos/\$releasever/\$basearch/stable|baseurl=https://download.docker.com/linux/centos/9/\$basearch/stable|' /etc/yum.repos.d/docker-ce.repo

  # Get the latest available 27.4.x version
  latest_version=$(sudo dnf list --showduplicates docker-ce | awk '{print $2}' | grep -E "^3:${required_version}\.[0-9]+-1\.el9$" | sort -V | tail -n 1 | sed 's/^3://; s/-1.el9$//')
  print_message "$CYAN" "Installing the latest Docker $latest_version version..."

  # Check docker current version
  if command -v docker &>/dev/null; then
    current_version=$(docker --version 2>/dev/null | grep -oP "\d+\.\d+\.\d+")
  else
    current_version=""
  fi

  if [[ "$current_version" == "$latest_version" ]]; then
    print_message "$GREEN" "Docker version $current_version is already up-to-date."
    return
  fi

  # Remove any existing Docker installation
  sudo dnf remove -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin docker-ce-rootless-extras
  sudo rm -rf /var/lib/docker /var/lib/containerd /etc/docker

  # Install Docker
  sudo dnf install -y docker-ce-3:$latest_version-1.el9 docker-ce-cli-1:$latest_version-1.el9 containerd.io docker-buildx-plugin docker-compose-plugin

  # Start Docker service and enable it to start on boot
  sudo systemctl start docker
  sudo systemctl enable --now docker

  # Add the current user to the Docker group
  sudo usermod -aG docker ec2-user

  # Check Docker installation if successful
  if docker --version >/dev/null 2>&1; then
    print_message "$GREEN" "Docker installation completed successfully."
    print_message "$GREEN" "$(docker --version)"
  else
    print_message "$RED" "Docker installation failed."
  fi
}

# Function to install Docker Compose
install_docker_compose() {
  local required_version="2.32"
  local latest_version

  # Get the latest version available in the required_version.x series
  latest_version=$(curl -s https://api.github.com/repos/docker/compose/releases | grep -oP "\"tag_name\":\s*\"v${required_version}\.\d+\"" | grep -oP "${required_version}\.\d+" | sort -V | tail -n 1)
  print_message "$CYAN" "Installing the latest Docker Compose $latest_version version..."

  # Remove any existing Docker Compose installation
  sudo rm /usr/local/bin/docker-compose

  # Install Docker Compose
  sudo curl -L "https://github.com/docker/compose/releases/download/v$latest_version/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

  # Make Docker Compose executable
  sudo chmod +x /usr/local/bin/docker-compose

  # Check Docker Compose installation if successful
  if docker-compose --version >/dev/null 2>&1; then
    print_message "$GREEN" "Docker Compose installation completed successfully."
    print_message "$GREEN" "$(docker-compose --version)"
  else
    print_message "$RED" "Docker Compose installation failed."
  fi
}

# Function to clone or update a Git repository
setup_repository() {
  local source_url="https://github.com/duclt-debughero/attendance-application-api.git"

  print_message "$CYAN" "Setting up repository..."

  # Get the path where you want to clone the repository
  read -r -p "Enter the path where you want to clone the repository (default: /home/ec2-user): " path_source
  path_source=${path_source:-/home/ec2-user}

  # Create the source directory if it doesn't exist
  mkdir -p "$path_source"
  cd "$path_source" || exit

  # Get folder and branch names with defaults
  read -r -p "Enter folder name (default: site_develop): " folder_name
  folder_name=${folder_name:-site_develop}

  read -r -p "Git branch name (default: develop): " branch_name
  branch_name=${branch_name:-develop}

  # Remember git credentials for 1 day
  git config --global credential.helper "cache --timeout=86400"

  # Clone or update the repository
  if [ -d "$folder_name" ]; then
    print_message "$YELLOW" "Folder '$folder_name' exists. Pulling updates..."
    cd "$folder_name" || exit
    git fetch
    git checkout "$branch_name"
    git pull
  else
    print_message "$YELLOW" "Cloning repository into '$folder_name'..."
    git clone "$source_url" "$folder_name"
    cd "$folder_name" || exit
    git checkout "$branch_name"
  fi

  print_message "$GREEN" "Repository setup completed successfully."

  # Run docker-start.sh
  run_docker_start "$path_source/$folder_name"
}

# Function to run docker-start.sh
run_docker_start() {
  local folder_path="$1"

  print_message "$CYAN" "Running file docker-start.sh..."

  # Change directory
  cd "$folder_path" || exit

  # Make docker-start.sh executable
  print_message "$CYAN" "Making docker-start.sh executable..."
  chmod +x ./docker-start.sh

  print_message "$CYAN" "Running docker-start.sh..."

  # Run shell script with group docker permissions
  sg docker -c "sh '$folder_path/docker-start.sh'"

  print_message "$GREEN" "docker-start.sh completed successfully."
}

# Main script execution
main() {
  update_core_system
  install_package "vim git"
  install_docker
  install_docker_compose
  setup_repository
}

main
