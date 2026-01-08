<?php
declare(strict_types=1);

// === SCRIPT TEMPORAL DE EMERGENCIA ===
// Abre en el navegador:
// http://localhost/tthh/tools/reset_admin.php?key=RESET-ADMIN-2026
//
// Luego BORRA este archivo por seguridad.

$key = $_GET['key'] ?? '';
if ($key !== 'RESET-ADMIN-2026') {
  http_response_code(403);
  echo "Acceso denegado.";
  exit;
}

require __DIR__ . '/../app/config/bootstrap.php';

$usuario = 'admin';
$nombre  = 'Administrador';
$email   = 'admin@local';
$rol     = 'ADMIN';
$clave   = 'Admin@1234';

$hash = password_hash($clave, PASSWORD_DEFAULT);

$pdo = db();
$pdo->beginTransaction();

try {
  // Si existe, actualiza. Si no existe, crea.
  $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario=? LIMIT 1");
  $stmt->execute([$usuario]);
  $row = $stmt->fetch();

  if ($row) {
    $upd = $pdo->prepare("
      UPDATE usuarios
      SET nombre=?, email=?, pass_hash=?, rol=?, activo=1, intentos_fallidos=0, bloqueado_hasta=NULL
      WHERE usuario=?
    ");
    $upd->execute([$nombre, $email, $hash, $rol, $usuario]);
    $msg = "✅ Admin actualizado correctamente.";
  } else {
    $ins = $pdo->prepare("
      INSERT INTO usuarios (usuario, nombre, email, pass_hash, rol, activo)
      VALUES (?, ?, ?, ?, ?, 1)
    ");
    $ins->execute([$usuario, $nombre, $email, $hash, $rol]);
    $msg = "✅ Admin creado correctamente.";
  }

  $pdo->commit();

  echo "<h3>{$msg}</h3>";
  echo "<p><b>Usuario:</b> admin</p>";
  echo "<p><b>Clave:</b> Admin@1234</p>";
  echo "<p style='color:#b91c1c;'><b>IMPORTANTE:</b> Borra este archivo <code>tools/reset_admin.php</code> ahora mismo.</p>";

} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(500);
  echo "Error: " . htmlspecialchars($e->getMessage());
}
