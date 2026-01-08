<?php
declare(strict_types=1);

final class Auth {
  public static function attempt(string $usuario, string $clave): bool {
    $u = DB::fetchOne("SELECT * FROM usuarios WHERE Usuario = ? AND Activo = 1 LIMIT 1", [$usuario]);
    if (!$u) return false;

    $hash = (string)($u['ClaveHash'] ?? '');
    if ($hash === '' || !password_verify($clave, $hash)) return false;

    $_SESSION['user'] = [
      'id' => $u['IdUsuario'],
      'usuario' => $u['Usuario'],
      'nombre' => $u['NombreCompleto'] ?? $u['Usuario'],
      'rol' => $u['Rol'] ?? 'USER',
    ];
    return true;
  }

  public static function logout(): void {
    unset($_SESSION['user']);
    session_regenerate_id(true);
  }
}
