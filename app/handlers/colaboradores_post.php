<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../core/Colaboradores.php';

if (!csrf_verify($_POST['_csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Token CSRF inválido.';
  redirect('index.php?r=colaboradores');
}

/**
 * Subida de imágenes (Selfie / CI frente / CI atrás)
 * Devuelve path relativo para guardar en DB, o null si no se subió.
 */
function upload_colab_image(string $field, int $colabId): ?string
{
  if (empty($_FILES[$field]) || !is_array($_FILES[$field])) return null;
  if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
  if (($_FILES[$field]['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    throw new RuntimeException("Error subiendo archivo ($field).");
  }

  $tmp = (string)$_FILES[$field]['tmp_name'];
  $size = (int)($_FILES[$field]['size'] ?? 0);

  if ($size <= 0) return null;
  if ($size > 5 * 1024 * 1024) { // 5MB
    throw new RuntimeException("El archivo ($field) supera 5MB.");
  }

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = (string)$finfo->file($tmp);

  $ext = match ($mime) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    default => '',
  };

  if ($ext === '') {
    throw new RuntimeException("Formato no permitido en ($field). Use JPG/PNG/WEBP.");
  }

  $dirFs = __DIR__ . '/../../public/uploads/colaboradores';
  if (!is_dir($dirFs)) {
    if (!mkdir($dirFs, 0775, true) && !is_dir($dirFs)) {
      throw new RuntimeException('No se pudo crear carpeta de uploads.');
    }
  }

  $baseName = $field . '_' . $colabId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
  $destFs = $dirFs . '/' . $baseName;

  if (!move_uploaded_file($tmp, $destFs)) {
    throw new RuntimeException("No se pudo mover el archivo ($field).");
  }

  // path relativo (para servir desde web)
  return 'public/uploads/colaboradores/' . $baseName;
}

function only_digits(string $v): string {
  return preg_replace('/[^\d]/', '', $v) ?? '';
}

$id = isset($_POST['ColaboradorId']) ? (int)$_POST['ColaboradorId'] : 0;

// eliminar
if (!empty($_POST['delete_id'])) {
  $delId = (int)$_POST['delete_id'];
  try {
    colaborador_delete($delId);
    $_SESSION['flash_ok'] = 'Registro eliminado.';
  } catch (Throwable $e) {
    $_SESSION['flash_error'] = 'No se pudo eliminar: ' . $e->getMessage();
  }
  redirect('index.php?r=colaboradores');
}

// data
$salarioBase = only_digits((string)($_POST['SalarioBase'] ?? ''));
$plusCargo   = only_digits((string)($_POST['PlusCargo'] ?? ''));

$data = [
  'Legajo' => trim((string)($_POST['Legajo'] ?? '')),
  'Nombres' => trim((string)($_POST['Nombres'] ?? '')),
  'Apellidos' => trim((string)($_POST['Apellidos'] ?? '')),

  'TipoDocumentoId' => (string)($_POST['TipoDocumentoId'] ?? ''),
  'NroDocumento' => trim((string)($_POST['NroDocumento'] ?? '')),
  'CodigoReloj' => trim((string)($_POST['CodigoReloj'] ?? '')), // ✅ NUEVO
  'RUC' => trim((string)($_POST['RUC'] ?? '')),

  'EstadoCivilId' => (string)($_POST['EstadoCivilId'] ?? ''),
  'FechaNacimiento' => (string)($_POST['FechaNacimiento'] ?? ''),
  'GrupoSanguineo' => trim((string)($_POST['GrupoSanguineo'] ?? '')),
  'VencimientoCI' => (string)($_POST['VencimientoCI'] ?? ''),

  'Email' => trim((string)($_POST['Email'] ?? '')),
  'Telefono' => trim((string)($_POST['Telefono'] ?? '')),
  'TelefonoParticular' => trim((string)($_POST['TelefonoParticular'] ?? '')),
  'Direccion' => trim((string)($_POST['Direccion'] ?? '')),

  'PaisId' => (string)($_POST['PaisId'] ?? ''),
  'DptoId' => (string)($_POST['DptoId'] ?? ''),
  'DistritoId' => (string)($_POST['DistritoId'] ?? ''),
  'LocalidadId' => (string)($_POST['LocalidadId'] ?? ''),

  'CargoId' => (string)($_POST['CargoId'] ?? ''),
  'AreaId' => (string)($_POST['AreaId'] ?? ''),
  'SectorId' => (string)($_POST['SectorId'] ?? ''),
  'TurnoId' => (string)($_POST['TurnoId'] ?? ''),
  'TipoId' => (string)($_POST['TipoId'] ?? ''),

  'FormaPagoId' => (string)($_POST['FormaPagoId'] ?? ''),
  'FechaIngreso' => (string)($_POST['FechaIngreso'] ?? ''),
  'FechaEgreso' => (string)($_POST['FechaEgreso'] ?? ''),

  'SalarioBase' => $salarioBase === '' ? '' : $salarioBase, // ✅ limpio miles
  'PlusCargo' => $plusCargo === '' ? '' : $plusCargo,       // ✅ limpio miles
  'NroAseguradoIPS' => trim((string)($_POST['NroAseguradoIPS'] ?? '')),

  'BonificacionFamiliar' => (string)($_POST['BonificacionFamiliar'] ?? '0'),
  'Aguinaldo' => (string)($_POST['Aguinaldo'] ?? '1'),

  'Observacion' => trim((string)($_POST['Observacion'] ?? '')),

  'Activo' => (string)($_POST['Activo'] ?? '1'),
];

if ($data['Nombres'] === '' || $data['Apellidos'] === '') {
  $_SESSION['flash_error'] = 'Nombres y Apellidos son obligatorios.';
  redirect('index.php?r=colaboradores');
}

try {
  // guardamos primero para obtener ID (necesario para nombre de archivos)
  $savedId = colaborador_save($data, $id > 0 ? $id : null);

  // uploads opcionales
  $updates = [];

  $p1 = upload_colab_image('FotoSelfie', $savedId);
  if ($p1) $updates['FotoSelfiePath'] = $p1;

  $p2 = upload_colab_image('FotoCIFrente', $savedId);
  if ($p2) $updates['FotoCIFrentePath'] = $p2;

  $p3 = upload_colab_image('FotoCIAtras', $savedId);
  if ($p3) $updates['FotoCIAtrasPath'] = $p3;

  if ($updates) {
    $sets = [];
    $params = [];
    foreach ($updates as $k => $v) {
      $sets[] = "`$k` = ?";
      $params[] = $v;
    }
    $params[] = $savedId;
    DB::exec("UPDATE colaboradores SET " . implode(', ', $sets) . " WHERE ColaboradorId = ?", $params);
  }

  $_SESSION['flash_ok'] = ($id > 0) ? 'Colaborador actualizado.' : 'Colaborador agregado.';
} catch (Throwable $e) {
  // mensaje amigable si el CódigoReloj es único y se repite
  $msg = $e->getMessage();
  if (stripos($msg, 'uk_colab_codigo_reloj') !== false || stripos($msg, 'Duplicate entry') !== false) {
    $_SESSION['flash_error'] = 'El Código Reloj ya está asignado a otro colaborador.';
  } else {
    $_SESSION['flash_error'] = 'Error al guardar: ' . $msg;
  }
}

redirect('index.php?r=colaboradores');
