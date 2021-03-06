<?php
declare(strict_types=1);

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\TwigMiddleware;
use Odan\Session\Middleware\SessionMiddleware;
use App\Middleware\EnforceHttpsMiddleware;

return function (App $app, array $settings) {
    $app->add(TwigMiddleware::class);
    $app->add(SessionMiddleware::class);
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(MethodOverrideMiddleware::class);
    $app->add(ErrorMiddleware::class);

    if ($settings['enforce_https']) {
        $app->add(EnforceHttpsMiddleware::class);
    }
};
