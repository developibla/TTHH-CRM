<?php
declare(strict_types=1);

require __DIR__ . '/app/config/bootstrap.php';

$route = $_GET['r'] ?? 'home';

switch ($route) {

  case 'login':
    require __DIR__ . '/views/auth/login.php';
    break;

  case 'login_post':
    require __DIR__ . '/views/auth/login_post.php';
    break;

  case 'logout':
    require __DIR__ . '/views/auth/logout.php';
    break;

  case 'home':
    require_login();
    require __DIR__ . '/views/home.php';
    break;

  case 'empresa':
    require_role(['ADMIN']);
    require __DIR__ . '/views/admin/empresa.php';
    break;

  case 'empresa_post':
    require_role(['ADMIN']);
    require __DIR__ . '/views/admin/empresa_post.php';
    break;

  default:
    http_response_code(404);
    echo "Ruta no encontrada.";
}
