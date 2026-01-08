<?php
declare(strict_types=1);

$cfg = require __DIR__ . '/../../app/config/config.php';
$empresa = company_params();
$user = current_user();
$route = (string)($_GET['r'] ?? 'home');

// Helper para marcar activo por ruta (básico)
function is_active_route(string $r, string $current): bool {
  return $r === $current;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($cfg['app_name']) ?></title>
  <link rel="stylesheet" href="public/assets/css/app.css">
</head>
<body>

<div class="container">

  <div class="topbar">
    <div class="brand">
      <?php if (!empty($empresa['logo_path'])): ?>
        <img src="<?= e($empresa['logo_path']) ?>" class="logo-img" alt="Logo">
      <?php else: ?>
        <div class="logo-fallback">TH</div>
      <?php endif; ?>

      <div class="meta">
        <div class="title"><?= e($empresa['empresa'] ?: 'Empresa') ?></div>
        <div class="sub"><?= e($cfg['app_name']) ?></div>
      </div>
    </div>

    <?php if ($user): ?>
      <div class="user-pill">
        <div>
          <div class="u1"><?= e($user['rol']) ?></div>
          <div class="u2"><?= e($user['nombre']) ?></div>
        </div>

        <a class="btn-icon" href="index.php?r=logout" title="Cerrar sesión" aria-label="Cerrar sesión">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M6 2a1 1 0 0 0-1 1v2h1V3h7v10H6v-2H5v2a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H6z"/>
            <path d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
          </svg>
        </a>
      </div>
    <?php endif; ?>
  </div>

  <div class="shell">

    <aside class="sidebar">
      <div class="acc">

        <!-- MANTENIMIENTO -->
        <button class="accordion active" type="button">
          <span class="left">
            <span class="ico">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M.102 2.223a.5.5 0 0 1 .58-.093l4.463 1.786a.5.5 0 0 1 .311.462v2.65a.5.5 0 0 1-.146.354L2.854 9.89a.5.5 0 0 1-.707 0L.606 8.35a.5.5 0 0 1 0-.707l2.106-2.106a.5.5 0 0 0 .146-.353V2.577a.5.5 0 0 1 .244-.354z"/>
                <path d="M7.5 1a.5.5 0 0 1 .5.5v6.793l2.146 2.147a.5.5 0 0 1-.708.707l-2.293-2.293A.5.5 0 0 1 7 8.5v-7A.5.5 0 0 1 7.5 1z"/>
              </svg>
            </span>
            Mantenimiento
          </span>
          <span class="chev">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
              <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
          </span>
        </button>
        <div class="panel" style="display:block;">
          <?php if (($user['rol'] ?? '') === 'ADMIN'): ?>
            <a class="<?= is_active_route('empresa', $route) ? 'active' : '' ?>" href="index.php?r=empresa">Parámetros</a>
            <a class="<?= is_active_route('catalogos', $route) ? 'active' : '' ?>" href="index.php?r=catalogos&t=cargo">Catálogos</a>
          <?php endif; ?>
        </div>

        <!-- LEGAJOS -->
        <button class="accordion" type="button">
          <span class="left">
            <span class="ico">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72a.265.265 0 0 1-.022.004H7.022z"/>
                <path d="M11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm-7 6c0 1-1 1-1 1H1s-1 0-1-1 1-4 5-4c.43 0 .83.04 1.2.115A5.49 5.49 0 0 0 4 13zm1-6a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
              </svg>
            </span>
            Legajos
          </span>
          <span class="chev">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
              <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
          </span>
        </button>
        <div class="panel">
          <a href="index.php?r=home">Colaboradores (próximo)</a>
          <a href="index.php?r=home">Contratos (próximo)</a>
          <a href="index.php?r=home">Documentos (próximo)</a>
        </div>

        <!-- MOVIMIENTOS -->
        <button class="accordion" type="button">
          <span class="left">
            <span class="ico">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M11.5 1a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0V2.707L8.854 4.854a.5.5 0 1 1-.708-.708L10.293 2H9.5a.5.5 0 0 1 0-1h2z"/>
                <path d="M4.5 15a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 1 1 0v1.793l2.146-2.147a.5.5 0 0 1 .708.708L5.707 15H6.5a.5.5 0 0 1 0 1h-2z"/>
              </svg>
            </span>
            Movimientos
          </span>
          <span class="chev">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
              <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
          </span>
        </button>
        <div class="panel">
          <a href="index.php?r=home">Vacaciones (próximo)</a>
          <a href="index.php?r=home">Suspensiones (próximo)</a>
          <a href="index.php?r=home">Mov. por Conceptos (próximo)</a>
        </div>

        <!-- REPORTES -->
        <button class="accordion" type="button">
          <span class="left">
            <span class="ico">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M0 0h1v15h15v1H0V0z"/>
                <path d="M2 13h2V6H2v7zm4 0h2V3H6v10zm4 0h2V9h-2v4zm4 0h2V1h-2v12z"/>
              </svg>
            </span>
            Reportes
          </span>
          <span class="chev">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
              <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
            </svg>
          </span>
        </button>
        <div class="panel">
          <a href="index.php?r=home">Listado Colaboradores (próximo)</a>
          <a href="index.php?r=home">Planilla IPS (próximo)</a>
          <a href="index.php?r=home">Recibos Salario (próximo)</a>
        </div>

      </div>

      <script>
        // Accordion: un panel abierto a la vez (como querés)
        (function(){
          var acc = document.getElementsByClassName("accordion");
          for (var i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
              // cerrar otros
              for (var j = 0; j < acc.length; j++) {
                if (acc[j] !== this) {
                  acc[j].classList.remove("active");
                  var p = acc[j].nextElementSibling;
                  if (p) p.style.display = "none";
                }
              }

              // toggle actual
              this.classList.toggle("active");
              var panel = this.nextElementSibling;
              if (!panel) return;

              if (panel.style.display === "block") {
                panel.style.display = "none";
              } else {
                panel.style.display = "block";
              }
            });
          }
        })();
      </script>

    </aside>

    <main class="content">
