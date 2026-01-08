<?php
declare(strict_types=1);

final class ImportGeo
{
  public static function config(): array
  {
    return [
      'departamento' => [
        'title' => 'Departamentos',
        'table' => 'departamento',
        'pk' => 'DptoId',
        'desc' => 'DptoDes',
      ],
      'distrito' => [
        'title' => 'Distritos',
        'table' => 'distrito',
        'pk' => 'DistritoId',
        'desc' => 'DistritoDes',
      ],
      'localidad' => [
        'title' => 'Localidades',
        'table' => 'localidad',
        'pk' => 'LocalidadId',
        'desc' => 'LocalidadDes',
      ],
    ];
  }

  public static function get(string $key): array
  {
    $cfg = self::config();
    if (!isset($cfg[$key])) $key = 'departamento';
    return $cfg[$key] + ['key' => $key];
  }

  public static function detectDelimiter(string $path): string
  {
    $sample = file_get_contents($path, false, null, 0, 4096);
    if ($sample === false) return ';';

    $delims = [';' => substr_count($sample, ';'), ',' => substr_count($sample, ','), "\t" => substr_count($sample, "\t")];
    arsort($delims);
    $best = array_key_first($delims);
    return $best ?: ';';
  }

  /**
   * Importa CSV con columnas:
   *   ID;DESCRIPCION;ACTIVO (ACTIVO opcional)
   * Puede tener encabezado o no.
   *
   * @return array{ok:bool,message:string,inserted:int,updated:int,errors:array<int,array{line:int,error:string,row:array}>}
   */
  public static function importCsv(string $tipo, string $tmpPath, bool $hasHeader, ?string $delimiter = null): array
  {
    $cat = self::get($tipo);
    $table = $cat['table'];
    $pk = $cat['pk'];
    $desc = $cat['desc'];

    if (!is_file($tmpPath) || !is_readable($tmpPath)) {
      return ['ok' => false, 'message' => 'Archivo CSV no legible.', 'inserted' => 0, 'updated' => 0, 'errors' => []];
    }

    $delimiter = $delimiter ?: self::detectDelimiter($tmpPath);

    $fh = new SplFileObject($tmpPath, 'r');
    $fh->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
    $fh->setCsvControl($delimiter);

    $errors = [];
    $inserted = 0;
    $updated = 0;

    // Leer encabezado si aplica
    $headerMap = null;
    $lineNo = 0;

    if ($hasHeader) {
      $line = $fh->fgetcsv();
      $lineNo++;

      if (!is_array($line)) $line = [];
      $norm = [];
      foreach ($line as $h) {
        $h = is_string($h) ? trim($h) : '';
        $h = mb_strtolower($h, 'UTF-8');
        $norm[] = $h;
      }

      // Aceptamos variantes comunes
      // Ej: dptoid, dpto_id, dpto, id, codigo
      $headerMap = [
        'id' => self::findHeaderIndex($norm, ['id','codigo','código', mb_strtolower($pk, 'UTF-8'), mb_strtolower(str_replace('Id','', $pk), 'UTF-8')]),
        'des' => self::findHeaderIndex($norm, ['des','descripcion','descripción', mb_strtolower($desc, 'UTF-8')]),
        'activo' => self::findHeaderIndex($norm, ['activo','act','estado']),
      ];

      if ($headerMap['id'] === -1 || $headerMap['des'] === -1) {
        return [
          'ok' => false,
          'message' => "Encabezados no reconocidos. Se espera ID y DESCRIPCIÓN (y Activo opcional).",
          'inserted' => 0,
          'updated' => 0,
          'errors' => []
        ];
      }
    }

    // Preparar SQL UPSERT
    $sql = "INSERT INTO `$table` (`$pk`, `$desc`, `Activo`)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
              `$desc` = VALUES(`$desc`),
              `Activo` = VALUES(`Activo`)";

    $st = DB::pdo()->prepare($sql);

    DB::pdo()->beginTransaction();

    try {
      while (!$fh->eof()) {
        $row = $fh->fgetcsv();
        $lineNo++;

        if (!is_array($row) || self::isEmptyCsvRow($row)) continue;

        // Obtener valores según modo
        if ($hasHeader && is_array($headerMap)) {
          $idRaw = self::cell($row, $headerMap['id']);
          $desRaw = self::cell($row, $headerMap['des']);
          $actRaw = $headerMap['activo'] !== -1 ? self::cell($row, $headerMap['activo']) : '';
        } else {
          // Sin encabezado: asumimos orden [0]=ID [1]=DES [2]=ACTIVO(opcional)
          $idRaw = self::cell($row, 0);
          $desRaw = self::cell($row, 1);
          $actRaw = self::cell($row, 2);
        }

        $idRaw = trim((string)$idRaw);
        $desRaw = trim((string)$desRaw);
        $actRaw = trim((string)$actRaw);

        // Validaciones
        if ($idRaw === '' || !ctype_digit($idRaw)) {
          $errors[] = ['line' => $lineNo, 'error' => 'ID inválido (debe ser numérico entero).', 'row' => $row];
          continue;
        }
        $id = (int)$idRaw;
        if ($id <= 0) {
          $errors[] = ['line' => $lineNo, 'error' => 'ID inválido (debe ser > 0).', 'row' => $row];
          continue;
        }

        if ($desRaw === '') {
          $errors[] = ['line' => $lineNo, 'error' => 'Descripción vacía.', 'row' => $row];
          continue;
        }

        $des = mb_strtoupper($desRaw, 'UTF-8');

        $activo = 1;
        if ($actRaw !== '') {
          // aceptamos: 1/0, SI/NO, S/N, TRUE/FALSE
          $a = mb_strtolower($actRaw, 'UTF-8');
          if ($a === '0' || $a === 'no' || $a === 'n' || $a === 'false') $activo = 0;
          if ($a === '1' || $a === 'si' || $a === 'sí' || $a === 's' || $a === 'true') $activo = 1;
          if (ctype_digit($a)) $activo = ((int)$a === 1) ? 1 : 0;
        }

        // Para contar inserted vs updated: consultamos rápido existencia
        $exists = DB::fetchOne("SELECT `$pk` FROM `$table` WHERE `$pk` = ? LIMIT 1", [$id]) ? true : false;

        $st->execute([$id, $des, $activo]);

        if ($exists) $updated++;
        else $inserted++;
      }

      DB::pdo()->commit();
    } catch (Throwable $e) {
      DB::pdo()->rollBack();
      return [
        'ok' => false,
        'message' => 'Error al importar: ' . $e->getMessage(),
        'inserted' => 0,
        'updated' => 0,
        'errors' => $errors
      ];
    }

    $msg = "Importación OK: Insertados $inserted · Actualizados $updated · Errores " . count($errors) . ".";
    return ['ok' => true, 'message' => $msg, 'inserted' => $inserted, 'updated' => $updated, 'errors' => $errors];
  }

  private static function findHeaderIndex(array $headers, array $candidates): int
  {
    foreach ($candidates as $cand) {
      $cand = mb_strtolower((string)$cand, 'UTF-8');
      $idx = array_search($cand, $headers, true);
      if ($idx !== false) return (int)$idx;
    }
    return -1;
  }

  private static function cell(array $row, int $idx): string
  {
    return isset($row[$idx]) ? (string)$row[$idx] : '';
  }

  private static function isEmptyCsvRow(array $row): bool
  {
    foreach ($row as $c) {
      if (trim((string)$c) !== '') return false;
    }
    return true;
  }
}
