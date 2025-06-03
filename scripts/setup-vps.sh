#!/bin/bash
# setup-vps.sh - Initial VPS setup script for Laravel deployment

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Setting up VPS for Laravel deployment...${NC}"

# Update system packages
echo -e "${YELLOW}📦 Updating system packages...${NC}"
sudo apt update && sudo apt upgrade -y

# Install required packages
echo -e "${YELLOW}📚 Installing required packages...${NC}"
sudo apt install -y \
    nginx \
    mysql-server \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-xml \
    php8.2-curl \
    php8.2-zip \
    php8.2-mbstring \
    php8.2-bcmath \
    php8.2-tokenizer \
    php8.2-json \
    php8.2-pdo \
    php8.2-fileinfo \
    php8.2-openssl \
    php8.2-gd \
    unzip \
    curl \
    git \
    supervisor

# Install Composer
echo -e "${YELLOW}🎼 Installing Composer...${NC}"
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
HASH=`curl -sS https://composer.github.io/installer.sig`
php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# Configure MySQL
echo -e "${YELLOW}🗄️  Configuring MySQL...${NC}"
echo "Please run mysql_secure_installation manually after this script completes"
echo "Then create your database and user:"
echo ""
echo "sudo mysql -u root -p"
echo "CREATE DATABASE your_database_name;"
echo "CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'your_secure_password';"
echo "GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_db_user'@'localhost';"
echo "FLUSH PRIVILEGES;"
echo "EXIT;"
echo ""

# Create application directory
echo -e "${YELLOW}📁 Creating application directory...${NC}"
sudo mkdir -p /var/www/your-app
sudo chown -R www-data:www-data /var/www/your-app
sudo chmod -R 755 /var/www/your-app

# Configure Nginx
echo -e "${YELLOW}🌐 Configuring Nginx...${NC}"
sudo tee /etc/nginx/sites-available/your-app > /dev/null <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/your-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable the site
sudo ln -sf /etc/nginx/sites-available/your-app /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Configure PHP-FPM
echo -e "${YELLOW}🐘 Configuring PHP-FPM...${NC}"
sudo sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' /etc/php/8.2/fpm/php.ini

# Configure Supervisor for queue workers
echo -e "${YELLOW}👷 Configuring Supervisor...${NC}"
sudo tee /etc/supervisor/conf.d/laravel-worker.conf > /dev/null <<EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/your-app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/your-app/storage/logs/worker.log
stopwaitsecs=3600
EOF

# Create logrotate configuration
echo -e "${YELLOW}📝 Configuring log rotation...${NC}"
sudo tee /etc/logrotate.d/laravel > /dev/null <<EOF
/var/www/your-app/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        sudo systemctl reload nginx
        sudo systemctl reload php8.2-fpm
    endscript
}
EOF

# Configure firewall
echo -e "${YELLOW}🔥 Configuring firewall...${NC}"
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Start and enable services
echo -e "${YELLOW}🔄 Starting services...${NC}"
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
sudo systemctl start mysql
sudo systemctl enable mysql
sudo systemctl start supervisor
sudo systemctl enable supervisor

# Set up SSL with Let's Encrypt (commented out - run manually)
echo -e "${YELLOW}🔒 SSL Setup (run manually after DNS is configured):${NC}"
echo "sudo apt install certbot python3-certbot-nginx"
echo "sudo certbot --nginx -d your-domain.com -d www.your-domain.com"

# Create deployment user (optional)
echo -e "${YELLOW}👤 Creating deployment user...${NC}"
read -p "Create a deployment user? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "Enter username: " DEPLOY_USER
    sudo adduser $DEPLOY_USER
    sudo usermod -aG www-data $DEPLOY_USER
    sudo usermod -aG sudo $DEPLOY_USER

    # Set up SSH key for deployment user
    echo "Set up SSH key for $DEPLOY_USER:"
    echo "sudo -u $DEPLOY_USER mkdir -p /home/$DEPLOY_USER/.ssh"
    echo "sudo -u $DEPLOY_USER nano /home/$DEPLOY_USER/.ssh/authorized_keys"
    echo "sudo chmod 700 /home/$DEPLOY_USER/.ssh"
    echo "sudo chmod 600 /home/$DEPLOY_USER/.ssh/authorized_keys"
fi

echo -e "${GREEN}✅ VPS setup completed!${NC}"
echo ""
echo -e "${BLUE}📋 Next steps:${NC}"
echo "1. Configure your domain DNS to point to this server"
echo "2. Run mysql_secure_installation"
echo "3. Create your database and user"
echo "4. Update /etc/nginx/sites-available/your-app with your actual domain"
echo "5. Set up SSL with certbot"
echo "6. Add your SSH key to GitHub secrets"
echo "7. Configure GitHub secrets for deployment"
echo ""
echo -e "${BLUE}🔐 Required GitHub Secrets:${NC}"
echo "SSH_PRIVATE_KEY: Your private SSH key"
echo "VPS_HOST: Your server IP address"
echo "VPS_USER: SSH username (e.g., ubuntu or your deployment user)"
echo "APP_DIRECTORY: /var/www/your-app"
echo "REPO_URL: Your GitHub repository URL"
echo "DEPLOY_BRANCH: main (or your preferred branch)"
