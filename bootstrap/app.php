<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Router;
use App\Core\Session;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();

$appConfig = require BASE_PATH . '/config/app.php';
$securityConfig = require BASE_PATH . '/config/security.php';
$sessionConfig = require BASE_PATH . '/config/session.php';

date_default_timezone_set($appConfig['timezone']);
Session::start($sessionConfig);

$router = new Router();
require BASE_PATH . '/routes/web.php';

$app = new App($router, $securityConfig['headers']);
