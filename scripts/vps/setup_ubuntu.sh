#!/usr/bin/env bash
set -euo pipefail

# This script is intentionally "print-first": it echoes the commands you should run.
# Review before executing on your server.

SITE_DIR="${SITE_DIR:-/var/www/firbrigs}"

cat <<EOF
sudo apt update
sudo apt install -y nginx php-fpm php-cli php-mbstring php-xml php-curl unzip composer git

sudo mkdir -p "$SITE_DIR"
sudo chown -R "\$USER:\$USER" "$SITE_DIR"

echo "Next:"
echo "  cd $SITE_DIR"
echo "  git clone <YOUR_GIT_URL> ."
echo "  composer install --no-dev --optimize-autoloader"
echo ""
echo "Then configure Nginx using:"
echo "  scripts/vps/nginx-ip-only.conf"
EOF

