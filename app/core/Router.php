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
        if (function_exists('auth_login_post')) {
          auth_login_post();
          return;
        }
        View::render('home/login_post');
        return;

      case 'logout':
        if (function_exists('auth_logout')) {
          auth_logout();
          return;
        }
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
        redirect('index.php?r=catalogos&t=cargo');
        return;

      // =========================
      // MANTENIMIENTO
      // =========================
      case 'empresa':
        View::render('mantenimiento/empresa');
        return;

      case 'catalogos':
        $base = __DIR__ . '/../../views/mantenimiento/';
        if (is_file($base . 'catalogo.php')) {
          View::render('mantenimiento/catalogo');
          return;
        }
        View::render('mantenimiento/catalogos');
        return;

      case 'catalogos_post':
        if (function_exists('catalogos_post')) {
          catalogos_post();
          return;
        }
        if (function_exists('catalogo_post')) {
          catalogo_post();
          return;
        }

        $base = __DIR__ . '/../../views/mantenimiento/';
        if (is_file($base . 'catalogos_post.php')) {
          View::render('mantenimiento/catalogos_post');
          return;
        }

        redirect('index.php?r=catalogos&t=' . urlencode((string)($_POST['t'] ?? 'cargo')));
        return;

      // =========================
      // IMPORTACIÓN MASIVA CSV (GEO)
      // =========================
      case 'import_geo':
        View::render('mantenimiento/import_geo');
        return;

      case 'import_geo_post':
        // handler (recomendado) para no mezclar lógica con vistas
        $handler = __DIR__ . '/../handlers/import_geo_post.php';
        if (is_file($handler)) {
          require $handler;
          return;
        }
        // fallback: si no existe handler, volvemos
        $_SESSION['flash_error'] = 'Handler de importación no encontrado (app/handlers/import_geo_post.php).';
        redirect('index.php?r=import_geo&t=' . urlencode((string)($_POST['t'] ?? 'departamento')));
        return;

      // =========================
      // LEGAJOS
      // =========================
      case 'colaboradores':
        View::render('legajos/colaboradores');
        return;
        
        case 'colaboradores_post':
          require __DIR__ . '/../handlers/colaboradores_post.php';
          return;
        
        View::render('legajos/colaboradores_post');
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
