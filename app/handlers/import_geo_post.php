<?php
declare(strict_types=1);

/* ===============================
   SEGURIDAD
   =============================== */
require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../core/ImportGeo.php';

if (!csrf_verify($_POST['_csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Token CSRF inválido.';
  redirect('index.php?r=import_geo&t=' . urlencode((string)($_POST['t'] ?? '')));
}

/* ===============================
   VALIDACIONES BÁSICAS
   =============================== */
$type = (string)($_POST['t'] ?? '');
if (!in_array($type, ['departamento', 'distrito', 'localidad'], true)) {
  $_SESSION['flash_error'] = 'Tipo de importación inválido.';
  redirect('index.php?r=import_geo');
}

if (
  empty($_FILES['csv_file']) ||
  $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK
) {
  $_SESSION['flash_error'] = 'No se recibió ningún archivo CSV.';
  redirect('index.php?r=import_geo&t=' . urlencode($type));
}

/* ===============================
   PROCESAR CSV
   =============================== */
try {
  $result = import_geo_csv(
    $type,
    $_FILES['csv_file']['tmp_name']
  );

  $_SESSION['flash_ok'] =
    "Importación completada. " .
    "Insertados: {$result['inserted']} · " .
    "Actualizados: {$result['updated']} · " .
    "Omitidos: {$result['skipped']}";

} catch (Throwable $e) {
  $_SESSION['flash_error'] = 'Error al importar: ' . $e->getMessage();
}

redirect('index.php?r=import_geo&t=' . urlencode($type));
