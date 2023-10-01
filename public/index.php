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
use App\Controller\DirectorController;
use App\Controller\HomeController;
use App\Controller\JobController;
use App\Controller\LoginController;
use App\Controller\PoolController;
use App\Controller\SettingsController;
use App\Controller\TestController;
use App\Controller\UserController;
use App\Controller\VolumesController;
use App\Middleware\DbAuthMiddleware;
use App\Middleware\FlashMiddleware;
use App\Middleware\GuestMiddleware;
use DI\ContainerBuilder;
use Odan\Session\Middleware\SessionStartMiddleware;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap application
require_once __DIR__ . '/../core/bootstrap.php';

$containerbuilder = new ContainerBuilder();
$containerbuilder->addDefinitions(CONFIG_DIR . 'container-bindings.php');

$container = $containerbuilder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addErrorMiddleware(
    true,
    true,
    true
);

$app->group('', function(\Slim\Routing\RouteCollectorProxy $group) {
    $group->map(['GET', 'POST'], '/', [HomeController::class, 'prepare']);

    $group->map(['GET', 'POST'], '/jobs', [JobController::class, 'index']);
    $group->get('/joblog/{jobid}', [JobController::class, 'showLogs']);
    $group->map(['GET', 'POST'], '/jobfiles[/{jobid}[/{page}[/{filename}]]]', [JobController::class, 'showFiles']);

    $group->get('/pools', [PoolController::class, 'prepare']);

    $group->get('/test', [TestController::class, 'index']);

    $group->get('/settings', [SettingsController::class, 'index']);
    $group->post('/settings', [SettingsController::class, 'addUser']);

    $group->map(['GET', 'POST'], '/volumes', [VolumesController::class, 'index']);
    $group->get('/volumes/{id}', [VolumesController::class, 'show']);

    $group->get('/directors', [DirectorController::class, 'index']);

    $group->map(['GET', 'POST'], '/backupjob', [BackupJobController::class, 'index']);

    $group->map(['GET', 'POST'], '/client', [\App\Controller\ClientController::class, 'index']);

    $group->map(['GET', 'POST'], '/user', [UserController::class, 'prepare']);

})->add(DbAuthMiddleware::class);

$app->group('', function(\Slim\Routing\RouteCollectorProxy $group) {
    $group->post('/signout', [LoginController::class, 'signOut']);
    $group->get('/login', [LoginController::class, 'index']);
    $group->post('/login', [LoginController::class, 'login']);
})->add(GuestMiddleware::class);

$app->add(FlashMiddleware::class)
    ->add(TwigMiddleware::create($app, $container->get(Twig::class)))
    ->add(SessionStartMiddleware::class);

$app->run();
