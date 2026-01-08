<?php
declare(strict_types=1);

require __DIR__ . '/../partials/layout_top.php';

$empresa = company_params();

$ok = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);
?>

<div class="card">
  <h2 style="margin:0 0 6px 0;">Parámetros de Empresa</h2>
  <div style="color:var(--muted);font-size:13px;">
    Estos datos se usarán en encabezados de reportes y recibos.
  </div>

  <?php if ($ok): ?><div class="alert alert-ok"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

  <div class="grid" style="margin-top:14px;">

    <!-- FORM -->
    <div class="card" style="margin:0;">
      <h3 style="margin:0 0 10px 0;">Datos</h3>

      <form method="post" action="index.php?r=empresa_post" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e((string)($empresa['id'] ?? 1)) ?>">

        <label class="label">Empresa</label>
        <input class="input" name="empresa" required value="<?= e((string)($empresa['empresa'] ?? '')) ?>">

        <label class="label">RUC</label>
        <input class="input" name="ruc" value="<?= e((string)($empresa['ruc'] ?? '')) ?>">

        <label class="label">Teléfono</label>
        <input class="input" name="telefono" value="<?= e((string)($empresa['telefono'] ?? '')) ?>">

        <label class="label">Dirección</label>
        <input class="input" name="direccion" value="<?= e((string)($empresa['direccion'] ?? '')) ?>">

        <label class="label">Capital</label>
        <input class="input" name="capital" value="<?= e((string)($empresa['capital'] ?? '')) ?>">

        <label class="label">N° Patronal IPS</label>
        <input class="input" name="numero_patronal_ips" value="<?= e((string)($empresa['numero_patronal_ips'] ?? '')) ?>">

        <label class="label">Cantidad de empleados</label>
        <input class="input" type="number" min="0" name="cantidad_empleados"
               value="<?= e((string)($empresa['cantidad_empleados'] ?? 0)) ?>">

        <label class="label">Logo (PNG/JPG)</label>
        <input class="input" type="file" name="logo" accept=".png,.jpg,.jpeg,.webp">

        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;flex-wrap:wrap;">
          <button class="btn btn-primary" type="submit" title="Guardar">
            Guardar
          </button>
        </div>

        <div style="margin-top:10px;font-size:12px;color:var(--muted);">
          Última actualización:
          <b><?= e((string)($empresa['actualizado_en'] ?? '')) ?></b>
        </div>
      </form>
    </div>

    <!-- PREVIEW -->
    <div class="card" style="margin:0;">
      <h3 style="margin:0 0 10px 0;">Vista previa</h3>

      <div style="display:flex;align-items:center;gap:12px;">
        <?php if (!empty($empresa['logo_path'])): ?>
          <img src="<?= e((string)$empresa['logo_path']) ?>" alt="Logo"
               style="height:70px;max-width:240px;object-fit:contain;">
        <?php else: ?>
          <div class="logo-fallback">TH</div>
        <?php endif; ?>

        <div>
          <div style="font-weight:900;font-size:16px;"><?= e((string)($empresa['empresa'] ?? '')) ?></div>
          <div style="color:var(--muted);font-size:13px;">
            RUC: <?= e((string)($empresa['ruc'] ?? '')) ?> · Tel: <?= e((string)($empresa['telefono'] ?? '')) ?>
          </div>
          <div style="color:var(--muted);font-size:13px;">
            Dirección: <?= e((string)($empresa['direccion'] ?? '')) ?>
          </div>
          <div style="color:var(--muted);font-size:13px;margin-top:6px;">
            IPS: <?= e((string)($empresa['numero_patronal_ips'] ?? '')) ?> · Empleados: <?= e((string)($empresa['cantidad_empleados'] ?? 0)) ?>
          </div>
        </div>
      </div>

      <div style="margin-top:14px;padding:12px;border:1px dashed var(--border);border-radius:12px;">
        <div style="font-weight:900;margin-bottom:6px;">Ejemplo de encabezado para reportes</div>
        <div style="font-size:13px;color:var(--muted);">
          <?= e((string)($empresa['empresa'] ?? '')) ?> — RUC <?= e((string)($empresa['ruc'] ?? '')) ?><br>
          <?= e((string)($empresa['direccion'] ?? '')) ?> · Tel: <?= e((string)($empresa['telefono'] ?? '')) ?><br>
          IPS: <?= e((string)($empresa['numero_patronal_ips'] ?? '')) ?>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
