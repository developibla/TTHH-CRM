<?php
declare(strict_types=1);

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): void {
  header("Location: $url");
  exit;
}

function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}

function require_login(): void {
  if (!current_user()) redirect('index.php?r=login');
}

/**
 * Devuelve el último registro de empresa_parametros
 * (tu tabla tiene PK `id`)
 */
function company_params(): array {
  $row = DB::fetchOne("SELECT * FROM empresa_parametros ORDER BY id DESC LIMIT 1");
  return $row ?: [
    'empresa' => 'Empresa',
    'ruc' => '',
    'telefono' => '',
    'direccion' => '',
    'capital' => '',
    'numero_patronal_ips' => '',
    'cantidad_empleados' => 0,
    'logo_path' => '',
  ];
}
