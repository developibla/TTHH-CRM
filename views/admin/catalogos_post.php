<?php
declare(strict_types=1);

csrf_validate();

$key = (string)($_POST['t'] ?? '');
$cat = catalogo_get($key);

try {
  // Eliminar
  if (!empty($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    if ($id <= 0) throw new RuntimeException("ID inválido.");
    catalogo_delete($key, $id);

    $_SESSION['flash_ok'] = "Registro eliminado correctamente.";
    redirect('catalogos', ['t' => $key]);
  }

  // Guardar (insert/update)
  $id = (int)($_POST['id'] ?? 0);
  $data = [];

  foreach ($cat['fields'] as $field => $meta) {
    $data[$field] = $_POST[$field] ?? null;
  }
  $data['Activo'] = $_POST['Activo'] ?? 1;

  catalogo_save($key, $id > 0 ? $id : null, $data);

  $_SESSION['flash_ok'] = $id > 0 ? "Registro actualizado correctamente." : "Registro agregado correctamente.";
  redirect('catalogos', ['t' => $key]);

} catch (Throwable $e) {
  $_SESSION['flash_error'] = $e->getMessage();
  redirect('catalogos', ['t' => $key]);
}
