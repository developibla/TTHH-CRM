<?php
declare(strict_types=1);

csrf_validate();

$empresa = trim((string)($_POST['empresa'] ?? ''));
$ruc = trim((string)($_POST['ruc'] ?? ''));
$telefono = trim((string)($_POST['telefono'] ?? ''));
$direccion = trim((string)($_POST['direccion'] ?? ''));
$capital = trim((string)($_POST['capital'] ?? ''));
$numero_patronal_ips = trim((string)($_POST['numero_patronal_ips'] ?? ''));
$cantidad_empleados = (int)($_POST['cantidad_empleados'] ?? 0);

if ($empresa === '') {
  $_SESSION['flash_error'] = 'El campo Empresa es obligatorio.';
  redirect('empresa');
}

$logoPath = null;

// Subida de logo (opcional)
if (!empty($_FILES['logo']) && ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
  $tmp = $_FILES['logo']['tmp_name'];
  $name = (string)($_FILES['logo']['name'] ?? '');
  $type = (string)($_FILES['logo']['type'] ?? '');

  $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg'];
  if (!isset($allowed[$type])) {
    $_SESSION['flash_error'] = 'Logo inválido. Solo PNG o JPG.';
    redirect('empresa');
  }

  $ext = $allowed[$type];
  $safeName = 'logo_empresa_' . date('Ymd_His') . '.' . $ext;

  $destDir = __DIR__ . '/../../public/assets/img';
  if (!is_dir($destDir)) {
    mkdir($destDir, 0775, true);
  }

  $dest = $destDir . '/' . $safeName;
  if (!move_uploaded_file($tmp, $dest)) {
    $_SESSION['flash_error'] = 'No se pudo guardar el logo.';
    redirect('empresa');
  }

  $logoPath = 'public/assets/img/' . $safeName;
}

// Guardar en BD
if ($logoPath) {
  $sql = "UPDATE empresa_parametros
          SET empresa=?, ruc=?, telefono=?, direccion=?, capital=?, numero_patronal_ips=?, cantidad_empleados=?, logo_path=?
          WHERE id=1";
  db()->prepare($sql)->execute([$empresa, $ruc, $telefono, $direccion, $capital, $numero_patronal_ips, $cantidad_empleados, $logoPath]);
} else {
  $sql = "UPDATE empresa_parametros
          SET empresa=?, ruc=?, telefono=?, direccion=?, capital=?, numero_patronal_ips=?, cantidad_empleados=?
          WHERE id=1";
  db()->prepare($sql)->execute([$empresa, $ruc, $telefono, $direccion, $capital, $numero_patronal_ips, $cantidad_empleados]);
}

$_SESSION['flash_ok'] = 'Parámetros guardados correctamente.';
redirect('empresa');
