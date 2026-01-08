<?php
declare(strict_types=1);

final class Router {
  public static function dispatch(): void {
    $r = (string)($_GET['r'] ?? 'home');

    // públicas
    if ($r === 'login') { View::render('home/login'); return; }
    if ($r === 'login_post') { require __DIR__ . '/../../views/home/login_post.php'; return; }

    // privadas
    if ($r === 'logout') { require_login(); Auth::logout(); redirect('index.php?r=login'); }

    require_login();

    switch ($r) {
      case 'home':
        View::render('home/index');
        break;

      // MANTENIMIENTO
      case 'empresa':
        View::render('mantenimiento/empresa'); // lo hacemos después
        break;
      case 'catalogos':
        View::render('mantenimiento/catalogos'); // lo que ya tenés, lo movemos luego
        break;

      // LEGAJOS
      case 'colaboradores':
        View::render('legajos/colaboradores'); // próximo
        break;

      // MOVIMIENTOS
      case 'vacaciones':
        View::render('movimientos/vacaciones'); // próximo
        break;

      // REPORTES
      case 'rpt_colaboradores':
        View::render('reportes/rpt_colaboradores'); // próximo
        break;

      default:
        http_response_code(404);
        echo "Ruta no encontrada: " . e($r);
    }
  }
}
