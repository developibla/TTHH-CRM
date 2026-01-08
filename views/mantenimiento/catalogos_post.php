<?php
declare(strict_types=1);

require_login();
csrf_check($_POST['_csrf'] ?? null);

$key = (string)($_POST['t'] ?? 'cargo');
$isAjax = (isset($_POST['_ajax']) && (string)$_POST['_ajax'] === '1')
       || (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== '');

function json_out(array $payload): void {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

// DELETE
if (!empty($_POST['delete_id'])) {
  $id = (int)$_POST['delete_id'];

  try {
    if ($id > 0) {
      catalogo_delete($key, $id);
    }

    if ($isAjax) {
      json_out([
        'ok' => true,
        'message' => 'Registro eliminado correctamente.',
        'id' => $id,
      ]);
    }

    $_SESSION['flash_ok'] = 'Registro eliminado correctamente.';
    redirect('index.php?r=catalogos&t=' . urlencode($key));

  } catch (Throwable $e) {
    $msg = 'Error al eliminar: ' . $e->getMessage();

    if ($isAjax) {
      json_out(['ok' => false, 'message' => $msg]);
    }

    $_SESSION['flash_error'] = $msg;
    redirect('index.php?r=catalogos&t=' . urlencode($key));
  }
}

// SAVE
$id = isset($_POST['id']) && (string)$_POST['id'] !== '' ? (int)$_POST['id'] : null;
$cat = catalogo_get($key);

$data = [];
foreach (array_keys($cat['fields']) as $f) {
  $data[$f] = trim((string)($_POST[$f] ?? ''));
}
$data['Activo'] = (int)($_POST['Activo'] ?? 1);

foreach ($cat['fields'] as $f => $meta) {
  if (!empty($meta['required']) && trim((string)($data[$f] ?? '')) === '') {
    $msg = "El campo $f es obligatorio.";
    if ($isAjax) json_out(['ok' => false, 'message' => $msg]);

    $_SESSION['flash_error'] = $msg;
    redirect('index.php?r=catalogos&t=' . urlencode($key));
  }
}

try {
  catalogo_save($key, $data, $id);

  // obtener id final si insert
  if (!$id) {
    $pk = $cat['pk'];
    $last = DB::fetchOne("SELECT `$pk` AS id FROM `{$cat['table']}` ORDER BY `$pk` DESC LIMIT 1");
    $id = (int)($last['id'] ?? 0);
  }

  if ($isAjax) {
    $rowDb = catalogo_find($key, (int)$id);

    $row = [
      'id' => (int)$id,
      'Activo' => (int)($rowDb['Activo'] ?? $data['Activo']),
    ];

    foreach (array_keys($cat['fields']) as $f) {
      $v = (string)($rowDb[$f] ?? $data[$f] ?? '');
      if (($cat['fields'][$f]['type'] ?? '') === 'time' && strlen($v) >= 5) $v = substr($v, 0, 5);
      $row[$f] = $v;
    }

    json_out([
      'ok' => true,
      'message' => 'Registro guardado correctamente.',
      'row' => $row,
      'fields' => array_keys($cat['fields']),
      't' => $key,
      'csrf' => csrf_token(),
    ]);
  }

  $_SESSION['flash_ok'] = 'Registro guardado correctamente.';
  redirect('index.php?r=catalogos&t=' . urlencode($key));

} catch (Throwable $e) {
  $msg = 'Error al guardar: ' . $e->getMessage();

  if ($isAjax) json_out(['ok' => false, 'message' => $msg]);

  $_SESSION['flash_error'] = $msg;
  redirect('index.php?r=catalogos&t=' . urlencode($key));
}
