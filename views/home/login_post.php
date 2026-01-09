<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/core/Helpers.php';
require_once __DIR__ . '/../../app/core/Auth.php';

/* ===============================
   CSRF
   =============================== */
if (!csrf_verify($_POST['_csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Sesión inválida. Intente nuevamente.';
  redirect('index.php?r=login');
}

/* ===============================
   INPUT
   =============================== */
$usuario = trim((string)($_POST['usuario'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if ($usuario === '' || $password === '') {
  $_SESSION['flash_error'] = 'Debe ingresar usuario y contraseña.';
  redirect('index.php?r=login');
}

/* ===============================
   LOGIN
   =============================== */
if (!auth_login($usuario, $password)) {
  $_SESSION['flash_error'] = 'Usuario o contraseña incorrectos.';
  redirect('index.php?r=login');
}

/* ===============================
   OK
   =============================== */
redirect('index.php?r=home');
