<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
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
use App\Libs\FileConfig;
use App\Middleware\CatalogSelectorMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\DbAuthMiddleware;
use App\Middleware\FlashMiddleware;
use App\Middleware\GuestMiddleware;
use Core\Utils\ExceptionRenderer;
use DI\ContainerBuilder;
use Odan\Session\Middleware\SessionStartMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap application
require_once __DIR__ . '/../core/bootstrap.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(CONFIG_DIR . 'container-bindings.php');
$container = $containerBuilder->build();

$app = $container->get(App::class);

FileConfig::open(CONFIG_FILE);

$basePath = FileConfig::get_Value('basepath') ?? null;
if (!is_null($basePath)) {
    $app->setBasePath($basePath);
}

$app->group('', function (RouteCollectorProxy $group) {
    $group->map(['GET', 'POST'], '/', [HomeController::class, 'prepare'])->setName('home');

    $group->map(['GET', 'POST'], '/jobs', [JobController::class, 'index'])->setName('jobs');
    $group->get('/joblog/{jobid}', [JobController::class, 'showLogs'])->setName('joblog');
    $group->map(['GET', 'POST'], '/jobfiles[/{jobid}[/{page}[/{filename}]]]', [JobController::class, 'showFiles'])->setName('jobfiles');

    $group->get('/pools', [PoolController::class, 'prepare']);

    $group->get('/test', [TestController::class, 'index']);

    $group->get('/settings', [SettingsController::class, 'index']);
    $group->post('/settings', [SettingsController::class, 'addUser'])->setName('adduser');

    $group->map(['GET', 'POST'], '/volumes', [VolumesController::class, 'index'])->setName('volumes');

    $group->get('/volume/{id}', [VolumesController::class, 'show'])->setName('volume_detail');

    $group->get('/directors', [DirectorController::class, 'index']);

    $group->map(['GET', 'POST'], '/backupjob', [BackupJobController::class, 'index'])->setName('backupjob');

    $group->map(['GET', 'POST'], '/client', [ClientController::class, 'index']);

    $group->map(['GET', 'POST'], '/user', [UserController::class, 'prepare'])->setName('user');

})->add(DbAuthMiddleware::class);

$app->group('', function (RouteCollectorProxy $group) {
    $group->post('/signout', [LoginController::class, 'signOut']);
    $group->get('/login', [LoginController::class, 'index']);
    $group->post('/login', [LoginController::class, 'login']);
})->add(GuestMiddleware::class);

$app->add(CsrfMiddleware::class)
    ->add('csrf')
    ->add(CatalogSelectorMiddleware::class)
    ->add(FlashMiddleware::class)
    ->add(TwigMiddleware::create($app, $container->get(Twig::class)))
    ->add(SessionStartMiddleware::class);

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(
    FileConfig::get_Value('debug') ?? false,
    FileConfig::get_Value('debug') ?? false,
    FileConfig::get_Value('debug') ?? false
);

$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', ExceptionRenderer::class);

$app->run();
