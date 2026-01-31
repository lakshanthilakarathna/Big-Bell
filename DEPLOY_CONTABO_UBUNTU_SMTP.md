## Contabo Ubuntu (Nginx + PHP-FPM) — Zoho SMTP Setup

### 0) Security first (do this before anything else)
- **Rotate your VPS password immediately** if it was shared anywhere.
- **Rotate your Zoho app password** if it was shared anywhere.
- **Recommended hardening**:
  - Use SSH keys
  - Disable password SSH login
  - Disable root login

### 1) Install runtime dependencies (Ubuntu)
SSH to the VPS and run:

```bash
sudo apt update
sudo apt install -y nginx php-fpm php-cli php-mbstring php-xml php-curl unzip composer
```

Notes:
- `php-mbstring` is required because `sendemail.php` uses `mb_substr`.

### 2) Deploy the site
Assuming your site lives here:

```bash
cd /var/www/<your-domain>/
```

Make sure these files exist:
- `contact.html`
- `sendemail.php`
- `composer.json`

### 3) Install PHPMailer (Composer)
From the site folder:

```bash
composer install --no-dev --optimize-autoloader
```

Verify:

```bash
test -f vendor/autoload.php && echo "OK: vendor/autoload.php exists"
```

### 4) Configure SMTP env vars for PHP-FPM (recommended)
Find your PHP-FPM pool file (commonly `www.conf`):

```bash
ls /etc/php/*/fpm/pool.d/*.conf
```

Edit the pool used by your Nginx site (often `/etc/php/<version>/fpm/pool.d/www.conf`) and add:

```ini
; Zoho SMTP (do not commit secrets)
env[SMTP_HOST]=smtppro.zoho.com
env[SMTP_PORT]=465
env[SMTP_USER]=noreply@k19.online
env[SMTP_PASS]=<ROTATED_ZOHO_APP_PASSWORD>
env[SMTP_FROM_EMAIL]=noreply@k19.online
env[CONTACT_TO_EMAIL]=lakshan@k19global.com

; IMPORTANT: ensure env vars aren’t stripped
clear_env = no
```

Restart:

```bash
sudo systemctl restart php*-fpm
sudo systemctl reload nginx
```

### 5) Quick env-var verification (CLI)
Run:

```bash
php scripts/check_smtp_env.php
```

Expected: it prints **OK** for all required variables (it will not print the SMTP password).

### 6) Test SMTP send (CLI)
Run:

```bash
php scripts/test_smtp_send.php
```

Expected: “Sent OK” and you receive an email at `lakshan@k19global.com`.

### 7) Test via the website form
- Open `https://<your-domain>/contact.html`
- Submit the form
- Confirm redirect:
  - `contact.html?message=Successfull` on success
  - `contact.html?message=Failed` on failure
- Confirm mail:
  - **To**: `lakshan@k19global.com`
  - **From**: `noreply@k19.online`
  - **Reply-To**: visitor email

### Troubleshooting
#### A) Always redirects to Failed
- Check that Composer dependencies are installed:

```bash
ls -la vendor/autoload.php
```

- Check PHP-FPM env vars are visible (see section 5).

- Check logs:
  - Nginx error log: `/var/log/nginx/error.log`
  - PHP-FPM log (varies): `/var/log/php*-fpm.log` or `journalctl -u php*-fpm --since "10 min ago"`

#### B) SMTP port blocked on VPS
If outbound `465` is blocked, the fix is to switch to **Zoho 587 STARTTLS**.
That requires a small code change in `sendemail.php` (use `ENCRYPTION_STARTTLS` and port 587).

