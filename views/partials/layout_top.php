<?php
declare(strict_types=1);
$cfg = require __DIR__ . '/../../app/config/config.php';
$empresa = company_params();
$user = current_user();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($cfg['app_name']) ?></title>
  <link rel="stylesheet" href="public/assets/css/app.css">
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div class="brand">
        <?php if (!empty($empresa['logo_path'])): ?>
          <img src="<?= e($empresa['logo_path']) ?>" alt="Logo" style="width:42px;height:42px;border-radius:12px;object-fit:cover;border:1px solid var(--border);">
        <?php else: ?>
          <div class="logo">TH</div>
        <?php endif; ?>
        <div class="meta">
          <div class="title"><?= e($empresa['empresa'] ?: 'Empresa') ?></div>
          <div class="sub"><?= e($cfg['app_name']) ?></div>
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:10px;">
        <?php if ($user): ?>
          <div style="font-size:13px;color:var(--muted);">
            <?= e($user['nombre']) ?> · <b><?= e($user['rol']) ?></b>
          </div>
          <?php if (($user['rol'] ?? '') === 'ADMIN'): ?>
            <a class="btn btn-ghost" href="index.php?r=empresa">Parámetros</a>
          <?php endif; ?>
          <a class="btn btn-primary" href="index.php?r=logout">Salir</a>
        <?php else: ?>
          <a class="btn btn-primary" href="index.php?r=login">Ingresar</a>
        <?php endif; ?>
      </div>
    </div>
