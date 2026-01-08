<?php
declare(strict_types=1);

csrf_check($_POST['_csrf'] ?? null);

$usuario = trim((string)($_POST['usuario'] ?? ''));
$clave = (string)($_POST['clave'] ?? '');

if ($usuario === '' || $clave === '') {
  $_SESSION['login_error'] = 'Complete usuario y clave.';
  redirect('index.php?r=login');
}

if (!Auth::attempt($usuario, $clave)) {
  $_SESSION['login_error'] = 'Credenciales inválidas.';
  redirect('index.php?r=login');
}

redirect('index.php?r=home');
