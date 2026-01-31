<?php

// Contact form → SMTP mail sender (PHPMailer)
// IMPORTANT: SMTP secrets must be provided via environment variables, not committed to this repo.
//
// Required env vars:
// - SMTP_HOST (e.g. smtppro.zoho.com)
// - SMTP_PORT (e.g. 465)
// - SMTP_USER (e.g. noreply@k19.online)
// - SMTP_PASS (Zoho app password)
// - SMTP_FROM_EMAIL (e.g. noreply@k19.online)
// - CONTACT_TO_EMAIL (e.g. lakshan@k19global.com)

function redirect_with_message(string $message): void {
  header('Location: contact.html?message=' . rawurlencode($message));
  exit;
}

// Only accept POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  redirect_with_message('Failed');
}

// Basic honeypot: bots fill hidden fields
$honeypot = trim($_POST['company'] ?? '');
if ($honeypot !== '') {
  redirect_with_message('Failed');
}

// Basic rate limit (per IP): max 8 submissions per 30 minutes
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateWindowSeconds = 30 * 60;
$rateMax = 8;
$rateFile = sys_get_temp_dir() . '/contact_rate_' . sha1($ip) . '.json';
try {
  $now = time();
  $timestamps = [];
  if (is_file($rateFile)) {
    $raw = @file_get_contents($rateFile);
    $decoded = json_decode($raw ?: '[]', true);
    if (is_array($decoded)) {
      $timestamps = array_values(array_filter($decoded, fn($t) => is_int($t) && ($now - $t) < $rateWindowSeconds));
    }
  }
  if (count($timestamps) >= $rateMax) {
    redirect_with_message('Failed');
  }
  $timestamps[] = $now;
  @file_put_contents($rateFile, json_encode($timestamps));
} catch (Throwable $e) {
  // If rate limiting fails, continue (do not block legit submissions)
}

// Read and validate form values
$userName = trim($_POST['username'] ?? '');
$senderEmail = trim($_POST['email'] ?? '');
$userPhone = trim($_POST['phone'] ?? '');
$userSubject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($userName === '' || $senderEmail === '' || $userPhone === '' || $userSubject === '' || $message === '') {
  redirect_with_message('Failed');
}

if (!filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
  redirect_with_message('Failed');
}

// Length limits (simple abuse protection)
$userName = mb_substr($userName, 0, 100);
$senderEmail = mb_substr($senderEmail, 0, 254);
$userPhone = mb_substr($userPhone, 0, 40);
$userSubject = mb_substr($userSubject, 0, 150);
$message = mb_substr($message, 0, 5000);

// Load PHPMailer (Composer)
$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
  // Composer dependencies not installed on the server yet.
  redirect_with_message('Failed');
}
require_once $autoload;

use PHPMailer\PHPMailer\PHPMailer;

$smtpHost = getenv('SMTP_HOST') ?: '';
$smtpPort = getenv('SMTP_PORT') ?: '';
$smtpUser = getenv('SMTP_USER') ?: '';
$smtpPass = getenv('SMTP_PASS') ?: '';
$smtpFromEmail = getenv('SMTP_FROM_EMAIL') ?: '';
$contactToEmail = getenv('CONTACT_TO_EMAIL') ?: '';

if ($smtpHost === '' || $smtpPort === '' || $smtpUser === '' || $smtpPass === '' || $smtpFromEmail === '' || $contactToEmail === '') {
  redirect_with_message('Failed');
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

  // Deliverability best practice: From must be your domain mailbox; Reply-To is the visitor.
  $mail->setFrom($smtpFromEmail, 'Big Bell Engineering Website');
  $mail->addAddress($contactToEmail);
  $mail->addReplyTo($senderEmail, $userName);

  $mail->Subject = 'Website contact: ' . $userSubject;
  $mail->Body =
    "Name: {$userName}\n" .
    "Email: {$senderEmail}\n" .
    "Phone: {$userPhone}\n" .
    "Subject: {$userSubject}\n\n" .
    "Message:\n{$message}\n";

  $mail->send();
  redirect_with_message('Successfull');
} catch (Throwable $e) {
  error_log('sendemail.php: SMTP send failed: ' . $e->getMessage());
  redirect_with_message('Failed');
}

?>