<?php
declare(strict_types=1);

/**
 * Configuración central de catálogos.
 * Cada catálogo define tabla, PK y campos.
 */
function catalogos_config(): array {
  return [
    'cargo' => [
      'title' => 'Cargos',
      'table' => 'cargo',
      'pk' => 'CargoId',
      'label' => 'Cargo',
      'fields' => [
        'Cargo' => ['type' => 'text', 'required' => true, 'max' => 120],
      ],
    ],
    'area' => [
      'title' => 'Áreas',
      'table' => 'area',
      'pk' => 'AreaId',
      'label' => 'Área',
      'fields' => [
        'Area' => ['type' => 'text', 'required' => true, 'max' => 120],
      ],
    ],
    'sector' => [
      'title' => 'Sectores',
      'table' => 'sector',
      'pk' => 'SectorId',
      'label' => 'Sector',
      'fields' => [
        'Sector' => ['type' => 'text', 'required' => true, 'max' => 120],
      ],
    ],
    'tipo' => [
      'title' => 'Tipos',
      'table' => 'tipo',
      'pk' => 'TipoId',
      'label' => 'Tipo',
      'fields' => [
        'Tipo' => ['type' => 'text', 'required' => true, 'max' => 120],
      ],
    ],
    'turno' => [
      'title' => 'Turnos',
      'table' => 'turno',
      'pk' => 'TurnoId',
      'label' => 'Turno',
      'fields' => [
        'Turno' => ['type' => 'text', 'required' => true, 'max' => 120],
        'TurnoHoraEntrada' => ['type' => 'time', 'required' => false],
        'TurnoHoraSalida' => ['type' => 'time', 'required' => false],
        'TurnoHoraSaleAlmorzar' => ['type' => 'time', 'required' => false],
        'TurnoHoraEntraAlmorzar' => ['type' => 'time', 'required' => false],
      ],
    ],
  ];
}

function catalogo_get(string $key): array {
  $cfg = catalogos_config();
  if (!isset($cfg[$key])) {
    http_response_code(400);
    echo "Catálogo inválido.";
    exit;
  }
  return $cfg[$key] + ['key' => $key];
}

function catalogo_list(string $key, string $q = ''): array {
  $c = catalogo_get($key);
  $table = $c['table'];
  $pk = $c['pk'];

  $where = "";
  $params = [];

  if ($q !== '') {
    // buscamos por todos los campos principales definidos
    $parts = [];
    foreach (array_keys($c['fields']) as $f) {
      $parts[] = "$f LIKE ?";
      $params[] = "%$q%";
    }
    $where = "WHERE (" . implode(" OR ", $parts) . ")";
  }

  $sql = "SELECT * FROM {$table} {$where} ORDER BY {$pk} DESC LIMIT 500";
  $st = db()->prepare($sql);
  $st->execute($params);
  return $st->fetchAll();
}

function catalogo_find(string $key, int $id): ?array {
  $c = catalogo_get($key);
  $sql = "SELECT * FROM {$c['table']} WHERE {$c['pk']}=? LIMIT 1";
  $st = db()->prepare($sql);
  $st->execute([$id]);
  $row = $st->fetch();
  return $row ?: null;
}

function catalogo_save(string $key, ?int $id, array $data): void {
  $c = catalogo_get($key);
  $table = $c['table'];
  $pk = $c['pk'];

  // validación simple
  foreach ($c['fields'] as $field => $meta) {
    $val = $data[$field] ?? null;

    if (($meta['type'] ?? '') === 'text') {
      $val = trim((string)$val);
      if (($meta['required'] ?? false) && $val === '') {
        throw new RuntimeException("El campo {$field} es obligatorio.");
      }
      if (isset($meta['max']) && mb_strlen($val) > (int)$meta['max']) {
        throw new RuntimeException("El campo {$field} supera el máximo permitido.");
      }
      $data[$field] = $val;
    }

    if (($meta['type'] ?? '') === 'time') {
      $val = trim((string)$val);
      if ($val === '') {
        $data[$field] = null;
      } else {
        // formato HH:MM o HH:MM:SS
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $val)) {
          throw new RuntimeException("Hora inválida en {$field}. Use HH:MM.");
        }
        // normalizamos a HH:MM:SS
        if (strlen($val) === 5) $val .= ':00';
        $data[$field] = $val;
      }
    }
  }

  // activo
  $activo = isset($data['Activo']) ? (int)$data['Activo'] : 1;
  $activo = ($activo === 1) ? 1 : 0;

  $fields = array_keys($c['fields']);

  if ($id) {
    $sets = [];
    $params = [];
    foreach ($fields as $f) {
      $sets[] = "{$f}=?";
      $params[] = $data[$f] ?? null;
    }
    $sets[] = "Activo=?";
    $params[] = $activo;
    $params[] = $id;

    $sql = "UPDATE {$table} SET " . implode(", ", $sets) . " WHERE {$pk}=?";
    db()->prepare($sql)->execute($params);
  } else {
    $cols = [];
    $place = [];
    $params = [];
    foreach ($fields as $f) {
      $cols[] = $f;
      $place[] = "?";
      $params[] = $data[$f] ?? null;
    }
    $cols[] = "Activo";
    $place[] = "?";
    $params[] = $activo;

    $sql = "INSERT INTO {$table} (" . implode(",", $cols) . ") VALUES (" . implode(",", $place) . ")";
    db()->prepare($sql)->execute($params);
  }
}

function catalogo_delete(string $key, int $id): void {
  $c = catalogo_get($key);
  $sql = "DELETE FROM {$c['table']} WHERE {$c['pk']}=? LIMIT 1";
  db()->prepare($sql)->execute([$id]);
}
