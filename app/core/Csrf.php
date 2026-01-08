<?php
declare(strict_types=1);

/**
 * CSRF Helper (funcional, no clase)
 * Uso:
 *  - csrf_token()  → genera / devuelve token
 *  - csrf_verify($token) → valida token recibido
 */

function csrf_token(): string
{
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }

  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }

  return $_SESSION['_csrf'];
}

function csrf_verify(?string $token): bool
{
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }

  if (empty($_SESSION['_csrf']) || empty($token)) {
    return false;
  }

  return hash_equals($_SESSION['_csrf'], $token);
}
