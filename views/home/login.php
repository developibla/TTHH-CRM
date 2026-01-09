<?php
declare(strict_types=1);

$err = $_SESSION['login_error'] ?? '';
$info = $_SESSION['login_info'] ?? '';
$lastUser = $_SESSION['login_last_user'] ?? '';
unset($_SESSION['login_error'], $_SESSION['login_info']);

$flashErr = $_SESSION['flash_error'] ?? '';
$flashOk  = $_SESSION['flash_ok'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_ok']);

$empresa = company_params();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login · TTHH</title>
  <link rel="stylesheet" href="public/assets/css/app.css">
</head>
<body>
  <div class="container" style="display:flex;justify-content:center;align-items:center;min-height:85vh;">
    <div class="card" style="max-width:420px;width:100%;">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
        <?php if (!empty($empresa['logo_path'])): ?>
          <img src="<?= e($empresa['logo_path']) ?>" style="height:52px;object-fit:contain;" alt="Logo">
        <?php else: ?>
          <div class="logo-fallback">TH</div>
        <?php endif; ?>
        <div>
          <div style="font-weight:900;font-size:16px;"><?= e((string)($empresa['empresa'] ?? 'Empresa')) ?></div>
          <div style="color:var(--muted);font-size:12px;">Acceso al sistema</div>
        </div>
      </div>

      <?php if ($err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
      <?php endif; ?>

      <?php if ($flashErr): ?>
        <div class="alert alert-error"><?= e($flashErr) ?></div>
      <?php endif; ?>

      <?php if ($info): ?>
        <div class="alert alert-ok"><?= e($info) ?></div>
      <?php endif; ?>

      <?php if ($flashOk): ?>
        <div class="alert alert-ok"><?= e($flashOk) ?></div>
      <?php endif; ?>

      <form method="post" action="index.php?r=login_post">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

        <label class="label">Usuario</label>
        <input class="input" name="usuario" required autocomplete="username"
               value="<?= e((string)$lastUser) ?>">

        <label class="label">Clave</label>
        <input class="input" name="clave" type="password" required autocomplete="current-password">

        <div style="display:flex;justify-content:flex-end;margin-top:12px;">
          <button class="btn btn-primary" type="submit" title="Ingresar">Ingresar</button>
        </div>
      </form>

      <div style="margin-top:12px;color:var(--muted);font-size:12px;">
        Si olvidaste tu clave, solicitá al administrador la restauración.
      </div>
    </div>
  </div>
</body>
</html>
