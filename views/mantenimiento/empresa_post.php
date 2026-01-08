<?php
declare(strict_types=1);

require_login();
csrf_check($_POST['_csrf'] ?? null);

$id = (int)($_POST['id'] ?? 1);

$empresa = trim((string)($_POST['empresa'] ?? ''));
$ruc = trim((string)($_POST['ruc'] ?? ''));
$telefono = trim((string)($_POST['telefono'] ?? ''));
$direccion = trim((string)($_POST['direccion'] ?? ''));
$capital = trim((string)($_POST['capital'] ?? ''));
$ips = trim((string)($_POST['numero_patronal_ips'] ?? ''));
$cant = (int)($_POST['cantidad_empleados'] ?? 0);

if ($empresa === '') {
  $_SESSION['flash_error'] = 'El campo Empresa es obligatorio.';
  redirect('index.php?r=empresa');
}

$logoPath = null;

// Upload logo si viene archivo
if (!empty($_FILES['logo']) && is_array($_FILES['logo']) && ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {

  if (($_FILES['logo']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    $_SESSION['flash_error'] = 'Error al subir el logo.';
    redirect('index.php?r=empresa');
  }

  $tmp = (string)$_FILES['logo']['tmp_name'];
  $name = (string)$_FILES['logo']['name'];

  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  $allowed = ['png','jpg','jpeg','webp'];

  if (!in_array($ext, $allowed, true)) {
    $_SESSION['flash_error'] = 'Formato de logo no permitido. Use PNG/JPG/WEBP.';
    redirect('index.php?r=empresa');
  }

  $dir = __DIR__ . '/../../public/uploads/empresa';
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }

  $filename = 'logo_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
  $dest = $dir . '/' . $filename;

  if (!move_uploaded_file($tmp, $dest)) {
    $_SESSION['flash_error'] = 'No se pudo guardar el archivo del logo.';
    redirect('index.php?r=empresa');
  }

  // Guardamos ruta pública
  $logoPath = 'public/uploads/empresa/' . $filename;
}

// Si ya existe fila con id, actualizamos, si no, insertamos
$exists = DB::fetchOne("SELECT id, logo_path FROM empresa_parametros WHERE id = ? LIMIT 1", [$id]);

if ($exists) {
  // si no subió logo, mantenemos el anterior
  if ($logoPath === null) $logoPath = (string)($exists['logo_path'] ?? '');

  DB::exec(
    "UPDATE empresa_parametros
     SET empresa=?, ruc=?, telefono=?, direccion=?, capital=?, numero_patronal_ips=?, cantidad_empleados=?, logo_path=?
     WHERE id=?",
    [$empresa, $ruc, $telefono, $direccion, $capital, $ips, $cant, $logoPath, $id]
  );
} else {
  // si id no existe, insertamos con ese id
  DB::exec(
    "INSERT INTO empresa_parametros (id, empresa, ruc, telefono, direccion, capital, numero_patronal_ips, cantidad_empleados, logo_path)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
    [$id, $empresa, $ruc, $telefono, $direccion, $capital, $ips, $cant, $logoPath]
  );
}

$_SESSION['flash_ok'] = 'Parámetros guardados correctamente.';
redirect('index.php?r=empresa');
