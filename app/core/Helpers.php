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

/** Carga parámetros empresa (si tenés esa tabla ya, adaptá el SQL aquí) */
function company_params(): array {
  // Ajustá el nombre de tu tabla y campos si difiere
  $row = DB::fetchOne("SELECT * FROM empresa_parametros ORDER BY EmpresaId DESC LIMIT 1");
  return $row ?: [
    'empresa' => 'Empresa',
    'logo_path' => '',
  ];
}
