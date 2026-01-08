<?php
declare(strict_types=1);

final class View {
  public static function render(string $path, array $data = []): void {
    extract($data, EXTR_SKIP);
    require __DIR__ . "/../../views/$path.php";
  }
}
