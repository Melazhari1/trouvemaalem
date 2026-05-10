#!/bin/bash

################################################################################
# DEPLOYMENT SCRIPT - trouvemaalem
#
# Automated deployment script for production deployment
#
# Usage:
#   chmod +x deploy.sh
#   ./deploy.sh              (normal deployment)
#   ./deploy.sh --rollback   (rollback to previous version)
#
# Prerequisites:
#   - Git repository configured
#   - SSH access to production server
#   - Bash shell (Linux/Mac)
#   - PHP 8.3+ and Composer installed
#
################################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'  # No Color

# Configuration
PROJECT_DIR="/var/www/trouvemaalem"
BACKUP_DIR="/var/backups/trouvemaalem"
LOG_FILE="/var/log/trouvemaalem_deploy.log"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="backup_${TIMESTAMP}.sql"

################################################################################
# HELPER FUNCTIONS
################################################################################

log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✓ $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}✗ ERROR: $1${NC}" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠ WARNING: $1${NC}" | tee -a "$LOG_FILE"
}

confirm() {
    local prompt="$1"
    local response
    read -p "$(echo -e ${YELLOW}$prompt${NC})" response
    [[ "$response" =~ ^[Yy]$ ]]
}

section() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

################################################################################
# PRE-DEPLOYMENT CHECKS
################################################################################

pre_deployment_checks() {
    section "Pre-Deployment Checks"

    # Check if running as root or with sudo
    if [[ $EUID -ne 0 ]]; then
        error "This script must be run as root (use: sudo ./deploy.sh)"
        exit 1
    fi

    log "Checking project directory..."
    if [[ ! -d "$PROJECT_DIR" ]]; then
        error "Project directory not found: $PROJECT_DIR"
        exit 1
    fi
    success "Project directory found"

    log "Checking git repository..."
    if ! cd "$PROJECT_DIR" && git status > /dev/null 2>&1; then
        error "Not a git repository or git not installed"
        exit 1
    fi
    success "Git repository OK"

    log "Checking PHP installation..."
    if ! command -v php &> /dev/null; then
        error "PHP not installed"
        exit 1
    fi
    PHP_VERSION=$(php -v | head -n 1)
    success "PHP installed: $PHP_VERSION"

    log "Checking Composer installation..."
    if ! command -v composer &> /dev/null; then
        error "Composer not installed"
        exit 1
    fi
    success "Composer installed"

    log "Checking MySQL/MariaDB connection..."
    if ! mysql -u root -p"$DB_PASSWORD" -e "SELECT 1;" > /dev/null 2>&1; then
        error "Cannot connect to MySQL/MariaDB"
        exit 1
    fi
    success "MySQL/MariaDB connection OK"

    log "Checking disk space..."
    DISK_USAGE=$(df -h "$PROJECT_DIR" | tail -1 | awk '{print $5}' | sed 's/%//')
    if [[ $DISK_USAGE -gt 90 ]]; then
        error "Disk usage too high: $DISK_USAGE%"
        exit 1
    fi
    success "Disk space OK: ${DISK_USAGE}%"

    log "Checking if APP_DEBUG is false..."
    if grep -q "APP_DEBUG=true" "$PROJECT_DIR/.env"; then
        error "APP_DEBUG is set to true in .env (security risk)"
        exit 1
    fi
    success "APP_DEBUG is false"

    log "Checking if .env is not in git history..."
    if git log --all --full-history -- .env | grep -q "commit"; then
        warning ".env appears in git history (should not be committed)"
    fi
    success "Pre-deployment checks complete"
}

################################################################################
# BACKUP PROCEDURES
################################################################################

create_database_backup() {
    section "Creating Database Backup"

    log "Backing up database: $DB_DATABASE"
    mkdir -p "$BACKUP_DIR"

    if mysqldump -u root -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_DIR/$BACKUP_NAME"; then
        success "Database backup created: $BACKUP_DIR/$BACKUP_NAME"

        # Verify backup
        BACKUP_SIZE=$(du -h "$BACKUP_DIR/$BACKUP_NAME" | cut -f1)
        log "Backup size: $BACKUP_SIZE"

        # Keep only last 7 backups
        cd "$BACKUP_DIR"
        ls -t backup_*.sql | tail -n +8 | xargs -r rm
        success "Cleanup: Keeping last 7 backups only"
    else
        error "Database backup failed"
        exit 1
    fi
}

################################################################################
# CODE DEPLOYMENT
################################################################################

deploy_code() {
    section "Deploying Code"

    cd "$PROJECT_DIR"

    log "Pulling latest code from git..."
    if git pull origin main; then
        success "Code pulled successfully"
    else
        error "Failed to pull code"
        exit 1
    fi

    log "Installing/updating Composer dependencies..."
    if composer install --no-dev --optimize-autoloader; then
        success "Composer dependencies installed"
    else
        error "Composer installation failed"
        exit 1
    fi

    log "Installing/updating NPM dependencies..."
    if npm install; then
        success "NPM dependencies installed"
    else
        error "NPM installation failed"
        exit 1
    fi

    log "Building assets (production)..."
    if npm run build; then
        success "Assets built successfully"
    else
        error "Asset build failed"
        exit 1
    fi
}

################################################################################
# DATABASE MIGRATIONS
################################################################################

run_migrations() {
    section "Running Database Migrations"

    cd "$PROJECT_DIR"

    log "Checking pending migrations..."
    if php artisan migrate:status | grep -q "Pending"; then
        log "Running migrations..."
        if php artisan migrate --force; then
            success "Migrations completed successfully"
        else
            error "Migrations failed"
            exit 1
        fi
    else
        success "No pending migrations"
    fi
}

################################################################################
# CACHE & OPTIMIZATION
################################################################################

optimize_application() {
    section "Optimizing Application"

    cd "$PROJECT_DIR"

    log "Clearing all caches..."
    php artisan cache:clear
    success "Caches cleared"

    log "Clearing views..."
    php artisan view:clear
    success "Views cleared"

    log "Caching configuration..."
    php artisan config:cache
    success "Configuration cached"

    log "Caching routes..."
    php artisan route:cache
    success "Routes cached"

    log "Optimizing Composer autoloader..."
    composer dumpautoload -o
    success "Autoloader optimized"

    log "Caching views..."
    php artisan view:cache
    success "Views cached"
}

################################################################################
# PERMISSIONS & OWNERSHIP
################################################################################

fix_permissions() {
    section "Fixing File Permissions"

    log "Setting ownership: www-data:www-data..."
    chown -R www-data:www-data "$PROJECT_DIR"
    success "Ownership set"

    log "Setting permissions on storage and bootstrap directories..."
    chmod -R 775 "$PROJECT_DIR/storage"
    chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
    success "Permissions set"

    log "Setting permissions on .env..."
    chmod 600 "$PROJECT_DIR/.env"
    success ".env permissions set (secure)"
}

################################################################################
# SERVICE RESTART
################################################################################

restart_services() {
    section "Restarting Services"

    log "Restarting queue worker..."
    if php artisan queue:restart; then
        success "Queue worker restarted"
    else
        warning "Queue worker may not be running"
    fi

    log "Restarting PHP-FPM..."
    if systemctl restart php8.3-fpm; then
        success "PHP-FPM restarted"
    else
        warning "PHP-FPM restart failed (may require sudo)"
    fi

    log "Restarting Nginx..."
    if systemctl restart nginx; then
        success "Nginx restarted"
    else
        warning "Nginx restart failed (may require sudo)"
    fi
}

################################################################################
# VERIFICATION
################################################################################

verify_deployment() {
    section "Verifying Deployment"

    cd "$PROJECT_DIR"

    log "Checking if application is responding..."
    if curl -s -I http://localhost > /dev/null 2>&1; then
        success "Application is responding"
    else
        error "Application is not responding"
        exit 1
    fi

    log "Checking for Laravel errors in logs..."
    if tail -20 storage/logs/laravel.log | grep -i error; then
        warning "Errors found in logs (review manually)"
    else
        success "No recent errors in logs"
    fi

    log "Checking database connection..."
    if php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
        success "Database connection OK"
    else
        error "Database connection failed"
        exit 1
    fi

    success "Deployment verified successfully"
}

################################################################################
# ROLLBACK
################################################################################

rollback_deployment() {
    section "Rolling Back Deployment"

    if ! confirm "Are you sure you want to rollback? (Y/n) "; then
        log "Rollback cancelled"
        exit 0
    fi

    cd "$PROJECT_DIR"

    log "Reverting to previous git commit..."
    if git revert --no-edit HEAD; then
        success "Code reverted"
    else
        error "Git revert failed"
        exit 1
    fi

    log "Finding latest backup..."
    LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/backup_*.sql | head -1)
    if [[ -z "$LATEST_BACKUP" ]]; then
        error "No backup found to restore"
        exit 1
    fi

    log "Restoring database from: $LATEST_BACKUP"
    if mysql -u root -p"$DB_PASSWORD" "$DB_DATABASE" < "$LATEST_BACKUP"; then
        success "Database restored"
    else
        error "Database restore failed"
        exit 1
    fi

    log "Clearing caches..."
    php artisan cache:clear
    php artisan view:clear
    php artisan config:cache
    php artisan route:cache

    log "Restarting services..."
    systemctl restart php8.3-fpm
    systemctl restart nginx

    success "Rollback completed successfully"
}

################################################################################
# MAIN DEPLOYMENT FLOW
################################################################################

main() {
    # Print header
    clear
    echo -e "${BLUE}"
    echo "╔════════════════════════════════════════════════════════╗"
    echo "║          TROUVEMAALEM DEPLOYMENT SCRIPT               ║"
    echo "║                 Laravel 13 Deployment                 ║"
    echo "╚════════════════════════════════════════════════════════╝"
    echo -e "${NC}\n"

    # Check for rollback flag
    if [[ "$1" == "--rollback" ]]; then
        log "ROLLBACK MODE ACTIVATED"
        rollback_deployment
        exit 0
    fi

    # Main deployment flow
    log "Starting deployment process..."
    log "Project directory: $PROJECT_DIR"
    log "Timestamp: $TIMESTAMP"

    # Prompt for confirmation
    echo -e "\n${YELLOW}Deployment Summary:${NC}"
    echo "  • Backup database"
    echo "  • Pull latest code"
    echo "  • Install dependencies"
    echo "  • Build assets"
    echo "  • Run migrations"
    echo "  • Optimize caches"
    echo "  • Fix permissions"
    echo "  • Restart services"
    echo "  • Verify deployment"
    echo ""

    if ! confirm "Continue with deployment? (Y/n) "; then
        log "Deployment cancelled"
        exit 0
    fi

    # Execute deployment steps
    pre_deployment_checks
    create_database_backup
    deploy_code
    run_migrations
    optimize_application
    fix_permissions
    restart_services
    verify_deployment

    # Success
    section "Deployment Complete! 🎉"
    success "Deployment finished successfully at $(date)"
    success "Backup saved: $BACKUP_DIR/$BACKUP_NAME"
    success "Logs saved: $LOG_FILE"
    success ""
    success "Next steps:"
    success "  1. Test website: https://yourdomain.com"
    success "  2. Check admin panel: https://yourdomain.com/admin"
    success "  3. Monitor logs: tail -f $PROJECT_DIR/storage/logs/laravel.log"
    success "  4. Review error monitoring (Sentry, etc.)"
}

# Error handler
trap 'error "Deployment failed. Check logs: $LOG_FILE"' EXIT

# Run main function
main "$@"
