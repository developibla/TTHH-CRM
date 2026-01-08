<?php
declare(strict_types=1);

function require_login(): void {
  if (empty($_SESSION['user'])) {
    redirect('login');
  }
}

function require_role(array $roles): void {
  require_login();
  $u = $_SESSION['user'];
  if (!in_array($u['rol'] ?? '', $roles, true)) {
    http_response_code(403);
    echo "Acceso denegado.";
    exit;
  }
}
