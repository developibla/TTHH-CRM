<?php
declare(strict_types=1);

$user = current_user();
$cp = company_params();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($cp['empresa'] ?? 'TTHH') ?></title>

  <link rel="stylesheet" href="public/assets/css/app.css">
  <link rel="stylesheet" href="public/assets/css/ui.css">
</head>
<body>

<div class="container">

  <header class="topbar">
    <div class="brand">
      <!-- Toggle sidebar -->
      <button class="side-toggle" type="button" data-toggle-sidebar title="Menú">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
          <path d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8.5zm0-4a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 4.5z"/>
        </svg>
      </button>

      <?php if (!empty($cp['logo_path'])): ?>
        <img class="logo-img" src="<?= e($cp['logo_path']) ?>" alt="Logo">
      <?php else: ?>
        <div class="logo-fallback">TH</div>
      <?php endif; ?>

      <div class="meta">
        <div class="title"><?= e($cp['empresa'] ?? 'TTHH') ?></div>
        <div class="sub">Gestión de Talento Humano</div>
      </div>
    </div>

    <?php if ($user): ?>
      <div class="user-pill">
        <div>
          <div class="u1">Usuario</div>
          <div class="u2"><?= e($user['nombre'] ?? $user['usuario'] ?? '') ?></div>
        </div>
        <a class="btn-icon" href="index.php?r=logout" title="Cerrar sesión">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
            <path d="M6 2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-2h1v2h6V2H7v2H6V2z"/>
            <path d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
          </svg>
        </a>
      </div>
    <?php endif; ?>
  </header>

  <!-- overlay para sidebar en mobile -->
  <div class="sidebar-overlay" data-sidebar-overlay></div>

  <div class="shell">
    <aside class="sidebar">
      <!-- Botón cerrar solo en mobile (opcional) -->
      <div style="display:flex;justify-content:flex-end;margin-bottom:6px;">
        <button class="btn-icon" type="button" data-close-sidebar title="Cerrar menú">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
        </button>
      </div>

      <?php require __DIR__ . '/sidebar.php'; ?>
    </aside>

    <main class="content">
