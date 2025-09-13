<?php


declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Core\Http\Request;
use Core\Kernel\HttpKernel;
use Core\Routing\Router;

$router = new Router();
(require __DIR__ . '/../routes/web.php')($router);

$request = new Request();
$kernel  = new HttpKernel($router);

$response = $kernel->handle($request);
$response->send();
