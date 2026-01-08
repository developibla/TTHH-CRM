<?php
declare(strict_types=1);
require __DIR__ . '/../partials/layout_top.php';
$route = $route ?? ($_GET['r'] ?? '');
?>
<div class="card">
  <h2 style="margin:0 0 6px 0;">Ruta no encontrada</h2>
  <div style="color:var(--muted);font-size:13px;">
    No existe la ruta: <b><?= e((string)$route) ?></b>
  </div>
  <div style="margin-top:12px;">
    <a class="btn btn-primary" href="index.php?r=catalogos&t=cargo">Ir a Catálogos</a>
  </div>
</div>
<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
