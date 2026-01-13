<?php
declare(strict_types=1);

/**
 * Importa marcaciones desde CSV con formato:
 * ID de usuario,Fecha/Hora,Dispositivo Nro.,Tipo de registro
 * 4,2026-01-02 06:47:30,2,0
 *
 * Retorna: ['inserted'=>int,'skipped'=>int,'errors'=>int]
 */
function import_reloj_csv(string $csvPath, string $sourceName = ''): array
{
  if (!is_file($csvPath)) {
    throw new RuntimeException('Archivo CSV no encontrado.');
  }

  $fh = fopen($csvPath, 'r');
  if (!$fh) {
    throw new RuntimeException('No se pudo abrir el CSV.');
  }

  $inserted = 0;
  $skipped  = 0;
  $errors   = 0;

  // Detectar separador (coma o ;)
  $firstLine = fgets($fh);
  if ($firstLine === false) throw new RuntimeException('CSV vacío.');
  $sep = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

  rewind($fh);

  $pdo = DB::pdo();
  $pdo->beginTransaction();

  try {
    $lineNo = 0;

    // Buscar colaborador por CodigoReloj
    $stFindColab = $pdo->prepare("SELECT ColaboradorId FROM colaboradores WHERE CodigoReloj = ? LIMIT 1");

    // Insert en movimientos
    $stInsert = $pdo->prepare("
      INSERT INTO reloj_movimientos
        (CodigoReloj, ColaboradorId, FechaHora, Dispositivo, TipoEvento, FuenteArchivo)
      VALUES
        (?, ?, ?, ?, ?, ?)
    ");

    while (($row = fgetcsv($fh, 0, $sep)) !== false) {
      $lineNo++;

      if (!$row || count($row) < 4) continue;

      // Header?
      if ($lineNo === 1) {
        $h = strtolower(trim((string)$row[0]));
        if (str_contains($h, 'id') || str_contains($h, 'usuario')) {
          continue;
        }
      }

      $codigo = trim((string)$row[0]);
      $fechaHoraStr = trim((string)$row[1]);
      $dispStr = trim((string)$row[2]);
      $tipoStr = trim((string)$row[3]);

      if ($codigo === '' || $fechaHoraStr === '' || $tipoStr === '') {
        $errors++;
        continue;
      }

      $codigo = strtoupper($codigo);

      if (!ctype_digit($tipoStr)) {
        $errors++;
        continue;
      }

      $tipo = (int)$tipoStr;
      if ($tipo < 0 || $tipo > 3) {
        $errors++;
        continue;
      }

      $disp = null;
      if ($dispStr !== '') $disp = (int)$dispStr;

      $dt = date_create($fechaHoraStr);
      if (!$dt) {
        $errors++;
        continue;
      }
      $fechaHora = $dt->format('Y-m-d H:i:s');

      $stFindColab->execute([$codigo]);
      $colabId = $stFindColab->fetchColumn();
      $colabId = $colabId !== false ? (int)$colabId : null;

      try {
        $stInsert->execute([$codigo, $colabId, $fechaHora, $disp, $tipo, $sourceName]);
        $inserted++;
      } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
          $skipped++;
          continue;
        }
        throw $e;
      }
    }

    fclose($fh);
    $pdo->commit();

    return ['inserted' => $inserted, 'skipped' => $skipped, 'errors' => $errors];

  } catch (Throwable $e) {
    fclose($fh);
    $pdo->rollBack();
    throw $e;
  }
}

/**
 * Reconciliar movimientos sin asociado:
 * - Busca reloj_movimientos.ColaboradorId IS NULL
 * - Asocia por CodigoReloj con colaboradores.CodigoReloj
 * Retorna cantidad actualizada.
 */
function reconciliar_reloj_movimientos(): int
{
  $sql = "
    UPDATE reloj_movimientos rm
    INNER JOIN colaboradores c
      ON c.CodigoReloj = rm.CodigoReloj
    SET rm.ColaboradorId = c.ColaboradorId
    WHERE rm.ColaboradorId IS NULL
      AND rm.CodigoReloj IS NOT NULL
      AND rm.CodigoReloj <> ''
  ";
  return DB::exec($sql);
}

function reloj_tipo_label(int $t): string
{
  return match ($t) {
    0 => 'ENTRADA',
    1 => 'SALIDA',
    2 => 'SALE ALMUERZO',
    3 => 'ENTRA ALMUERZO',
    default => 'OTRO',
  };
}
