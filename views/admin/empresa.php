<?php
declare(strict_types=1);

$empresa = company_params();
$ok = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

require __DIR__ . '/../partials/layout_top.php';
?>

<div class="card">
  <h2 style="margin:0 0 6px 0;">Parámetros de Empresa</h2>
  <div style="color:var(--muted);font-size:13px;">
    Estos datos se usarán en encabezados de reportes y recibos para no editarlos uno por uno.
  </div>

  <?php if ($ok): ?><div class="alert alert-ok" style="margin-top:12px;"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error" style="margin-top:12px;"><?= e($err) ?></div><?php endif; ?>

  <form method="post" action="index.php?r=empresa_post" enctype="multipart/form-data" style="margin-top:12px;">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

    <div class="grid">
      <div>
        <label class="label">Empresa</label>
        <input class="input" name="empresa" value="<?= e((string)$empresa['empresa']) ?>" required>

        <label class="label">RUC</label>
        <input class="input" name="ruc" value="<?= e((string)$empresa['ruc']) ?>">

        <label class="label">Teléfono</label>
        <input class="input" name="telefono" value="<?= e((string)$empresa['telefono']) ?>">

        <label class="label">Dirección</label>
        <input class="input" name="direccion" value="<?= e((string)$empresa['direccion']) ?>">

        <label class="label">Capital</label>
        <input class="input" name="capital" value="<?= e((string)$empresa['capital']) ?>">
      </div>

      <div>
        <label class="label">N° Patronal IPS</label>
        <input class="input" name="numero_patronal_ips" value="<?= e((string)$empresa['numero_patronal_ips']) ?>">

        <label class="label">Cantidad de empleados</label>
        <input class="input" type="number" min="0" name="cantidad_empleados" value="<?= e((string)$empresa['cantidad_empleados']) ?>">

        <label class="label">Logo (PNG/JPG)</label>
        <input class="input" type="file" name="logo" accept="image/png,image/jpeg">

        <div style="margin-top:12px;">
          <?php if (!empty($empresa['logo_path'])): ?>
            <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Logo actual:</div>
            <img src="<?= e($empresa['logo_path']) ?>" alt="Logo" style="max-width:220px;border-radius:14px;border:1px solid var(--border);background:#fff;">
          <?php else: ?>
            <div style="color:var(--muted);font-size:13px;">No hay logo cargado.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:14px;">
      <button class="btn btn-primary" type="submit">Guardar cambios</button>
      <a class="btn btn-ghost" href="index.php?r=home">Volver</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
