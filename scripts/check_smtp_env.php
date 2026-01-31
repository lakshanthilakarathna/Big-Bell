<?php

/**
 * CLI helper: verifies required SMTP env vars are visible to PHP.
 *
 * Usage:
 *   php scripts/check_smtp_env.php
 */

$required = [
  'SMTP_HOST',
  'SMTP_PORT',
  'SMTP_USER',
  'SMTP_PASS',
  'SMTP_FROM_EMAIL',
  'CONTACT_TO_EMAIL',
];

$ok = true;
foreach ($required as $key) {
  $val = getenv($key);
  $present = ($val !== false && $val !== '');
  if (!$present) {
    $ok = false;
  }

  // Never print the SMTP password value.
  if ($key === 'SMTP_PASS') {
    echo $key . ': ' . ($present ? 'OK (set)' : 'MISSING') . PHP_EOL;
    continue;
  }

  echo $key . ': ' . ($present ? ('OK (' . $val . ')') : 'MISSING') . PHP_EOL;
}

exit($ok ? 0 : 1);

