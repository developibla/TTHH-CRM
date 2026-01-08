<?php
declare(strict_types=1);

final class Router
{
  public static function dispatch(): void
  {
    $r = (string)($_GET['r'] ?? 'home');

    // Rutas públicas (sin login)
    $public = ['login', 'login_post'];

    if (!in_array($r, $public, true)) {
      require_login();
    }

    switch ($r) {

      // =========================
      // AUTH
      // =========================
      case 'login':
        View::render('home/login');
        return;

      case 'login_post':
        // Si tu Auth.php define una función para procesar login, la usamos.
        // (sin romper tu estructura actual)
        if (function_exists('auth_login_post')) {
          auth_login_post();
          return;
        }
        // fallback (si lo manejas como view)
        View::render('home/login_post');
        return;

      case 'logout':
        if (function_exists('auth_logout')) {
          auth_logout();
          return;
        }
        // fallback simple
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
        return;

      // =========================
      // HOME
      // =========================
      case 'home':
        // Si tenés dashboard luego, acá lo cambiamos.
        redirect('index.php?r=catalogos&t=cargo');
        return;

      // =========================
      // MANTENIMIENTO
      // =========================
      case 'empresa':
        // Si existe el view, lo renderiza.
        View::render('mantenimiento/empresa');
        return;

      case 'catalogos':
        // Compatibilidad: si tu archivo es catalogo.php (singular) o catalogos.php (plural)
        $base = __DIR__ . '/../../views/mantenimiento/';
        if (is_file($base . 'catalogo.php')) {
          View::render('mantenimiento/catalogo');
          return;
        }
        View::render('mantenimiento/catalogos');
        return;

      case 'catalogos_post':
        // Si tu Catalogos.php define una función que procesa alta/edición/baja, la usamos.
        // Esto es lo que probablemente ya te está funcionando.
        if (function_exists('catalogos_post')) {
          catalogos_post();
          return;
        }
        if (function_exists('catalogo_post')) {
          catalogo_post();
          return;
        }

        // fallback (si lo manejas como view)
        $base = __DIR__ . '/../../views/mantenimiento/';
        if (is_file($base . 'catalogos_post.php')) {
          View::render('mantenimiento/catalogos_post');
          return;
        }
        // si no existe, volvemos a la pantalla principal de catálogos
        redirect('index.php?r=catalogos&t=' . urlencode((string)($_POST['t'] ?? 'cargo')));
        return;

      // =========================
      // LEGAJOS
      // =========================
      case 'colaboradores':
        View::render('legajos/colaboradores');
        return;

      case 'contratos':
        View::render('legajos/contratos');
        return;

      // =========================
      // MOVIMIENTOS
      // =========================
      case 'vacaciones':
        View::render('movimientos/vacaciones');
        return;

      case 'suspensiones':
        View::render('movimientos/suspensiones');
        return;

      case 'mov_conceptos':
        View::render('movimientos/mov_conceptos');
        return;

      // =========================
      // REPORTES
      // =========================
      case 'reportes':
        View::render('reportes/reportes');
        return;

      case 'recibos':
        View::render('reportes/recibos');
        return;

      case 'planilla_ips':
        View::render('reportes/planilla_ips');
        return;

      case 'planilla_mtess':
        View::render('reportes/planilla_mtess');
        return;

      // =========================
      // 404
      // =========================
      default:
        http_response_code(404);
        // si no tenés not_found.php, no pasa nada: mostramos un mensaje simple
        $nf = __DIR__ . '/../../views/home/not_found.php';
        if (is_file($nf)) {
          View::render('home/not_found', ['route' => $r]);
        } else {
          echo "<h2 style='font-family:Arial'>Ruta no encontrada: " . htmlspecialchars($r) . "</h2>";
        }
        return;
    }
  }
}
