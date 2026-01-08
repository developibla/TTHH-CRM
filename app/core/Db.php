<?php
declare(strict_types=1);

final class DB {
  private static ?PDO $pdo = null;

  public static function pdo(): PDO {
    if (self::$pdo) return self::$pdo;

    $cfg = require __DIR__ . '/../config/database.php';
    $dsn = sprintf(
      'mysql:host=%s;port=%d;dbname=%s;charset=%s',
      $cfg['host'], (int)$cfg['port'], $cfg['dbname'], $cfg['charset']
    );

    self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return self::$pdo;
  }

  public static function fetchOne(string $sql, array $params = []): ?array {
    $st = self::pdo()->prepare($sql);
    $st->execute($params);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function fetchAll(string $sql, array $params = []): array {
    $st = self::pdo()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
  }

  public static function exec(string $sql, array $params = []): int {
    $st = self::pdo()->prepare($sql);
    $st->execute($params);
    return $st->rowCount();
  }

  public static function lastId(): string {
    return self::pdo()->lastInsertId();
  }
  
}
