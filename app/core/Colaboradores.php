<?php
declare(strict_types=1);

/**
 * Colaboradores - CRUD base
 * Requiere: DB.php, Helpers.php
 */

function colaboradores_list(string $q = ''): array
{
  $params = [];
  $where = '';

  if ($q !== '') {
    $where = "WHERE
      c.Nombres LIKE ? OR c.Apellidos LIKE ? OR c.Legajo LIKE ? OR c.NroDocumento LIKE ? OR c.Email LIKE ? OR c.RUC LIKE ?";
    $like = '%' . $q . '%';
    $params = [$like, $like, $like, $like, $like, $like];
  }

  $sql = "
    SELECT
      c.*,
      CASE
        WHEN c.FechaNacimiento IS NULL THEN NULL
        ELSE TIMESTAMPDIFF(YEAR, c.FechaNacimiento, CURDATE())
      END AS Edad,

      td.TipoDocumentoDes AS TipoDocumentoDes,
      ec.EstadoCivilDes AS EstadoCivilDes,
      p.PaisDes AS PaisDes,
      d.DptoDes AS DptoDes,
      di.DistritoDes AS DistritoDes,
      l.LocalidadDes AS LocalidadDes,
      ca.Cargo AS CargoDes,
      a.Area AS AreaDes,
      s.Sector AS SectorDes,
      t.Turno AS TurnoDes,
      ti.Tipo AS TipoDes,
      fp.FormaPagoDes AS FormaPagoDes
    FROM colaboradores c
    LEFT JOIN tipodocumento td ON td.TipoDocumentoId = c.TipoDocumentoId
    LEFT JOIN estadocivil ec ON ec.EstadoCivilId = c.EstadoCivilId
    LEFT JOIN pais p ON p.PaisId = c.PaisId
    LEFT JOIN departamento d ON d.DptoId = c.DptoId
    LEFT JOIN distrito di ON di.DistritoId = c.DistritoId
    LEFT JOIN localidad l ON l.LocalidadId = c.LocalidadId
    LEFT JOIN cargo ca ON ca.CargoId = c.CargoId
    LEFT JOIN area a ON a.AreaId = c.AreaId
    LEFT JOIN sector s ON s.SectorId = c.SectorId
    LEFT JOIN turno t ON t.TurnoId = c.TurnoId
    LEFT JOIN tipo ti ON ti.TipoId = c.TipoId
    LEFT JOIN formapago fp ON fp.FormaPagoId = c.FormaPagoId
    $where
    ORDER BY c.Apellidos ASC, c.Nombres ASC
    LIMIT 500
  ";

  return DB::fetchAll($sql, $params);
}

function colaborador_find(int $id): ?array
{
  return DB::fetchOne("SELECT * FROM colaboradores WHERE ColaboradorId = ? LIMIT 1", [$id]);
}

/**
 * Guarda y retorna el ID del colaborador (insert o update)
 */
function colaborador_save(array $data, ?int $id): int
{
  $activo = isset($data['Activo']) ? (int)$data['Activo'] : 1;
  $data['Activo'] = ($activo === 1) ? 1 : 0;

  $data['BonificacionFamiliar'] = !empty($data['BonificacionFamiliar']) ? 1 : 0;
  $data['Aguinaldo'] = isset($data['Aguinaldo']) ? ((int)$data['Aguinaldo'] === 1 ? 1 : 0) : 1;

  // Campos permitidos (whitelist)
  $cols = [
    'Legajo','Nombres','Apellidos',

    'TipoDocumentoId','NroDocumento','RUC',

    'EstadoCivilId','FechaNacimiento','GrupoSanguineo','VencimientoCI',

    'Email','Telefono','TelefonoParticular','Direccion',

    'PaisId','DptoId','DistritoId','LocalidadId',

    'CargoId','AreaId','SectorId','TurnoId','TipoId',

    'FormaPagoId','FechaIngreso','FechaEgreso',

    'SalarioBase','PlusCargo','NroAseguradoIPS',

    'BonificacionFamiliar','Aguinaldo',

    'FotoSelfiePath','FotoCIFrentePath','FotoCIAtrasPath',

    'Observacion',

    'Activo',
  ];

  $payload = [];
  foreach ($cols as $c) {
    $payload[$c] = $data[$c] ?? null;
  }

  // IDs vacíos => NULL
  foreach ($payload as $k => $v) {
    if (str_ends_with($k, 'Id') && ($v === '' || $v === null)) $payload[$k] = null;
  }

  // Fechas vacías => NULL
  foreach (['FechaNacimiento','VencimientoCI','FechaIngreso','FechaEgreso'] as $f) {
    if (($payload[$f] ?? '') === '') $payload[$f] = null;
  }

  // Normalizar money PYG: dejamos solo dígitos
  foreach (['SalarioBase','PlusCargo'] as $m) {
    $v = (string)($payload[$m] ?? '');
    $v = preg_replace('/[^\d]/', '', $v ?? '');
    $payload[$m] = ($v === '') ? null : $v;
  }

  // Normalizar texto corto
  if (isset($payload['GrupoSanguineo']) && is_string($payload['GrupoSanguineo'])) {
    $payload['GrupoSanguineo'] = strtoupper(trim($payload['GrupoSanguineo']));
  }

  if ($id && $id > 0) {
    $sets = [];
    $params = [];
    foreach ($payload as $col => $val) {
      $sets[] = "`$col` = ?";
      $params[] = $val;
    }
    $params[] = $id;

    DB::exec("UPDATE colaboradores SET " . implode(', ', $sets) . " WHERE ColaboradorId = ?", $params);
    return $id;
  }

  $colNames = array_keys($payload);
  $ph = array_fill(0, count($colNames), '?');
  $params = array_values($payload);

  DB::exec(
    "INSERT INTO colaboradores (`" . implode('`,`', $colNames) . "`) VALUES (" . implode(',', $ph) . ")",
    $params
  );

  return (int)DB::lastId();
}

function colaborador_delete(int $id): void
{
  DB::exec("DELETE FROM colaboradores WHERE ColaboradorId = ? LIMIT 1", [$id]);
}

/** =========================
 * Combos genéricos
 * ========================= */
function combo_list(string $table, string $idCol, string $desCol): array
{
  try {
    return DB::fetchAll("SELECT `$idCol` AS id, `$desCol` AS des FROM `$table` WHERE `Activo`=1 ORDER BY `$desCol` ASC");
  } catch (Throwable $e) {
    return DB::fetchAll("SELECT `$idCol` AS id, `$desCol` AS des FROM `$table` ORDER BY `$desCol` ASC");
  }
}
