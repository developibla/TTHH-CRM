<?php
declare(strict_types=1);

require __DIR__ . '/../partials/layout_top.php';
?>

<div class="card">
  <h2 style="margin:0 0 6px 0;">Parámetros de Empresa</h2>
  <div style="color:var(--muted);font-size:13px;">
    Esta pantalla será el mantenimiento de datos generales (logo, RUC, teléfono, etc.)
  </div>

  <div class="card" style="margin-top:14px;background:rgba(15,42,28,.02);">
    <div style="font-weight:900;margin-bottom:6px;">Próximo paso</div>
    <div style="color:var(--muted);font-size:13px;">
      Aquí vamos a construir el formulario completo y la carga de logo con vista previa.
    </div>
  </div>

  <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;">
    <a class="btn btn-primary" href="index.php?r=catalogos&t=cargo">Ir a Catálogos</a>
    <a class="btn btn-outline" href="index.php?r=colaboradores">Ir a Colaboradores</a>
  </div>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
