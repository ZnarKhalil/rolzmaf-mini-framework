<?php

use Core\Routing\Router;
use App\Controllers\ExampleController;
use Core\Middleware\System\CsrfMiddleware;
use Core\Middleware\System\SessionMiddleware;

return function (Router $router) {
    $router->addGlobalMiddleware(SessionMiddleware::class);
    $router->addGlobalMiddleware(CsrfMiddleware::class);

    $router->get('/', [ExampleController::class, 'index'])
           ->middleware();

    $router->get('/about', [ExampleController::class, 'about'])
           ->middleware();
};