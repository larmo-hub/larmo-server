<?php

$config = __DIR__ . '/../config/';

require_once $config . 'env.php';
require_once $config . 'path.php';
require_once $config . 'authfile.php';

if (file_exists($config . 'parameters.php')) {
    require_once $config . 'parameters.php';
}