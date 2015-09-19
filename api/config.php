<?php

$config = __DIR__ . '/../config/';

require_once $config . 'env.php';
require_once $config . 'path.php';
require_once $config . 'authinfo.php';
require_once $config . 'sources.php';

if (file_exists($config . 'parameters.php')) {
    require_once $config . 'parameters.php';
}