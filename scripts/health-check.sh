#!/bin/bash
# health-check.sh - Post-deployment health check script

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

APP_DIR="/var/www/your-app"
DOMAIN="your-domain.com"

echo -e "${BLUE}🏥 Running post-deployment health checks...${NC}"

# Check if application directory exists
echo -e "${YELLOW}📁 Checking application directory...${NC}"
if [ -d "$APP_DIR" ]; then
    echo -e "${GREEN}✅ Application directory exists${NC}"
else
    echo -e "${RED}❌ Application directory not found${NC}"
    exit 1
fi

# Check file permissions
echo -e "${YELLOW}🔐 Checking file permissions...${NC}"
STORAGE_PERMS=$(stat -c "%a" "$APP_DIR/storage")
CACHE_PERMS=$(stat -c "%a" "$APP_DIR/bootstrap/cache")

if [ "$STORAGE_PERMS" = "775" ] && [ "$CACHE_PERMS" = "775" ]; then
    echo -e "${GREEN}✅ File permissions are correct${NC}"
else
    echo -e "${RED}❌ File permissions need adjustment${NC}"
    echo "Storage permissions: $STORAGE_PERMS (should be 775)"
    echo "Cache permissions: $CACHE_PERMS (should be 775)"
fi

# Check .env file
echo -e "${YELLOW}📝 Checking .env file...${NC}"
if [ -f "$APP_DIR/.env" ]; then
    echo -e "${GREEN}✅ .env file exists${NC}"

    # Check if APP_KEY is set
    if grep -q "APP_KEY=base64:" "$APP_DIR/.env"; then
        echo -e "${GREEN}✅ APP_KEY is set${NC}"
    else
        echo -e "${RED}❌ APP_KEY is not set${NC}"
    fi

    # Check if database credentials are set
    if grep -q "DB_DATABASE=" "$APP_DIR/.env" && ! grep -q "DB_DATABASE=$" "$APP_DIR/.env"; then
        echo -e "${GREEN}✅ Database credentials are configured${NC}"
    else
        echo -e "${RED}❌ Database credentials are not configured${NC}"
    fi
else
    echo -e "${RED}❌ .env file not found${NC}"
    exit 1
fi

# Check Composer dependencies
echo -e "${YELLOW}📚 Checking Composer dependencies...${NC}"
if [ -d "$APP_DIR/vendor" ]; then
    echo -e "${GREEN}✅ Composer dependencies installed${NC}"
else
    echo -e "${RED}❌ Composer dependencies not installed${NC}"
    exit 1
fi

# Check storage link
echo -e "${YELLOW}🔗 Checking storage link...${NC}"
if [ -L "$APP_DIR/public/storage" ]; then
    echo -e "${GREEN}✅ Storage link exists${NC}"
else
    echo -e "${YELLOW}⚠️  Storage link not found${NC}"
fi

# Check services
echo -e "${YELLOW}🔄 Checking services...${NC}"

# Nginx
if systemctl is-active --quiet nginx; then
    echo -e "${GREEN}✅ Nginx is running${NC}"
else
    echo -e "${RED}❌ Nginx is not running${NC}"
fi

# PHP-FPM
if systemctl is-active --quiet php8.2-fpm; then
    echo -e "${GREEN}✅ PHP-FPM is running${NC}"
else
    echo -e "${RED}❌ PHP-FPM is not running${NC}"
fi

# MySQL
if systemctl is-active --quiet mysql; then
    echo -e "${GREEN}✅ MySQL is running${NC}"
else
    echo -e "${RED}❌ MySQL is not running${NC}"
fi

# Test database connection
echo -e "${YELLOW}🗄️  Testing database connection...${NC}"
cd $APP_DIR
if sudo -u www-data php artisan migrate:status > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connection successful${NC}"
else
    echo -e "${RED}❌ Database connection failed${NC}"
fi

# Test web server response
echo -e "${YELLOW}🌐 Testing web server response...${NC}"
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost" || echo "000")
if [ "$HTTP_STATUS" = "200" ]; then
    echo -e "${GREEN}✅ Web server responding (HTTP $HTTP_STATUS)${NC}"
elif [ "$HTTP_STATUS" = "000" ]; then
    echo -e "${RED}❌ Web server not accessible${NC}"
else
    echo -e "${YELLOW}⚠️  Web server responding with HTTP $HTTP_STATUS${NC}"
fi

# Test API endpoints
echo -e "${YELLOW}🚀 Testing API endpoints...${NC}"
API_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost/api/v1/health" || echo "000")
if [ "$API_STATUS" = "200" ]; then
    echo -e "${GREEN}✅ API health check passed (HTTP $API_STATUS)${NC}"
else
    echo -e "${YELLOW}⚠️  API health check returned HTTP $API_STATUS${NC}"
fi

# Check log files
echo -e "${YELLOW}📋 Checking log files...${NC}"
if [ -f "$APP_DIR/storage/logs/laravel.log" ]; then
    RECENT_ERRORS=$(tail -n 100 "$APP_DIR/storage/logs/laravel.log" | grep -i "error\|exception\|fatal" | wc -l)
    if [ "$RECENT_ERRORS" -gt 0 ]; then
        echo -e "${YELLOW}⚠️  Found $RECENT_ERRORS recent errors in logs${NC}"
        echo "Recent errors:"
        tail -n 20 "$APP_DIR/storage/logs/laravel.log" | grep -i "error\|exception\|fatal" | tail -n 5
    else
        echo -e "${GREEN}✅ No recent errors in logs${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  No log file found${NC}"
fi

# Check disk space
echo -e "${YELLOW}💾 Checking disk space...${NC}"
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -lt 80 ]; then
    echo -e "${GREEN}✅ Disk usage: ${DISK_USAGE}%${NC}"
elif [ "$DISK_USAGE" -lt 90 ]; then
    echo -e "${YELLOW}⚠️  Disk usage: ${DISK_USAGE}% (Warning)${NC}"
else
    echo -e "${RED}❌ Disk usage: ${DISK_USAGE}% (Critical)${NC}"
fi

# Check memory usage
echo -e "${YELLOW}🧠 Checking memory usage...${NC}"
MEMORY_USAGE=$(free | awk 'NR==2{printf "%.2f", $3*100/$2}')
echo -e "${GREEN}✅ Memory usage: ${MEMORY_USAGE}%${NC}"

# Summary
echo ""
echo -e "${BLUE}📊 Health Check Summary${NC}"
echo -e "🕒 Completed at: $(date)"
echo -e "🏠 Application: $APP_DIR"
echo -e "🌐 Domain: $DOMAIN"
echo -e "💾 Disk usage: ${DISK_USAGE}%"
echo -e "🧠 Memory usage: ${MEMORY_USAGE}%"

echo ""
echo -e "${GREEN}🎉 Health check completed!${NC}"
