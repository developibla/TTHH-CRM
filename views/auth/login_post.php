<?php
declare(strict_types=1);

csrf_validate();

$cfg = require __DIR__ . '/../../app/config/config.php';
$maxAttempts = (int)$cfg['security']['max_attempts'];
$lockMinutes = (int)$cfg['security']['lock_minutes'];

$usuario = trim((string)($_POST['usuario'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($usuario === '' || $password === '') {
  $_SESSION['flash_error'] = 'Completa usuario y contraseña.';
  redirect('login');
}

$stmt = db()->prepare("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1");
$stmt->execute([$usuario]);
$u = $stmt->fetch();

$now = new DateTimeImmutable('now');

if (!$u) {
  db()->prepare("INSERT INTO login_intentos (usuario, ip, exitoso) VALUES (?, ?, 0)")
      ->execute([$usuario, $ip]);
  $_SESSION['flash_error'] = 'Credenciales inválidas.';
  redirect('login');
}

if ((int)$u['activo'] !== 1) {
  $_SESSION['flash_error'] = 'Usuario inactivo. Contacta con el administrador.';
  redirect('login');
}

if (!empty($u['bloqueado_hasta'])) {
  $bh = new DateTimeImmutable($u['bloqueado_hasta']);
  if ($bh > $now) {
    $_SESSION['flash_error'] = 'Usuario bloqueado temporalmente. Intenta más tarde.';
    redirect('login');
  }
}

$ok = password_verify($password, (string)$u['pass_hash']);

if (!$ok) {
  $intentos = (int)$u['intentos_fallidos'] + 1;
  $bloqueadoHasta = null;

  if ($intentos >= $maxAttempts) {
    $bloqueadoHasta = $now->modify("+{$lockMinutes} minutes")->format('Y-m-d H:i:s');
    $intentos = 0; // reiniciamos contador al aplicar bloqueo
  }

  db()->prepare("UPDATE usuarios SET intentos_fallidos=?, bloqueado_hasta=? WHERE id=?")
      ->execute([$intentos, $bloqueadoHasta, (int)$u['id']]);

  db()->prepare("INSERT INTO login_intentos (usuario, ip, exitoso) VALUES (?, ?, 0)")
      ->execute([$usuario, $ip]);

  $_SESSION['flash_error'] = 'Credenciales inválidas.';
  redirect('login');
}

// Login OK: reset intentos
db()->prepare("UPDATE usuarios SET intentos_fallidos=0, bloqueado_hasta=NULL WHERE id=?")
    ->execute([(int)$u['id']]);

db()->prepare("INSERT INTO login_intentos (usuario, ip, exitoso) VALUES (?, ?, 1)")
    ->execute([$usuario, $ip]);

$_SESSION['user'] = [
  'id' => (int)$u['id'],
  'usuario' => (string)$u['usuario'],
  'nombre' => (string)$u['nombre'],
  'rol' => (string)$u['rol'],
];

redirect('home');
