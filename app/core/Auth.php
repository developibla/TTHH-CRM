<?php
declare(strict_types=1);

final class Auth
{
  /**
   * Intenta login con tu tabla usuarios:
   * - usuario (unique)
   * - pass_hash (password_hash)
   * - activo (1/0)
   * - intentos_fallidos
   * - bloqueado_hasta (datetime)
   * - rol (ADMIN/RRHH/LECTURA)
   */
  public static function attempt(string $usuario, string $clave): bool
  {
    $usuario = trim($usuario);

    $u = DB::fetchOne(
      "SELECT id, usuario, nombre, email, pass_hash, rol, activo, intentos_fallidos, bloqueado_hasta
       FROM usuarios
       WHERE usuario = ?
       LIMIT 1",
      [$usuario]
    );

    // Si no existe el usuario, igual devolvemos false sin dar pistas
    if (!$u) {
      return false;
    }

    // Verificar activo
    if ((int)$u['activo'] !== 1) {
      // Usuario inactivo
      return false;
    }

    // Verificar bloqueo por tiempo
    if (!empty($u['bloqueado_hasta'])) {
      $bh = strtotime((string)$u['bloqueado_hasta']);
      if ($bh !== false && $bh > time()) {
        // Aún bloqueado
        return false;
      }
    }

    $hash = (string)$u['pass_hash'];
    $ok = ($hash !== '') && password_verify($clave, $hash);

    if (!$ok) {
      self::registerFailedAttempt((int)$u['id'], (int)$u['intentos_fallidos']);
      return false;
    }

    // login OK → resetear intentos/bloqueo
    DB::exec(
      "UPDATE usuarios
       SET intentos_fallidos = 0, bloqueado_hasta = NULL
       WHERE id = ?",
      [(int)$u['id']]
    );

    // Guardar sesión
    $_SESSION['user'] = [
      'id' => (int)$u['id'],
      'usuario' => (string)$u['usuario'],
      'nombre' => (string)$u['nombre'],
      'email' => (string)($u['email'] ?? ''),
      'rol' => (string)$u['rol'],
    ];

    session_regenerate_id(true);
    return true;
  }

  private static function registerFailedAttempt(int $userId, int $currentFails): void
  {
    $fails = $currentFails + 1;

    // Política sugerida: 5 intentos → bloqueo 15 minutos
    $max = 5;
    $lockMinutes = 15;

    if ($fails >= $max) {
      DB::exec(
        "UPDATE usuarios
         SET intentos_fallidos = ?, bloqueado_hasta = DATE_ADD(NOW(), INTERVAL ? MINUTE)
         WHERE id = ?",
        [$fails, $lockMinutes, $userId]
      );
    } else {
      DB::exec(
        "UPDATE usuarios
         SET intentos_fallidos = ?
         WHERE id = ?",
        [$fails, $userId]
      );
    }
  }

  public static function logout(): void
  {
    unset($_SESSION['user']);
    session_regenerate_id(true);
  }

  /**
   * Para mostrar info de bloqueo/estado en el login (opcional)
   */
  public static function getUserLoginStatus(string $usuario): array
  {
    $u = DB::fetchOne(
      "SELECT activo, intentos_fallidos, bloqueado_hasta
       FROM usuarios
       WHERE usuario = ?
       LIMIT 1",
      [trim($usuario)]
    );

    if (!$u) return ['exists' => false];

    return [
      'exists' => true,
      'activo' => (int)$u['activo'],
      'intentos_fallidos' => (int)$u['intentos_fallidos'],
      'bloqueado_hasta' => (string)($u['bloqueado_hasta'] ?? ''),
    ];
  }
}
