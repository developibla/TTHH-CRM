<?php
declare(strict_types=1);

function csrf_token(): string {
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(16));
  }
  return (string)$_SESSION['_csrf'];
}

function csrf_check(?string $token): void {
  $ok = isset($_SESSION['_csrf']) && is_string($token) && hash_equals($_SESSION['_csrf'], $token);
  if (!$ok) {
    http_response_code(403);
    exit('CSRF inválido.');
  }
}
