<?php

/**
 * CLI helper: sends a test email using the same SMTP config as sendemail.php.
 *
 * Usage:
 *   php scripts/test_smtp_send.php
 *
 * Requirements:
 * - composer install has been run (vendor/autoload.php exists)
 * - SMTP_* env vars are set (see scripts/check_smtp_env.php)
 */

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoload)) {
  fwrite(STDERR, "Missing vendor/autoload.php. Run: composer install\n");
  exit(2);
}
require_once $autoload;

use PHPMailer\PHPMailer\PHPMailer;

$smtpHost = getenv('SMTP_HOST') ?: '';
$smtpPort = getenv('SMTP_PORT') ?: '';
$smtpUser = getenv('SMTP_USER') ?: '';
$smtpPass = getenv('SMTP_PASS') ?: '';
$smtpFromEmail = getenv('SMTP_FROM_EMAIL') ?: '';
$contactToEmail = getenv('CONTACT_TO_EMAIL') ?: '';

foreach ([
  'SMTP_HOST' => $smtpHost,
  'SMTP_PORT' => $smtpPort,
  'SMTP_USER' => $smtpUser,
  'SMTP_PASS' => $smtpPass,
  'SMTP_FROM_EMAIL' => $smtpFromEmail,
  'CONTACT_TO_EMAIL' => $contactToEmail,
] as $k => $v) {
  if ($v === '') {
    fwrite(STDERR, "Missing env var: {$k}\n");
    exit(2);
  }
}

try {
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host = $smtpHost;
  $mail->SMTPAuth = true;
  $mail->Username = $smtpUser;
  $mail->Password = $smtpPass;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL on 465
  $mail->Port = (int)$smtpPort;

  $mail->setFrom($smtpFromEmail, 'Big Bell Engineering Website');
  $mail->addAddress($contactToEmail);
  $mail->Subject = 'SMTP test: ' . date('c');
  $mail->Body = "This is a test email sent from the VPS via Zoho SMTP.\n";

  $mail->send();
  echo "Sent OK\n";
  exit(0);
} catch (Throwable $e) {
  fwrite(STDERR, "Send FAILED: " . $e->getMessage() . "\n");
  exit(1);
}

