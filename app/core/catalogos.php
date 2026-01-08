<?php
declare(strict_types=1);

function catalogos_config(): array {
  return [
    'cargo' => [
      'title' => 'Cargos',
      'table' => 'cargo',
      'pk' => 'CargoId',
      'manual_pk' => false,
      'fields' => [
        'Cargo' => ['required' => true],
      ],
    ],
    'area' => [
      'title' => 'Áreas',
      'table' => 'area',
      'pk' => 'AreaId',
      'manual_pk' => false,
      'fields' => [
        'Area' => ['required' => true],
      ],
    ],
    'sector' => [
      'title' => 'Sectores',
      'table' => 'sector',
      'pk' => 'SectorId',
      'manual_pk' => false,
      'fields' => [
        'Sector' => ['required' => true],
      ],
    ],
    'tipo' => [
      'title' => 'Tipos',
      'table' => 'tipo',
      'pk' => 'TipoId',
      'manual_pk' => false,
      'fields' => [
        'Tipo' => ['required' => true],
      ],
    ],
    'turno' => [
      'title' => 'Turnos',
      'table' => 'turno',
      'pk' => 'TurnoId',
      'manual_pk' => false,
      'fields' => [
        'Turno' => ['required' => true],
        'TurnoHoraEntrada' => ['type' => 'time'],
        'TurnoHoraSalida' => ['type' => 'time'],
        'TurnoHoraSaleAlmorzar' => ['type' => 'time'],
        'TurnoHoraEntraAlmorzar' => ['type' => 'time'],
      ],
    ],

    'estadocivil' => [
      'title' => 'Estado civil',
      'table' => 'estadocivil',
      'pk' => 'EstadoCivilId',
      'manual_pk' => false,
      'fields' => [
        'EstadoCivilDes' => ['required' => true],
      ],
    ],
    'tipodocumento' => [
      'title' => 'Tipo de documento',
      'table' => 'tipodocumento',
      'pk' => 'TipoDocumentoId',
      'manual_pk' => false,
      'fields' => [
        'TipoDocumentoDes' => ['required' => true],
      ],
    ],
    'pais' => [
      'title' => 'País',
      'table' => 'pais',
      'pk' => 'PaisId',
      'manual_pk' => false,
      'fields' => [
        'PaisDes' => ['required' => true],
      ],
    ],

    // ✅ IDs NO AUTONUMÉRICOS (se cargan desde planilla)
    'departamento' => [
      'title' => 'Departamento',
      'table' => 'departamento',
      'pk' => 'DptoId',
      'manual_pk' => true,
      'fields' => [
        'DptoDes' => ['required' => true],
      ],
    ],
    'distrito' => [
      'title' => 'Distrito',
      'table' => 'distrito',
      'pk' => 'DistritoId',
      'manual_pk' => true,
      'fields' => [
        'DistritoDes' => ['required' => true],
      ],
    ],
    'localidad' => [
      'title' => 'Localidad',
      'table' => 'localidad',
      'pk' => 'LocalidadId',
      'manual_pk' => true,
      'fields' => [
        'LocalidadDes' => ['required' => true],
      ],
    ],

    'formapago' => [
      'title' => 'Forma de pago',
      'table' => 'formapago',
      'pk' => 'FormaPagoId',
      'manual_pk' => false,
      'fields' => [
        'FormaPagoDes' => ['required' => true],
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
    'manual_pk' => (bool)($c['manual_pk'] ?? false),
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

  // ✅ Si PK es manual, en insert debemos incluirlo
  if ($cat['manual_pk']) {
    $manualId = isset($data[$pk]) ? (int)$data[$pk] : 0;
    if (!$id && $manualId > 0) {
      $payload = array_merge([$pk => $manualId], $payload);
    }
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
