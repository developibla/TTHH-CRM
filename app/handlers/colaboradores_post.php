<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../core/Colaboradores.php';

if (!csrf_verify($_POST['_csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Token CSRF inválido.';
  redirect('index.php?r=colaboradores');
}

$id = isset($_POST['ColaboradorId']) ? (int)$_POST['ColaboradorId'] : 0;

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

// Guardar
$data = [
  'Legajo' => trim((string)($_POST['Legajo'] ?? '')),
  'Nombres' => trim((string)($_POST['Nombres'] ?? '')),
  'Apellidos' => trim((string)($_POST['Apellidos'] ?? '')),

  'TipoDocumentoId' => (string)($_POST['TipoDocumentoId'] ?? ''),
  'NroDocumento' => trim((string)($_POST['NroDocumento'] ?? '')),

  'EstadoCivilId' => (string)($_POST['EstadoCivilId'] ?? ''),
  'FechaNacimiento' => (string)($_POST['FechaNacimiento'] ?? ''),

  'Email' => trim((string)($_POST['Email'] ?? '')),
  'Telefono' => trim((string)($_POST['Telefono'] ?? '')),
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

  'Activo' => (string)($_POST['Activo'] ?? '1'),
];

// Validación mínima
if ($data['Nombres'] === '' || $data['Apellidos'] === '') {
  $_SESSION['flash_error'] = 'Nombres y Apellidos son obligatorios.';
  redirect('index.php?r=colaboradores');
}

try {
  colaborador_save($data, $id > 0 ? $id : null);
  $_SESSION['flash_ok'] = $id > 0 ? 'Colaborador actualizado.' : 'Colaborador agregado.';
} catch (Throwable $e) {
  $_SESSION['flash_error'] = 'Error al guardar: ' . $e->getMessage();
}

redirect('index.php?r=colaboradores');
