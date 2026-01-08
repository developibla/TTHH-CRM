<?php
declare(strict_types=1);

$cfg = require __DIR__ . '/config.php';

date_default_timezone_set($cfg['timezone']);

session_name($cfg['session_name']);
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/catalogos.php';
require_once __DIR__ . '/../middleware/auth.php';
