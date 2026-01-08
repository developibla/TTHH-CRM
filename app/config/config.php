<?php
declare(strict_types=1);

return [
  'app_name' => 'TTHH - Gestión de Talento Humano',
  'base_url' => '', // si usas http://localhost/tthh -> deja '' y el router lo maneja por rutas ?r=
  'session_name' => 'TTHHSESS',
  'timezone' => 'America/Asuncion',

  'db' => [
    'host' => '127.0.0.1',
    'name' => 'tthh',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
  ],

  'security' => [
    'max_attempts' => 5,
    'lock_minutes' => 15,
  ],
];
