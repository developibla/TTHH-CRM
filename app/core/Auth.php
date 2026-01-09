<?php
declare(strict_types=1);

/**
 * AUTH (funcional)
 * Tabla: usuarios(id, usuario, nombre, email, pass_hash, rol, activo, intentos_fallidos, bloqueado_hasta, ...)
 */

function auth_login(string $usuario, string $clave): bool
{
  $usuario = trim($usuario);

  // buscar usuario
  $u = DB::fetchOne("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1", [$usuario]);
  if (!$u) {
    // guardamos para UX
    $_SESSION['login_last_user'] = $usuario;
    return false;
  }

  // activo?
  if ((int)$u['activo'] !== 1) {
    $_SESSION['login_last_user'] = $usuario;
    $_SESSION['login_error'] = 'Usuario inactivo. Contacte al administrador.';
    return false;
  }

  // bloqueado?
  if (!empty($u['bloqueado_hasta'])) {
    $bh = strtotime((string)$u['bloqueado_hasta']);
    if ($bh !== false && $bh > time()) {
      $_SESSION['login_last_user'] = $usuario;
      $_SESSION['login_error'] = 'Usuario bloqueado temporalmente. Intente más tarde.';
      return false;
    }
  }

  // validar password
  $ok = password_verify($clave, (string)$u['pass_hash']);

  if (!$ok) {
    // incrementar intentos
    $fails = (int)($u['intentos_fallidos'] ?? 0) + 1;

    // política simple: al 5° intento bloquear 10 min
    $bloqHasta = null;
    if ($fails >= 5) {
      $bloqHasta = date('Y-m-d H:i:s', time() + 10 * 60);
    }

    DB::exec(
      "UPDATE usuarios
       SET intentos_fallidos = ?, bloqueado_hasta = ?
       WHERE id = ?",
      [$fails, $bloqHasta, (int)$u['id']]
    );

    $_SESSION['login_last_user'] = $usuario;
    $_SESSION['login_error'] = 'Credenciales inválidas.';
    return false;
  }

  // reset intentos/bloqueo
  DB::exec("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?", [(int)$u['id']]);

  // guardar sesión (lo mínimo)
  $_SESSION['user'] = [
    'id' => (int)$u['id'],
    'usuario' => (string)$u['usuario'],
    'nombre' => (string)$u['nombre'],
    'rol' => (string)$u['rol'],
  ];

  // limpiar mensajes
  unset($_SESSION['login_error'], $_SESSION['login_info']);

  return true;
}

/**
 * Procesa POST del login (router llama a esto si existe)
 */
function auth_login_post(): void
{
  // CSRF
  if (!csrf_verify($_POST['_csrf'] ?? '')) {
    $_SESSION['login_error'] = 'Sesión inválida. Actualice la página e intente de nuevo.';
    redirect('index.php?r=login');
  }

  $usuario = trim((string)($_POST['usuario'] ?? ''));
  // ✅ OJO: tu form envía "clave"
  $clave   = (string)($_POST['clave'] ?? '');

  $_SESSION['login_last_user'] = $usuario;

  if ($usuario === '' || $clave === '') {
    $_SESSION['login_error'] = 'Debe ingresar usuario y clave.';
    redirect('index.php?r=login');
  }

  if (!auth_login($usuario, $clave)) {
    // auth_login ya carga login_error cuando corresponde
    if (empty($_SESSION['login_error'])) {
      $_SESSION['login_error'] = 'Credenciales inválidas.';
    }
    redirect('index.php?r=login');
  }

  // OK
  redirect('index.php?r=home');
}

function auth_logout(): void
{
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
  }
  session_destroy();
  redirect('index.php?r=login');
}
