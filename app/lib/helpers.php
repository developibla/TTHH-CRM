<?php
declare(strict_types=1);

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $route): never {
  header("Location: index.php?r=" . urlencode($route));
  exit;
}

function csrf_token(): string {
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['_csrf'];
}

function csrf_validate(): void {
  $t = $_POST['_csrf'] ?? '';
  if (!$t || empty($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], (string)$t)) {
    http_response_code(403);
    echo "CSRF inválido.";
    exit;
  }
}

function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}

function company_params(): array {
  static $cache = null;
  if (is_array($cache)) return $cache;

  $stmt = db()->query("SELECT * FROM empresa_parametros WHERE id=1 LIMIT 1");
  $row = $stmt->fetch();
  $cache = $row ?: [
    'empresa' => '',
    'ruc' => '',
    'telefono' => '',
    'direccion' => '',
    'capital' => '',
    'numero_patronal_ips' => '',
    'cantidad_empleados' => 0,
    'logo_path' => null,
  ];
  return $cache;
}
