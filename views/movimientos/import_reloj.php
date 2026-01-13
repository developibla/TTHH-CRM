<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/core/Helpers.php';
require_once __DIR__ . '/../../app/core/Db.php';

$ok  = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

// Últimos movimientos (máx 300)
$items = DB::fetchAll("
  SELECT
    rm.Id,
    rm.CodigoReloj,
    rm.ColaboradorId,
    rm.FechaHora,
    rm.TipoEvento,
    rm.Dispositivo,
    rm.FuenteArchivo,
    c.Apellidos,
    c.Nombres
  FROM reloj_movimientos rm
  LEFT JOIN colaboradores c ON c.ColaboradorId = rm.ColaboradorId
  ORDER BY rm.FechaHora DESC
  LIMIT 300
");

require __DIR__ . '/../partials/layout_top.php';
?>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Importar marcaciones (Reloj)</h2>
      <div style="color:var(--muted);font-size:13px;margin-top:4px;">
        Subí el CSV del reloj y luego presioná <b>Reconciliar</b> para vincular con colaboradores por <b>CódigoReloj</b>.
      </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <form method="post" action="index.php?r=import_reloj_post" enctype="multipart/form-data"
            style="display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input class="input" type="file" name="csv_file" accept=".csv,text/csv" required>
        <button class="btn btn-primary" type="submit">Importar</button>
      </form>

      <form method="post" action="index.php?r=reconcile_reloj_post" style="display:flex;align-items:center;">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <button class="btn btn-outline" type="submit">Reconciliar</button>
      </form>
    </div>
  </div>

  <?php if ($ok): ?><div class="alert alert-ok" style="margin-top:12px;"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error" style="margin-top:12px;"><?= e($err) ?></div><?php endif; ?>

  <div class="table-wrap" style="margin-top:12px;">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>CódigoReloj</th>
          <th>Fecha/Hora</th>
          <th>Tipo evento</th>
          <th>Dispositivo</th>
          <th>Colaborador</th>
          <th>Fuente</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$items): ?>
          <tr><td colspan="99" style="padding:12px;color:var(--muted);">Sin registros.</td></tr>
        <?php endif; ?>

        <?php foreach ($items as $r): ?>
          <tr>
            <td><?= e((string)$r['Id']) ?></td>
            <td><?= e((string)$r['CodigoReloj']) ?></td>
            <td><?= e((string)$r['FechaHora']) ?></td>
            <td><?= e((string)($r['TipoEvento'] ?? '')) ?></td>
            <td><?= e((string)($r['Dispositivo'] ?? '')) ?></td>
            <td><?= e(trim((string)($r['Apellidos'] ?? '') . ' ' . (string)($r['Nombres'] ?? ''))) ?></td>
            <td><?= e((string)($r['FuenteArchivo'] ?? '')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:10px;font-size:12px;color:var(--muted);">
    Mostrando últimos 300 movimientos.
  </div>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
