<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../core/ImportReloj.php';

require_login();

if (!csrf_verify($_POST['_csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Token CSRF inválido.';
  redirect('index.php?r=import_reloj');
}

try {
  $updated = reconciliar_reloj_movimientos();
  $_SESSION['flash_ok'] = "Reconciliación completada. Asociados: {$updated}.";
} catch (Throwable $e) {
  $_SESSION['flash_error'] = 'Error al reconciliar: ' . $e->getMessage();
}

redirect('index.php?r=import_reloj');
/**
 * Importar archivo CSV de reloj de marcaciones.
 *
 * Formato esperado:
 * CodigoReloj,FechaHora,DispositivoNro,TipoRegistro
 *
 * Retorna array con conteo de insertados, omitidos y errores.
 *
 * @param string $filePath Ruta al archivo CSV.
 * @param string $sourceName Nombre del archivo original (para registro).
 * @return array{inserted:int, skipped:int, errors:int}
 * @throws Throwable
 */