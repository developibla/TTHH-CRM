<?php
declare(strict_types=1);

if (!empty($_SESSION['user'])) {
  redirect('home');
}

$empresa = company_params();
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

require __DIR__ . '/../partials/layout_top.php';
?>

<div class="card" style="max-width:520px;margin:18px auto;">
  <h2 style="margin:0 0 6px 0;">Acceso al sistema</h2>
  <div style="color:var(--muted);font-size:13px;">
    Inicia sesión para gestionar colaboradores, planillas y reportes.
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-top:12px;"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="index.php?r=login_post" style="margin-top:12px;">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <label class="label">Usuario</label>
    <input class="input" type="text" name="usuario" autocomplete="username" required>

    <label class="label">Contraseña</label>
    <input class="input" type="password" name="password" autocomplete="current-password" required>

    <div style="display:flex;gap:10px;align-items:center;justify-content:space-between;margin-top:14px;">
      <button class="btn btn-primary" type="submit" style="flex:1;">Ingresar</button>
      <a class="btn btn-ghost" href="index.php?r=login">Limpiar</a>
    </div>

    <div style="margin-top:10px;font-size:12px;color:var(--muted);">
      Demo: <b>admin</b> / <b>Admin@1234</b>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
