<?php
declare(strict_types=1);

csrf_check($_POST['_csrf'] ?? null);

$usuario = trim((string)($_POST['usuario'] ?? ''));
$clave = (string)($_POST['clave'] ?? '');

$_SESSION['login_last_user'] = $usuario;

if ($usuario === '' || $clave === '') {
  $_SESSION['login_error'] = 'Complete usuario y clave.';
  redirect('index.php?r=login');
}

$ok = Auth::attempt($usuario, $clave);

if (!$ok) {
  // Chequear estado para dar mensaje más útil (sin revelar demasiado)
  $st = Auth::getUserLoginStatus($usuario);

  if (!($st['exists'] ?? false)) {
    $_SESSION['login_error'] = 'Credenciales inválidas.';
    redirect('index.php?r=login');
  }

  if ((int)($st['activo'] ?? 0) !== 1) {
    $_SESSION['login_error'] = 'Usuario inactivo. Contacte al administrador.';
    redirect('index.php?r=login');
  }

  $bh = (string)($st['bloqueado_hasta'] ?? '');
  if ($bh !== '') {
    $bhTs = strtotime($bh);
    if ($bhTs !== false && $bhTs > time()) {
      $_SESSION['login_error'] = 'Usuario bloqueado temporalmente. Intente más tarde.';
      redirect('index.php?r=login');
    }
  }

  $fails = (int)($st['intentos_fallidos'] ?? 0);
  if ($fails > 0) {
    $_SESSION['login_error'] = "Credenciales inválidas. Intentos fallidos: {$fails}.";
  } else {
    $_SESSION['login_error'] = 'Credenciales inválidas.';
  }

  redirect('index.php?r=login');
}

redirect('index.php?r=home');
