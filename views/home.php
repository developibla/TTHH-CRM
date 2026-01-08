<?php
declare(strict_types=1);

require __DIR__ . '/partials/layout_top.php';
$empresa = company_params();
?>

<div class="card">
  <h2 style="margin:0 0 6px 0;">Panel principal</h2>
  <div style="color:var(--muted);">
    Bienvenido/a al sistema. Desde aquí iremos agregando los módulos: Colaboradores, Vacaciones, Suspensiones, IPS, Recibos y Reportes.
  </div>

  <div class="grid" style="margin-top:14px;">
    <div class="card" style="margin:0;">
      <h3 style="margin-top:0;">Empresa</h3>
      <div><b><?= e($empresa['empresa']) ?></b></div>
      <div style="color:var(--muted);font-size:13px;">RUC: <?= e($empresa['ruc']) ?></div>
      <div style="color:var(--muted);font-size:13px;">Tel: <?= e($empresa['telefono']) ?></div>
      <div style="color:var(--muted);font-size:13px;">IPS: <?= e($empresa['numero_patronal_ips']) ?></div>
    </div>

    <div class="card" style="margin:0;">
      <h3 style="margin-top:0;">Próximo paso</h3>
      <ul style="margin:0;color:var(--muted);">
        <li>CRUD de colaboradores (tabla + formulario + listado + búsqueda)</li>
        <li>Subida de foto/archivo (documentos)</li>
        <li>Reportes e impresión con encabezado empresa</li>
      </ul>
    </div>
  </div>
</div>

<?php require __DIR__ . '/partials/layout_bottom.php'; ?>
