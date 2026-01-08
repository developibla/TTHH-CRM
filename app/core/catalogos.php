<?php
declare(strict_types=1);

/**
 * Configuración de catálogos y su mapeo a tablas reales.
 * Ajustá los nombres de tabla/PK/campos si difieren en tu BD.
 */
function catalogos_config(): array {
  return [
    'cargo' => [
      'title' => 'Cargos',
      'table' => 'cargo',
      'pk' => 'CargoId',
      'fields' => [
        'Cargo' => ['required' => true],
      ],
    ],
    'area' => [
      'title' => 'Áreas',
      'table' => 'area',
      'pk' => 'AreaId',
      'fields' => [
        'Area' => ['required' => true],
      ],
    ],
    'sector' => [
      'title' => 'Sectores',
      'table' => 'sector',
      'pk' => 'SectorId',
      'fields' => [
        'Sector' => ['required' => true],
      ],
    ],
    'tipo' => [
      'title' => 'Tipos',
      'table' => 'tipo',
      'pk' => 'TipoId',
      'fields' => [
        'Tipo' => ['required' => true],
      ],
    ],
    'turno' => [
      'title' => 'Turnos',
      'table' => 'turno',
      'pk' => 'TurnoId',
      'fields' => [
        'Turno' => ['required' => true],
        'TurnoHoraEntrada' => ['type' => 'time'],
        'TurnoHoraSalida' => ['type' => 'time'],
        'TurnoHoraSaleAlmorzar' => ['type' => 'time'],
        'TurnoHoraEntraAlmorzar' => ['type' => 'time'],
      ],
    ],
  ];
}

function catalogo_get(string $key): array {
  $cfg = catalogos_config();
  if (!isset($cfg[$key])) $key = 'cargo';
  $c = $cfg[$key];

  return [
    'key' => $key,
    'title' => $c['title'],
    'table' => $c['table'],
    'pk' => $c['pk'],
    'fields' => $c['fields'],
  ];
}

function catalogo_list(string $key, string $q = ''): array {
  $cat = catalogo_get($key);
  $table = $cat['table'];
  $pk = $cat['pk'];

  $where = '';
  $params = [];

  if ($q !== '') {
    // buscar en todos los campos configurados
    $likes = [];
    foreach (array_keys($cat['fields']) as $f) {
      $likes[] = "`$f` LIKE ?";
      $params[] = '%' . $q . '%';
    }
    $where = 'WHERE ' . implode(' OR ', $likes);
  }

  $sql = "SELECT * FROM `$table` $where ORDER BY `$pk` DESC LIMIT 500";
  return DB::fetchAll($sql, $params);
}

function catalogo_find(string $key, int $id): ?array {
  $cat = catalogo_get($key);
  $sql = "SELECT * FROM `{$cat['table']}` WHERE `{$cat['pk']}` = ? LIMIT 1";
  return DB::fetchOne($sql, [$id]);
}

/**
 * Guardar (insert/update) y eliminar
 */
function catalogo_save(string $key, array $data, ?int $id): void {
  $cat = catalogo_get($key);
  $table = $cat['table'];
  $pk = $cat['pk'];

  // Normalizar Activo
  $activo = isset($data['Activo']) ? (int)$data['Activo'] : 1;
  $data['Activo'] = ($activo === 1) ? 1 : 0;

  // Construir payload solo con fields + Activo
  $cols = array_keys($cat['fields']);
  $cols[] = 'Activo';

  $payload = [];
  foreach ($cols as $c) {
    $payload[$c] = $data[$c] ?? null;
  }

  if ($id && $id > 0) {
    // UPDATE
    $sets = [];
    $params = [];
    foreach ($payload as $col => $val) {
      $sets[] = "`$col` = ?";
      $params[] = $val;
    }
    $params[] = $id;

    $sql = "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE `$pk` = ?";
    DB::exec($sql, $params);
  } else {
    // INSERT
    $colNames = array_keys($payload);
    $placeholders = array_fill(0, count($colNames), '?');
    $params = array_values($payload);

    $sql = "INSERT INTO `$table` (`" . implode('`,`', $colNames) . "`) VALUES (" . implode(',', $placeholders) . ")";
    DB::exec($sql, $params);
  }
}

function catalogo_delete(string $key, int $id): void {
  $cat = catalogo_get($key);
  $sql = "DELETE FROM `{$cat['table']}` WHERE `{$cat['pk']}` = ? LIMIT 1";
  DB::exec($sql, [$id]);
}

/**
 * ==========================================
 * HANDLER de la ruta: index.php?r=catalogos_post
 * - soporta POST tradicional + AJAX (_ajax=1)
 * ==========================================
 */
function catalogos_post(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php?r=catalogos');
  }

  // CSRF
  $token = (string)($_POST['_csrf'] ?? '');
  if (!csrf_validate($token)) {
    catalogos_respond(false, 'Sesión inválida (CSRF). Actualizá la página e intentá de nuevo.');
  }

  $key = (string)($_POST['t'] ?? 'cargo');
  $cat = catalogo_get($key);

  // DELETE
  $deleteId = (int)($_POST['delete_id'] ?? 0);
  if ($deleteId > 0) {
    try {
      catalogo_delete($key, $deleteId);
      catalogos_respond(true, 'Registro eliminado correctamente.', [
        't' => $key,
        'deleted_id' => $deleteId,
        'fields' => array_keys($cat['fields']),
        'csrf' => csrf_token(),
      ]);
    } catch (Throwable $e) {
      catalogos_respond(false, 'No se pudo eliminar. ' . $e->getMessage());
    }
  }

  // SAVE (insert / update)
  $id = (int)($_POST['id'] ?? 0);
  $data = [];

  // tomar fields configurados
  foreach ($cat['fields'] as $field => $meta) {
    $val = $_POST[$field] ?? '';

    // normalizar time => HH:MM
    if (($meta['type'] ?? '') === 'time') {
      $val = is_string($val) ? trim($val) : '';
      if ($val !== '' && strlen($val) >= 5) $val = substr($val, 0, 5);
      $data[$field] = $val;
      continue;
    }

    // texto => trim + UPPER (server-side)
    $val = is_string($val) ? trim($val) : '';
    if ($val !== '') {
      $val = mb_strtoupper($val, 'UTF-8');
    }
    $data[$field] = $val;
  }

  // Activo
  $data['Activo'] = isset($_POST['Activo']) ? (int)$_POST['Activo'] : 1;

  // validar requeridos
  foreach ($cat['fields'] as $field => $meta) {
    if (!empty($meta['required'])) {
      $v = (string)($data[$field] ?? '');
      if (trim($v) === '') {
        catalogos_respond(false, "El campo '$field' es obligatorio.", [
          't' => $key,
          'fields' => array_keys($cat['fields']),
          'csrf' => csrf_token(),
        ]);
      }
    }
  }

  try {
    catalogo_save($key, $data, $id > 0 ? $id : null);

    // obtener ID final (si era insert)
    $finalId = $id > 0 ? $id : (int)DB::lastId();

    $row = catalogo_find($key, $finalId) ?: [];
    $rowOut = catalogos_row_out($cat, $row);

    catalogos_respond(true, $id > 0 ? 'Cambios guardados.' : 'Registro agregado.', [
      't' => $key,
      'row' => $rowOut,
      'fields' => array_keys($cat['fields']),
      'csrf' => csrf_token(),
    ]);

  } catch (Throwable $e) {
    catalogos_respond(false, 'No se pudo guardar. ' . $e->getMessage(), [
      't' => $key,
      'fields' => array_keys($cat['fields']),
      'csrf' => csrf_token(),
    ]);
  }
}

/**
 * Convertir una fila de BD a un objeto listo para JS
 * - agrega 'id' estándar
 * - mantiene campos + Activo
 */
function catalogos_row_out(array $cat, array $row): array {
  $out = [];
  $pk = $cat['pk'];

  $out['id'] = (int)($row[$pk] ?? 0);

  foreach (array_keys($cat['fields']) as $f) {
    $out[$f] = $row[$f] ?? '';
  }

  $out['Activo'] = (int)($row['Activo'] ?? 1);
  return $out;
}

/**
 * Responder AJAX o redirect con flash
 */
function catalogos_respond(bool $ok, string $msg, array $extra = []): void {
  $isAjax = (string)($_POST['_ajax'] ?? '') === '1';

  if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
      'ok' => $ok,
      'message' => $msg,
    ], $extra), JSON_UNESCAPED_UNICODE);
    exit;
  }

  if ($ok) $_SESSION['flash_ok'] = $msg;
  else $_SESSION['flash_error'] = $msg;

  $t = (string)($extra['t'] ?? ($_POST['t'] ?? 'cargo'));
  redirect('index.php?r=catalogos&t=' . urlencode($t));
}
