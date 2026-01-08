<?php
declare(strict_types=1);

$key = (string)($_GET['t'] ?? 'cargo');
$q = trim((string)($_GET['q'] ?? ''));
$cfgAll = catalogos_config();
$cat = catalogo_get($key);

$items = catalogo_list($key, $q);

$ok = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

require __DIR__ . '/../partials/layout_top.php';

function json_attr(array $arr): string {
  return htmlspecialchars(json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
}
?>

<style>
.modal{ position:fixed; inset:0; display:none; z-index:9999; }
.modal.is-open{ display:block; }
.modal-overlay{ position:absolute; inset:0; background:rgba(0,0,0,.45); }
.modal-dialog{
  position:relative;
  width:min(720px, 94vw);
  margin:7vh auto;
  background:rgba(255,255,255,.98);
  border-radius:16px;
  box-shadow:0 18px 50px rgba(0,0,0,.25);
  border:1px solid rgba(255,255,255,.65);
  overflow:hidden;
}
.modal-dialog.sm{ width:min(460px, 92vw); margin: 18vh auto; }
.modal-head{
  display:flex; align-items:center; justify-content:space-between; gap:12px;
  padding:14px 16px;
  border-bottom:1px solid var(--border);
  background:linear-gradient(180deg, rgba(17,138,85,.08), rgba(255,255,255,0));
}
.modal-title{ font-weight:950; margin:0; font-size:15px; }
.modal-sub{ color:var(--muted); font-size:12px; margin-top:2px; }
.modal-body{ padding:14px 16px; }
.modal-actions{
  display:flex; justify-content:flex-end; gap:10px;
  padding:12px 16px;
  border-top:1px solid var(--border);
  background:rgba(15,42,28,.02);
}
body.modal-open{ overflow:hidden; }

.page-head{ display:flex; justify-content:space-between; align-items:flex-end; gap:12px; flex-wrap:wrap; }
.page-head h2{ margin:0; }
.tools{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.table-actions{ display:flex; gap:8px; align-items:center; }

/* TOAST */
#toast{
  position: fixed;
  right: 16px;
  bottom: 16px;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid var(--border);
  box-shadow: 0 10px 30px rgba(0,0,0,.15);
  background: #fff;
  font-size: 13px;
  transform: translateY(16px);
  opacity: 0;
  pointer-events: none;
  transition: .2s;
  z-index: 10000;
}
#toast.show{ opacity:1; transform: translateY(0); }
#toast.ok{ border-color: rgba(17,138,85,.35); }
#toast.err{ border-color: rgba(220,53,69,.35); }

/* ✅ ELIMINAR “ELEGANTE”: gris y rojo solo en hover/focus */
.btn-danger{
  background: rgba(15, 23, 42, .06) !important;     /* gris suave */
  border: 1px solid rgba(15, 23, 42, .14) !important;
  color: rgba(15, 23, 42, .70) !important;
}
.btn-danger:hover,
.btn-danger:focus{
  background: rgba(220, 53, 69, .10) !important;    /* rojo suave */
  border-color: rgba(220, 53, 69, .35) !important;
  color: rgba(220, 53, 69, .95) !important;
}
.btn-danger:active{
  background: rgba(220, 53, 69, .16) !important;
}
.btn-danger svg{ color: currentColor; }
</style>

<div id="toast"></div>

<div class="card">
  <div class="page-head">
    <div>
      <h2>Catálogos referenciales</h2>
      <div style="color:var(--muted);font-size:13px;margin-top:4px;">
        Administrar: Cargos, Áreas, Sectores, Tipos y Turnos.
      </div>
    </div>

    <div class="tools">
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <?php foreach ($cfgAll as $k => $c): ?>
          <a class="btn btn-sm <?= $k===$key ? 'btn-primary' : 'btn-outline' ?>"
             href="index.php?r=catalogos&t=<?= e($k) ?>"
             title="<?= e($c['title']) ?>">
            <?= e($c['title']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <?php if ($ok): ?><div class="alert alert-ok" style="margin-top:12px;"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error" style="margin-top:12px;"><?= e($err) ?></div><?php endif; ?>

  <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-top:12px;">
    <form method="get" action="index.php" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
      <input type="hidden" name="r" value="catalogos">
      <input type="hidden" name="t" value="<?= e($key) ?>">
      <input class="input" name="q" placeholder="Buscar..." value="<?= e($q) ?>" style="min-width:240px;max-width:360px;">
      <button class="btn btn-ico btn-outline" type="submit" title="Buscar">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
        </svg>
      </button>
      <?php if ($q !== ''): ?>
        <a class="btn btn-ico btn-outline" href="index.php?r=catalogos&t=<?= e($key) ?>" title="Limpiar búsqueda">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
        </a>
      <?php endif; ?>
    </form>

    <button class="btn btn-primary" type="button" data-cat-add title="Agregar nuevo">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14.5a.5.5 0 0 1 0 1H8.5V14.5a.5.5 0 0 1-1 0V8.5H1.5a.5.5 0 0 1 0-1H7.5V1.5A.5.5 0 0 1 8 1z"/>
      </svg>
      Agregar
    </button>
  </div>

  <div class="table-wrap" style="margin-top:12px;">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <?php foreach (array_keys($cat['fields']) as $f): ?>
            <th><?= e($f) ?></th>
          <?php endforeach; ?>
          <th>Activo</th>
          <th style="width:110px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$items): ?>
          <tr><td colspan="99" style="padding:12px;color:var(--muted);">Sin registros.</td></tr>
        <?php endif; ?>

        <?php foreach ($items as $row): ?>
          <?php
            $rowObj = ['id' => (int)$row[$cat['pk']], 'Activo' => (int)($row['Activo'] ?? 1)];
            foreach (array_keys($cat['fields']) as $f) {
              $v = (string)($row[$f] ?? '');
              if (($cat['fields'][$f]['type'] ?? '') === 'time' && strlen($v) >= 5) $v = substr($v, 0, 5);
              $rowObj[$f] = $v;
            }
          ?>
          <tr data-row-id="<?= e((string)$rowObj['id']) ?>">
            <td data-col="id"><?= e((string)$rowObj['id']) ?></td>

            <?php foreach (array_keys($cat['fields']) as $f): ?>
              <td data-col="<?= e($f) ?>"><?= e((string)($rowObj[$f] ?? '')) ?></td>
            <?php endforeach; ?>

            <td data-col="Activo"><?= ((int)$rowObj['Activo'] === 1) ? 'Sí' : 'No' ?></td>

            <td>
              <div class="table-actions">
                <a href="#" class="btn btn-ico btn-outline" title="Editar"
                   data-cat-edit="1"
                   data-row='<?= json_attr($rowObj) ?>'>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10z"/>
                    <path d="M11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5z"/>
                  </svg>
                </a>

                <form class="js-del-form" method="post" action="index.php?r=catalogos_post" style="display:inline;">
                  <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="t" value="<?= e($key) ?>">
                  <input type="hidden" name="delete_id" value="<?= e((string)$rowObj['id']) ?>">
                  <button class="btn btn-ico btn-danger" type="submit" title="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M5.5 5.5A.5.5 0 0 1 6 6v7a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0V6zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V6z"/>
                      <path d="M14.5 3a1 1 0 0 1-1 1H13l-1 11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2L3 4h-.5a1 1 0 0 1 0-2H5.5l1-1h3l1 1H14.5a1 1 0 0 1 1 1z"/>
                    </svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:10px;font-size:12px;color:var(--muted);">
    Mostrando hasta 500 registros. (En móvil: desplazamiento horizontal dentro de la tabla)
  </div>
</div>

<!-- MODAL FORM -->
<div class="modal" id="catModal" aria-hidden="true" data-cat-title="<?= e($cat['title']) ?>">
  <div class="modal-overlay" data-modal-overlay></div>

  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="catModalTitle">
    <div class="modal-head">
      <div>
        <div class="modal-title" id="catModalTitle" data-modal-title><?= e($cat['title']) ?> · Nuevo</div>
        <div class="modal-sub" data-modal-subtitle>Complete los datos y guarde.</div>
      </div>

      <button class="btn-icon" type="button" data-modal-close title="Cerrar">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </svg>
      </button>
    </div>

    <div class="modal-body">
      <form method="post" action="index.php?r=catalogos_post">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="t" value="<?= e($key) ?>">
        <input type="hidden" name="id" value="">

        <?php foreach ($cat['fields'] as $field => $meta): ?>
          <label class="label"><?= e($field) ?></label>

          <?php if (($meta['type'] ?? '') === 'time'): ?>
            <input class="input" type="time" name="<?= e($field) ?>" data-field-name="<?= e($field) ?>">
          <?php else: ?>
            <input class="input" type="text"
                   name="<?= e($field) ?>"
                   data-field-name="<?= e($field) ?>"
                   data-uppercase="1"
                   <?= !empty($meta['required']) ? 'required' : '' ?>>
          <?php endif; ?>
        <?php endforeach; ?>

        <label class="label">Activo</label>
        <select class="input" name="Activo">
          <option value="1" selected>Sí</option>
          <option value="0">No</option>
        </select>

        <div class="modal-actions">
          <button class="btn btn-outline" type="button" data-modal-cancel>Cancelar</button>
          <button class="btn btn-primary" type="submit" title="Guardar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8.5 1h-6A1.5 1.5 0 0 0 1 2.5v11A1.5 1.5 0 0 0 2.5 15h11A1.5 1.5 0 0 0 15 13.5V5.707a1 1 0 0 0-.293-.707l-3.707-3.707A1 1 0 0 0 10.293 1H9.5v3.5a1 1 0 0 1-1 1h-3a1 1 0 0 1-1-1V1z"/>
              <path d="M5 1v3h3V1H5z"/>
            </svg>
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CONFIRM DELETE MODAL -->
<div class="modal" id="confirmModal" aria-hidden="true">
  <div class="modal-overlay" data-confirm-overlay></div>

  <div class="modal-dialog sm" role="dialog" aria-modal="true">
    <div class="modal-head">
      <div>
        <div class="modal-title" data-confirm-title>Confirmar</div>
        <div class="modal-sub" style="max-width:380px;" data-confirm-msg>¿Está seguro?</div>
      </div>

      <button class="btn-icon" type="button" data-confirm-no title="Cerrar">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </svg>
      </button>
    </div>

    <div class="modal-body">
      <div style="color:var(--muted);font-size:13px;">
        Esta acción no se puede deshacer.
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn btn-outline" type="button" data-confirm-no>Cancelar</button>
      <button class="btn btn-danger" type="button" data-confirm-yes>Eliminar</button>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
