<?php
declare(strict_types=1);

$r = (string)($_GET['r'] ?? 'home');

function is_active_route(string $route, array $aliases = []): bool {
  $r = (string)($_GET['r'] ?? '');
  if ($r === $route) return true;
  return in_array($r, $aliases, true);
}

$menu = [
  [
    'key' => 'mnt',
    'title' => 'MANTENIMIENTO',
    'icon' => 'wrench',
    'items' => [
      ['title' => 'Parámetros de Empresa', 'route' => 'empresa'],
      ['title' => 'Catálogos',             'route' => 'catalogos'],
    ],
  ],
  [
    'key' => 'leg',
    'title' => 'LEGAJOS',
    'icon' => 'folder',
    'items' => [
      ['title' => 'Colaboradores', 'route' => 'colaboradores'],
      ['title' => 'Contratos',     'route' => 'contratos'],
    ],
  ],
  [
    'key' => 'mov',
    'title' => 'MOVIMIENTOS',
    'icon' => 'arrow',
    'items' => [
      ['title' => 'Vacaciones',         'route' => 'vacaciones'],
      ['title' => 'Suspensiones',       'route' => 'suspensiones'],
      ['title' => 'Mov. por Conceptos', 'route' => 'mov_conceptos'],
    ],
  ],
  [
    'key' => 'rep',
    'title' => 'REPORTES',
    'icon' => 'chart',
    'items' => [
      ['title' => 'Listados',        'route' => 'reportes'],
      ['title' => 'Recibos',         'route' => 'recibos'],
      ['title' => 'Planillas IPS',   'route' => 'planilla_ips'],
      ['title' => 'Planillas MTESS', 'route' => 'planilla_mtess'],
    ],
  ],
];

$openKey = $menu[0]['key'];
foreach ($menu as $sec) {
  foreach ($sec['items'] as $it) {
    if (is_active_route($it['route'])) { $openKey = $sec['key']; break 2; }
  }
}

function ico(string $name): string {
  switch ($name) {
    case 'wrench':
      return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M.102 2.223a.5.5 0 0 1 .606-.607l2.876.72a.5.5 0 0 1 .364.485v1.48l2.96 2.96 1.48.001a.5.5 0 0 1 .485.364l.72 2.876a.5.5 0 0 1-.607.606l-2.876-.72a.5.5 0 0 1-.364-.485V9.5L5.2 6.954H3.72a.5.5 0 0 1-.485-.364l-.72-2.876z"/>
        <path d="M5.5 11.5 2 15l-1-1 3.5-3.5 1 1z"/>
      </svg>';
    case 'folder':
      return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M9.828 3a.5.5 0 0 1 .354.146l.646.647H14.5A1.5 1.5 0 0 1 16 5.293v7.207A1.5 1.5 0 0 1 14.5 14h-13A1.5 1.5 0 0 1 0 12.5v-10A1.5 1.5 0 0 1 1.5 1h5.379a.5.5 0 0 1 .353.146L8.354 2.5H9.828z"/>
      </svg>';
    case 'arrow':
      return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M11.5 1a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0V3.207L4.854 9.354a.5.5 0 1 1-.708-.708L10.293 2.5H7.5a.5.5 0 0 1 0-1h4z"/>
        <path d="M4.5 15a.5.5 0 0 1-.5-.5v-5a.5.5 0 0 1 1 0v3.293l6.146-6.147a.5.5 0 0 1 .708.708L5.707 13.5H8.5a.5.5 0 0 1 0 1h-4z"/>
      </svg>';
    case 'chart':
      return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
        <path d="M0 0h1v15h15v1H0V0z"/>
        <path d="M2 13h2V7H2v6zm4 0h2V3H6v10zm4 0h2V9h-2v4zm4 0h2V5h-2v8z"/>
      </svg>';
    default:
      return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7"/></svg>';
  }
}
?>

<nav class="acc" aria-label="Menú principal">
  <?php foreach ($menu as $sec): ?>
    <?php $isOpen = ($sec['key'] === $openKey); ?>
    <button class="accordion <?= $isOpen ? 'active' : '' ?>"
            type="button"
            data-acc="<?= e($sec['key']) ?>">
      <div class="left">
        <div class="ico"><?= ico($sec['icon']) ?></div>
        <span class="label-text"><?= e($sec['title']) ?></span>
      </div>
      <div class="chev">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
        </svg>
      </div>
    </button>

    <div class="panel" style="<?= $isOpen ? 'display:block;' : 'display:none;' ?>">
      <?php foreach ($sec['items'] as $it): ?>
        <?php $active = is_active_route($it['route']); ?>
        <a class="<?= $active ? 'active' : '' ?>"
           href="index.php?r=<?= e($it['route']) ?>">
          <span><?= e($it['title']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</nav>
