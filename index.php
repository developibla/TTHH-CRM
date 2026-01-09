<?php
declare(strict_types=1);

session_start();

$cfg = require __DIR__ . '/app/config/config.php';
date_default_timezone_set($cfg['timezone']);

require __DIR__ . '/app/core/DB.php';
require __DIR__ . '/app/core/Helpers.php';
require __DIR__ . '/app/core/Csrf.php';
require __DIR__ . '/app/core/View.php';
require __DIR__ . '/app/core/Auth.php';
require __DIR__ . '/app/core/Catalogos.php';
require __DIR__ . '/app/core/Router.php';
require __DIR__ . '/app/core/ImportGeo.php';
require __DIR__ . '/app/core/Colaboradores.php';


Router::dispatch();
