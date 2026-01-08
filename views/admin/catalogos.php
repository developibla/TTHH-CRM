<?php
declare(strict_types=1);

$key = (string)($_GET['t'] ?? 'cargo');
$q = trim((string)($_GET['q'] ?? ''));
$editId = (int)($_GET['edit'] ?? 0);

$cfgAll = catalogos_config();
$cat = catalogo_get($key);

$items = catalogo_list($key, $q);
$editRow = $editId ? catalogo_find($key, $editId) : null;

$ok = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

require __DIR__ . '/../partials/layout_top.php';
?>

<div class="card">
  <h2 style="margin:0 0 6px 0;">Catálogos referenciales</h2>
  <div style="color:var(--muted);font-size:13px;">
    Administrar: Cargos, Áreas, Sectores, Tipos y Turnos.
  </div>

  <?php if ($ok): ?><div class="alert alert-ok"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

  <!-- Tabs catálogos -->
  <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
    <?php foreach ($cfgAll as $k => $c): ?>
      <a class="btn btn-sm <?= $k===$key ? 'btn-primary' : 'btn-outline' ?>"
         href="index.php?r=catalogos&t=<?= e($k) ?>"
         title="<?= e($c['title']) ?>">
        <?= e($c['title']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="grid" style="margin-top:14px;">

    <!-- FORM -->
    <div class="card" style="margin:0;">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
        <h3 style="margin:0;"><?= e($cat['title']) ?> · <?= $editRow ? 'Editar' : 'Nuevo' ?></h3>

        <div style="display:flex;gap:8px;align-items:center;">
          <!-- Nuevo (reset) -->
          <a class="btn btn-ico btn-outline" href="index.php?r=catalogos&t=<?= e($key) ?>" title="Nuevo registro">
            <!-- plus -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14.5a.5.5 0 0 1 0 1H8.5V14.5a.5.5 0 0 1-1 0V8.5H1.5a.5.5 0 0 1 0-1H7.5V1.5A.5.5 0 0 1 8 1z"/>
            </svg>
          </a>
        </div>
      </div>

      <form method="post" action="index.php?r=catalogos_post" style="margin-top:10px;">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="t" value="<?= e($key) ?>">
        <input type="hidden" name="id" value="<?= e((string)($editRow[$cat['pk']] ?? '')) ?>">

        <?php foreach ($cat['fields'] as $field => $meta): ?>
          <label class="label"><?= e($field) ?></label>

          <?php if (($meta['type'] ?? '') === 'time'): ?>
            <input class="input" type="time" name="<?= e($field) ?>"
              value="<?php
                $v = $editRow[$field] ?? '';
                if (is_string($v) && strlen($v) >= 5) $v = substr($v, 0, 5);
                echo e((string)$v);
              ?>">
          <?php else: ?>
            <input class="input" type="text" name="<?= e($field) ?>"
              value="<?= e((string)($editRow[$field] ?? '')) ?>"
              <?= !empty($meta['required']) ? 'required' : '' ?>>
          <?php endif; ?>
        <?php endforeach; ?>

        <label class="label">Activo</label>
        <?php $a = (int)($editRow['Activo'] ?? 1); ?>
        <select class="input" name="Activo">
          <option value="1" <?= $a===1 ? 'selected' : '' ?>>Sí</option>
          <option value="0" <?= $a===0 ? 'selected' : '' ?>>No</option>
        </select>

        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;flex-wrap:wrap;">
          <!-- Guardar / Agregar -->
          <button class="btn btn-ico btn-primary" type="submit" title="<?= $editRow ? 'Guardar cambios' : 'Agregar registro' ?>">
            <!-- save -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8.5 1h-6A1.5 1.5 0 0 0 1 2.5v11A1.5 1.5 0 0 0 2.5 15h11A1.5 1.5 0 0 0 15 13.5V5.707a1 1 0 0 0-.293-.707l-3.707-3.707A1 1 0 0 0 10.293 1H9.5v3.5a1 1 0 0 1-1 1h-3a1 1 0 0 1-1-1V1z"/>
              <path d="M5 1v3h3V1H5z"/>
            </svg>
          </button>

          <!-- Limpiar (Nuevo) -->
          <a class="btn btn-ico btn-outline" href="index.php?r=catalogos&t=<?= e($key) ?>" title="Limpiar formulario">
            <!-- broom -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M15.293 1.293a1 1 0 0 1 0 1.414l-1.586 1.586-1.414-1.414 1.586-1.586a1 1 0 0 1 1.414 0z"/>
              <path d="M12.293 4.293 2.5 14.086a1 1 0 0 1-.707.293H1a1 1 0 0 1-1-1v-.793a1 1 0 0 1 .293-.707L10.086 2.879l2.207 1.414z"/>
            </svg>
          </a>
        </div>

        <?php if ($editRow): ?>
          <div style="margin-top:10px;font-size:12px;color:var(--muted);">
            Editando ID: <b><?= e((string)$editRow[$cat['pk']]) ?></b>
          </div>
        <?php endif; ?>
      </form>
    </div>

    <!-- LISTADO -->
    <div class="card" style="margin:0;">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
        <h3 style="margin:0;"><?= e($cat['title']) ?> · Listado</h3>

        <form method="get" action="index.php" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
          <input type="hidden" name="r" value="catalogos">
          <input type="hidden" name="t" value="<?= e($key) ?>">
          <input class="input" name="q" placeholder="Buscar..." value="<?= e($q) ?>" style="min-width:220px;max-width:320px;">
          <button class="btn btn-ico btn-outline" type="submit" title="Buscar">
            <!-- search -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
            </svg>
          </button>
        </form>
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
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$items): ?>
              <tr><td colspan="99" style="padding:12px;color:var(--muted);">Sin registros.</td></tr>
            <?php endif; ?>

            <?php foreach ($items as $row): ?>
              <tr>
                <td><?= e((string)$row[$cat['pk']]) ?></td>

                <?php foreach (array_keys($cat['fields']) as $f): ?>
                  <td><?= e((string)($row[$f] ?? '')) ?></td>
                <?php endforeach; ?>

                <td><?= ((int)$row['Activo'] === 1) ? 'Sí' : 'No' ?></td>

                <td style="white-space:nowrap;display:flex;gap:8px;">
                  <!-- Editar -->
                  <a class="btn btn-ico btn-outline"
                     href="index.php?r=catalogos&t=<?= e($key) ?>&edit=<?= e((string)$row[$cat['pk']]) ?>"
                     title="Editar">
                    <!-- pencil -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10z"/>
                      <path d="M11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5z"/>
                    </svg>
                  </a>

                  <!-- Eliminar -->
                  <form method="post" action="index.php?r=catalogos_post" style="display:inline;">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="t" value="<?= e($key) ?>">
                    <input type="hidden" name="delete_id" value="<?= e((string)$row[$cat['pk']]) ?>">
                    <button class="btn btn-ico btn-danger"
                            type="submit"
                            title="Eliminar"
                            onclick="return confirm('¿Eliminar este registro?');">
                      <!-- trash -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v7a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0V6zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0V6z"/>
                        <path d="M14.5 3a1 1 0 0 1-1 1H13l-1 11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2L3 4h-.5a1 1 0 0 1 0-2H5.5l1-1h3l1 1H14.5a1 1 0 0 1 1 1z"/>
                      </svg>
                    </button>
                  </form>
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

  </div>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
