<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of Bacula-Web.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

use App\Controller\BackupJobController;
use App\Controller\ClientController;
use App\Controller\DirectorController;
use App\Controller\HomeController;
use App\Controller\JobController;
use App\Controller\LoginController;
use App\Controller\PoolController;
use App\Controller\SettingsController;
use App\Controller\TestController;
use App\Controller\UserController;
use App\Controller\VolumesController;
use App\Libs\Config;
use App\Middleware\CatalogSelectorMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\DbAuthMiddleware;
use App\Middleware\FlashMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\HttpHeadersMiddleware;
use App\Middleware\RefererMiddleware;
use App\Middleware\TrailingSlashMiddleware;
use App\Middleware\ValidationErrorMiddleware;
use App\Middleware\ValidationExceptionMiddleware;
use Core\Exception\ConfigFileException;
use Core\Utils\ExceptionRenderer;
use DI\Bridge\Slim\Bridge;
use Odan\Session\Middleware\SessionStartMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap application
require_once __DIR__ . '/../core/bootstrap.php';

$container = require dirname(__DIR__) . '/config/container/container.php';
$app = Bridge::create($container);

/**
 * Catch ConfigFileException before Slim's exception handler
 * This avoids having an "Uncaught exception" ugly error if
 * config.php is missing.
 */
try {
    $basePath = $container->get(Config::class)->get('basepath', null);
    if (!is_null($basePath)) {
        $app->setBasePath($basePath);
    }
} catch (ConfigFileException $e) {
    $exceptionRenderer = new ExceptionRenderer();
    http_response_code(500);
    die($exceptionRenderer($e, false));
}

$app->group('', function (RouteCollectorProxy $group) {
    $group->map(['GET', 'POST'], '/', [HomeController::class, 'prepare'])->setName('home');

    $group->map(['GET', 'POST'], '/jobs', [JobController::class, 'index'])->setName('jobs');
    $group->get('/joblog/{id}', [JobController::class, 'showLogs'])->setName('joblog');
    //$group->map(['GET', 'POST'], '/jobfiles[/{jobid}[/{page}[/{filename}]]]', [JobController::class, 'showFiles'])->setName('jobfiles');
    $group->map(['GET', 'POST'], '/job/{id}/files', [JobController::class, 'showFiles'])->setName('jobfiles');

    $group->get('/pools', [PoolController::class, 'prepare'])->setName('pools');

    $group->get('/test', [TestController::class, 'index']);

    $group->get('/settings', [SettingsController::class, 'index']);
    $group->post('/settings', [SettingsController::class, 'addUser'])->setName('adduser');

    $group->map(['GET', 'POST'], '/volumes', [VolumesController::class, 'index'])->setName('volumes');

    $group->get('/volume/{id}', [VolumesController::class, 'show'])->setName('volume_detail');

    $group->get('/directors', [DirectorController::class, 'index']);

    $group->map(['GET', 'POST'], '/backupjob', [BackupJobController::class, 'index'])->setName('backupjob');

    $group->map(['GET', 'POST'], '/client', [ClientController::class, 'index'])->setName('app_client_report');

    $group->get( '/user', [UserController::class, 'index'])->setName('user.profile');

    $group->get('/user/reset-password', [UserController::class, 'passwordReset'])->setName('user.password-reset');
    $group->post('/user/reset-password', [UserController::class, 'attemptPasswordReset'])->setName('user.attempt-password-reset');
})->add(DbAuthMiddleware::class);

$app->group('', function (RouteCollectorProxy $group) {
    $group->post('/signout', [LoginController::class, 'signOut']);
    $group->get('/login', [LoginController::class, 'index']);
    $group->post('/login', [LoginController::class, 'login']);
})->add(GuestMiddleware::class);

$app
    ->add(HttpHeadersMiddleware::class)
    ->add(CsrfMiddleware::class)
    ->add(CatalogSelectorMiddleware::class)
    ->add(RefererMiddleware::class)
    ->add(TrailingSlashMiddleware::class)
    ->add('csrf')
    ->add(FlashMiddleware::class)
    ->add(ValidationErrorMiddleware::class)
    ->add(ValidationExceptionMiddleware::class)
    ->add(TwigMiddleware::create($app, $container->get(Twig::class)))
    ->add(SessionStartMiddleware::class)
    ->addRoutingMiddleware();

// Add Error Middleware
$isDebug = $container->get(Config::class)->get('debug', false);
$errorMiddleware = $app->addErrorMiddleware($isDebug, $isDebug, $isDebug);

$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', ExceptionRenderer::class);

$app->run();
