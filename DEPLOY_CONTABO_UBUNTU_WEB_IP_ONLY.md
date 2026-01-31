## Contabo Ubuntu (Nginx + PHP-FPM) — Host site on IP (Git deploy)

This repo is a static site + a PHP contact form endpoint (`sendemail.php`).
This guide hosts it over **http://<VPS_IP>/** (no domain required).

### 0) Security first (recommended)
- Use **SSH keys**
- Disable password login (`PasswordAuthentication no`)
- Disable root login (`PermitRootLogin no`)
- Keep firewall enabled (UFW)

### 1) Install packages
```bash
sudo apt update
sudo apt install -y nginx php-fpm php-cli php-mbstring php-xml php-curl unzip composer git
```

### 2) Deploy via Git
Pick a folder for the site (example uses `/var/www/firbrigs`):

```bash
sudo mkdir -p /var/www/firbrigs
sudo chown -R $USER:$USER /var/www/firbrigs
cd /var/www/firbrigs

# Clone your repo (replace with your git URL)
git clone <YOUR_GIT_URL> .
```

Install PHP deps (PHPMailer):

```bash
composer install --no-dev --optimize-autoloader
```

### 3) Nginx server block (IP-only)
Create:
`/etc/nginx/sites-available/firbrigs`

Use this config (adjust PHP-FPM socket if needed):

```nginx
server {
  listen 80 default_server;
  listen [::]:80 default_server;

  # IP-only (catch-all)
  server_name _;

  root /var/www/firbrigs;
  index index.html;

  # Static files
  location / {
    try_files $uri $uri/ =404;
  }

  # PHP (contact form endpoint)
  location ~ \\.php$ {
    include snippets/fastcgi-php.conf;

    # Choose the correct socket from: ls /run/php/
    fastcgi_pass unix:/run/php/php8.1-fpm.sock;
  }

  # Basic hardening: deny hidden files
  location ~ /\\. {
    deny all;
  }
}
```

Enable it:

```bash
sudo ln -sf /etc/nginx/sites-available/firbrigs /etc/nginx/sites-enabled/firbrigs
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

### 4) SMTP env vars (Zoho)
Follow `DEPLOY_CONTABO_UBUNTU_SMTP.md` to set SMTP env vars for PHP-FPM, then:

```bash
php scripts/check_smtp_env.php
php scripts/test_smtp_send.php
```

### 5) Verify
- Open: `http://<YOUR_VPS_IP>/`
- Open: `http://<YOUR_VPS_IP>/contact.html`
- Submit the form and confirm it redirects to `contact.html?message=Successfull`

