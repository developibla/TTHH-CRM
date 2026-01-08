<?php
declare(strict_types=1);

require_login();

$type = (string)($_GET['t'] ?? 'departamento');

$cfg = ImportGeo::config();
if (!isset($cfg[$type])) $type = 'departamento';
$cat = ImportGeo::get($type);

$ok = $_SESSION['flash_ok'] ?? '';
$err = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

require __DIR__ . '/../partials/layout_top.php';
?>

<div class="card">
  <h2 style="margin:0 0 6px 0;">Importación masiva (CSV)</h2>
  <div style="color:var(--muted);font-size:13px;">
    Importar códigos oficiales para <b><?= e($cat['title']) ?></b>.
    Formato sugerido: <code>ID;DESCRIPCION;ACTIVO</code>
  </div>

  <?php if ($ok): ?><div class="alert alert-ok" style="margin-top:12px;"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error" style="margin-top:12px;"><?= e($err) ?></div><?php endif; ?>

  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;">
    <?php foreach ($cfg as $k => $c): ?>
      <a class="btn btn-sm <?= $k===$type ? 'btn-primary' : 'btn-outline' ?>"
         href="index.php?r=import_geo&t=<?= e($k) ?>">
        <?= e($c['title']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="grid" style="margin-top:14px;">
    <div class="card" style="margin:0;">
      <h3 style="margin:0 0 10px 0;">Subir archivo CSV</h3>

      <!-- ✅ IMPORTANTE: enctype multipart -->
      <form method="post"
            action="index.php?r=import_geo_post"
            enctype="multipart/form-data">

        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="t" value="<?= e($type) ?>">

        <label class="label">Archivo CSV</label>
        <!-- ✅ IMPORTANTE: name="csv_file" (igual al handler) -->
        <input class="input"
               type="file"
               name="csv_file"
               accept=".csv,text/csv"
               required>

        <div style="margin-top:10px;color:var(--muted);font-size:12px;">
          Ejemplo:
          <br><code>ID;DESCRIPCION;ACTIVO</code>
          <br><code>1;ASUNCION;1</code>
          <br><code>2;CENTRAL;1</code>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:14px;">
          <button class="btn btn-primary" type="submit">
            Importar CSV
          </button>
        </div>
      </form>
    </div>

    <div class="card" style="margin:0;">
      <h3 style="margin:0 0 10px 0;">Notas</h3>
      <ul style="margin:0;padding-left:18px;color:var(--muted);font-size:13px;line-height:1.55;">
        <li>Los IDs deben ser numéricos (según tu planilla oficial).</li>
        <li>La descripción se guardará en UPPERCASE.</li>
        <li>Si el ID ya existe, se actualiza (re-importación segura).</li>
      </ul>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/layout_bottom.php'; ?>
