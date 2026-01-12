<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../core/ImportReloj.php';

require_login();

if (!csrf_verify($_POST['_csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Token CSRF inválido.';
  redirect('index.php?r=import_reloj');
}

if (empty($_FILES['csv_file']) || ($_FILES['csv_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
  $_SESSION['flash_error'] = 'No se recibió ningún archivo CSV.';
  redirect('index.php?r=import_reloj');
}

$tmp  = (string)$_FILES['csv_file']['tmp_name'];
$orig = (string)($_FILES['csv_file']['name'] ?? 'import.csv');

try {
  $res = import_reloj_csv($tmp, $orig);

  $_SESSION['flash_ok'] =
    "Importación OK. Insertados: {$res['inserted']} · " .
    "Duplicados/Omitidos: {$res['skipped']} · " .
    "Filas con error: {$res['errors']}";

} catch (Throwable $e) {
  $_SESSION['flash_error'] = 'Error al importar: ' . $e->getMessage();
}

redirect('index.php?r=import_reloj');
