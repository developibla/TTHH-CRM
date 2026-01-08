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
