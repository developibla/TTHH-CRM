<?php
declare(strict_types=1);

require_login();

$q = trim((string)($_GET['q'] ?? ''));

$ok = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

/*
  Búsqueda: por CodigoReloj, por documento, o por nombre/apellido
*/
$where = '';
$params = [];

if ($q !== '') {
  $where = "WHERE
      rm.CodigoReloj LIKE ?
      OR c.NroDocumento LIKE ?
      OR CONCAT(IFNULL(c.Apellidos,''), ' ', IFNULL(c.Nombres,'')) LIKE ?
  ";
  $like = '%' . $q . '%';
  $params = [$like, $like, $like];
}

$rows = DB::fetchAll("
  SELECT
    rm.MovimientoId,
    rm.CodigoReloj,
    rm.FechaHora,
    rm.DispositivoNro,
    rm.TipoRegistro,
    c.ColaboradorId,
    c.Apellidos,
    c.Nombres,
    c.NroDocumento
  FROM reloj_movimientos rm
  LEFT JOIN colaboradores c ON c.ColaboradorId = rm.ColaboradorId
  $where
  ORDER BY rm.FechaHora DESC
  LIMIT 250
", $params);

require __DIR__ . '/../partials/layout_top.php';
?>

<style>
/* buscador con lupa a la derecha (inline) */
.searchbar{ display:flex; align-items:center; flex:1; }
.search-group{
  display:flex; align-items:center; gap:8px; flex:1;
  min-width: 280px;
}
.search-input{ flex:1; min-width: 180px; }

@media (max-width: 700px){
  .search-group{ width:100%; min-width:0; }
}
</style>

<div class="card">
  <div class="page-head" style="display:flex;justify-content:space-between;align-items:flex-end;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Marcaciones del reloj</h2>
      <div style="color:var(--muted);font-size:13px;margin-top:4px;">
        CSV: <b>ID de usuario</b>, <b>Fecha/Hora</b>, <b>Dispositivo</b>, <b>Tipo</b> (0/1/2/3).
      </div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <!-- IMPORT -->
      <form method="post" action="index.php?r=import_reloj_post" enctype="multipart/form-data"
            style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

        <input class="input" type="file" name="csv_file" accept=".csv,text/csv" required style="max-width:340px;">

        <button class="btn btn-primary" type="submit" title="Importar CSV">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.6a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6a.5.5 0 0 1 1 0v2.6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.6a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
          </svg>
          Importar
        </button>
      </form>

      <!-- RECONCILIAR -->
      <form method="post" action="index.php?r=reconcile_reloj_post" style="display:flex;align-items:center;">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <button class="btn btn-outline" type="submit"
                title="Asociar movimientos que quedaron sin colaborador (ColaboradorId NULL) usando CodigoReloj"
                onclick="return confirm('Esto asociará movimientos sin colaborador usando CodigoReloj. ¿Continuar?');">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.418A6 6 0 1 1 8 2v1z"/>
            <path d="M8 0a.5.5 0 0 1 .5.5V3h2.5a.5.5 0 0 1 0 1H8a.5.5 0 0 1-.5-.5V.5A.5.5 0 0 1 8 0z"/>
          </svg>
          Reconciliar
        </button>
      </form>
    </div>
  </div>

  <?php if ($ok): ?><div class="alert alert-ok" style="margin-top:12px;"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error" style="margin-top:12px;"><?= e($err) ?></div><?php endif; ?>

  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-top:12px;">
    <form method="get" action="index.php" class="searchbar">
      <input type="hidden" name="r" value="import_reloj">

      <div class="search-group">
        <input class="input search-input" name="q"
               placeholder="Buscar (código reloj, documento, apellidos/nombres)..."
               value="<?= e($q) ?>">

        <button class="btn btn-ico btn-outline" type="submit" title="Buscar">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
          </svg>
        </button>

        <?php if ($q !== ''): ?>
          <a class="btn btn-ico btn-outline" href="index.php?r=import_reloj" title="Limpiar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="table-wrap" style="margin-top:12px;">
    <table class="table" style="min-width: 980px;">
      <thead>
        <tr>
          <th style="width:90px;">MovID</th>
          <th style="width:120px;">Código</th>
          <th>Colaborador</th>
          <th style="width:170px;">FechaHora</th>
          <th style="width:110px;">Dispositivo</th>
          <th style="width:170px;">Tipo</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="99" style="padding:12px;color:var(--muted);">Sin registros.</td></tr>
        <?php endif; ?>

        <?php foreach ($rows as $r): ?>
          <?php
            $nombre = trim((string)($r['Apellidos'] ?? '') . ' ' . (string)($r['Nombres'] ?? ''));
            $doc = (string)($r['NroDocumento'] ?? '');
          ?>
          <tr>
            <td><?= e((string)$r['MovimientoId']) ?></td>
            <td><?= e((string)$r['CodigoReloj']) ?></td>
            <td>
              <?php if (!empty($r['ColaboradorId'])): ?>
                <div style="font-weight:900;"><?= e($nombre !== '' ? $nombre : '(Sin nombre)') ?></div>
                <div style="color:var(--muted);font-size:12px;">
                  ID: <?= e((string)$r['ColaboradorId']) ?>
                  <?php if ($doc !== ''): ?> · Doc: <?= e($doc) ?><?php endif; ?>
                </div>
              <?php else: ?>
                <span style="color:var(--muted);">No asociado</span>
                <div style="color:var(--muted);font-size:12px;">(Cargá CodigoReloj en el colaborador)</div>
              <?php endif; ?>
            </td>
            <td><?= e((string)$r['FechaHora']) ?></td>
            <td><?= e((string)($r['DispositivoNro'] ?? '')) ?></td>
            <td><?= e(reloj_tipo_label((int)$r['TipoRegistro'])) ?> <span style="color:var(--muted);">(#<?= e((string)$r['TipoRegistro']) ?>)</span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:10px;font-size:12px;color:var(--muted);">
    Mostrando últimas 250 marcaciones.
  </div>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
