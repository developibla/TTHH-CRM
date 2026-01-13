<?php
declare(strict_types=1);

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
      ['title' => 'Parámetros de Empresa', 'route' => 'empresa',   'icon' => 'building', 'aliases' => []],
      ['title' => 'Catálogos',             'route' => 'catalogos', 'icon' => 'list',     'aliases' => ['catalogos_post']],
    ],
  ],
  [
    'key' => 'leg',
    'title' => 'LEGAJOS',
    'icon' => 'folder',
    'items' => [
      ['title' => 'Colaboradores', 'route' => 'colaboradores', 'icon' => 'people', 'aliases' => ['colaboradores_post']],
      ['title' => 'Contratos',     'route' => 'contratos',     'icon' => 'file',   'aliases' => []],
    ],
  ],
  [
    'key' => 'mov',
    'title' => 'MOVIMIENTOS',
    'icon' => 'arrow',
    'items' => [
      ['title' => 'Vacaciones',         'route' => 'vacaciones',    'icon' => 'calendar', 'aliases' => []],
      ['title' => 'Suspensiones',       'route' => 'suspensiones',  'icon' => 'pause',    'aliases' => []],
      ['title' => 'Mov. por Conceptos', 'route' => 'mov_conceptos', 'icon' => 'repeat',   'aliases' => []],

      // ✅ SOLO 1 ITEM (sin duplicar)
      [
        'title' => 'Marcaciones (Reloj)',
        'route' => 'import_reloj',
        'icon'  => 'clock',
        'aliases' => ['import_reloj_post', 'reconcile_reloj_post']
      ],
    ],
  ],
  [
    'key' => 'rep',
    'title' => 'REPORTES',
    'icon' => 'chart',
    'items' => [
      ['title' => 'Listados',        'route' => 'reportes',      'icon' => 'doc',       'aliases' => []],
      ['title' => 'Recibos',         'route' => 'recibos',       'icon' => 'receipt',   'aliases' => []],
      ['title' => 'Planillas IPS',   'route' => 'planilla_ips',  'icon' => 'shield',    'aliases' => []],
      ['title' => 'Planillas MTESS', 'route' => 'planilla_mtess','icon' => 'briefcase', 'aliases' => []],
    ],
  ],
];

$openKey = $menu[0]['key'];
foreach ($menu as $sec) {
  foreach ($sec['items'] as $it) {
    $aliases = (array)($it['aliases'] ?? []);
    if (is_active_route((string)$it['route'], $aliases)) {
      $openKey = (string)$sec['key'];
      break 2;
    }
  }
}

function ico(string $name): string {
  switch ($name) {
    case 'wrench': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.102 2.223a.5.5 0 0 1 .606-.607l2.876.72a.5.5 0 0 1 .364.485v1.48l2.96 2.96 1.48.001a.5.5 0 0 1 .485.364l.72 2.876a.5.5 0 0 1-.607.606l-2.876-.72a.5.5 0 0 1-.364-.485V9.5L5.2 6.954H3.72a.5.5 0 0 1-.485-.364l-.72-2.876z"/><path d="M5.5 11.5 2 15l-1-1 3.5-3.5 1 1z"/></svg>';
    case 'folder': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M9.828 3a.5.5 0 0 1 .354.146l.646.647H14.5A1.5 1.5 0 0 1 16 5.293v7.207A1.5 1.5 0 0 1 14.5 14h-13A1.5 1.5 0 0 1 0 12.5v-10A1.5 1.5 0 0 1 1.5 1h5.379a.5.5 0 0 1 .353.146L8.354 2.5H9.828z"/></svg>';
    case 'arrow': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.5 1a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0V3.207L4.854 9.354a.5.5 0 1 1-.708-.708L10.293 2.5H7.5a.5.5 0 0 1 0-1h4z"/><path d="M4.5 15a.5.5 0 0 1-.5-.5v-5a.5.5 0 0 1 1 0v3.293l6.146-6.147a.5.5 0 0 1 .708.708L5.707 13.5H8.5a.5.5 0 0 1 0 1h-4z"/></svg>';
    case 'chart': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 0h1v15h15v1H0V0z"/><path d="M2 13h2V7H2v6zm4 0h2V3H6v10zm4 0h2V9h-2v4zm4 0h2V5h-2v8z"/></svg>';
    case 'building': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M6.5 15.5v-15h-5v15h5zm1 0h7v-10h-7v10zM2.5 2h3v2h-3V2zm0 3h3v2h-3V5zm0 3h3v2h-3V8zm0 3h3v2h-3v-2zm6-5h2v2h-2V6zm0 3h2v2h-2V9zm3-3h2v2h-2V6zm0 3h2v2h-2V9z"/></svg>';
    case 'list': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 2.5zm0 4a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 6.5zm0 4a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0 3.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>';
    case 'people': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 0a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/><path d="M2 14s-1 0-1-1 1-4 4-4 4 3 4 4-1 1-1 1H2zm9 0c-.223 0-.443-.01-.656-.03.11-.67.656-1.97 2.156-2.47C14.5 10.7 16 12.4 16 13s-1 1-1 1h-4z"/></svg>';
    case 'file': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 0h5.5L14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/><path d="M9.5 0v4a1 1 0 0 0 1 1h4"/></svg>';
    case 'calendar': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h.5A1.5 1.5 0 0 1 15 2.5v11A1.5 1.5 0 0 1 13.5 15h-11A1.5 1.5 0 0 1 1 13.5v-11A1.5 1.5 0 0 1 2.5 1H3V.5a.5.5 0 0 1 .5-.5zM2 6h12V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5V6z"/></svg>';
    case 'pause': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 3.5A.5.5 0 0 1 6 4v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5zm5 0A.5.5 0 0 1 11 4v8a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5z"/></svg>';
    case 'repeat': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 1.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V2.707l-2.146 2.147a.5.5 0 0 1-.708-.708L13.293 2H11.5a.5.5 0 0 1-.5-.5z"/><path d="M5 14.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 1 0v1.793l2.146-2.147a.5.5 0 0 1 .708.708L2.707 14H4.5a.5.5 0 0 1 .5.5z"/><path d="M2 5a4 4 0 0 1 4-4h5a.5.5 0 0 1 0 1H6a3 3 0 0 0-3 3v1a.5.5 0 0 1-1 0V5z"/><path d="M14 11a4 4 0 0 1-4 4H5a.5.5 0 0 1 0-1h5a3 3 0 0 0 3-3v-1a.5.5 0 0 1 1 0v1z"/></svg>';
    case 'doc': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 0h5.5L14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/><path d="M9.5 0v4a1 1 0 0 0 1 1h4"/><path d="M4.5 9h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1zm0 2h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1z"/></svg>';
    case 'receipt': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M1 0v16l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V0H1z"/><path d="M3 3h10v1H3V3zm0 2h10v1H3V5zm0 2h10v1H3V7zm0 2h7v1H3V9z"/></svg>';
    case 'shield': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0c-.69 0-3.5 1.2-6 2.5v5.5c0 3.5 2.7 6.7 6 8 3.3-1.3 6-4.5 6-8V2.5C11.5 1.2 8.69 0 8 0z"/></svg>';
    case 'briefcase': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M6.5 0a1 1 0 0 0-1 1v1H2a2 2 0 0 0-2 2v3h16V4a2 2 0 0 0-2-2h-3.5V1a1 1 0 0 0-1-1h-3zM6.5 2V1h3v1h-3z"/><path d="M0 8v4a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8H0z"/></svg>';
    case 'clock': return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l2.25 1.35a.5.5 0 0 1-.5.86l-2.5-1.5A.5.5 0 0 1 7.5 9V4a.5.5 0 0 1 .5-.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/></svg>';
    default:
      return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7"/></svg>';
  }
}
?>

<nav class="acc" aria-label="Menú principal" data-accordion-root data-open-key="<?= htmlspecialchars($openKey, ENT_QUOTES, 'UTF-8') ?>">
  <?php foreach ($menu as $sec): ?>
    <?php $isOpen = ($sec['key'] === $openKey); ?>
    <button class="accordion <?= $isOpen ? 'active' : '' ?>"
            type="button"
            data-acc="<?= htmlspecialchars($sec['key'], ENT_QUOTES, 'UTF-8') ?>"
            aria-expanded="<?= $isOpen ? 'true' : 'false' ?>">
      <div class="left">
        <div class="ico"><?= ico($sec['icon']) ?></div>
        <span class="label-text"><?= htmlspecialchars($sec['title'], ENT_QUOTES, 'UTF-8') ?></span>
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
           href="index.php?r=<?= htmlspecialchars($it['route'], ENT_QUOTES, 'UTF-8') ?>">
          <span><?= htmlspecialchars($it['title'], ENT_QUOTES, 'UTF-8') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</nav>
