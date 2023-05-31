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
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Symfony\Component\HttpFoundation\Session\Session;

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap application
require_once __DIR__ . '/../core/bootstrap.php';

// TODO: replace below code by middleware
$session = new Session();
if (!$session->isStarted()) {
    $session->start();
}

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

$app->map(['GET', 'POST'], '/', [HomeController::class, 'prepare']);

$app->map(['GET', 'POST'],'/jobs[/{page}]', [JobController::class, 'index']);
$app->get('/joblog/{jobid}', [JobController::class, 'showLogs']);
$app->map(['GET', 'POST'],'/jobfiles/{jobid}', [JobController::class, 'showFiles']);

$app->get('/pools', [PoolController::class, 'prepare']);

$app->get('/test', [TestController::class, 'index']);

$app->get('/settings', [SettingsController::class, 'index']);
$app->post('/settings', [SettingsController::class, 'addUser']);

$app->map(['GET', 'POST'], '/volumes', [VolumesController::class, 'index']);
$app->get('/volumes/{id}', [VolumesController::class, 'show']);

$app->get('/directors', [DirectorController::class, 'index'] );

$app->map(['GET', 'POST'], '/backupjob', [BackupJobController::class, 'index']);

$app->map(['GET', 'POST'], '/client', [\App\Controller\ClientController::class, 'index']);

$app->map(['GET', 'POST'], '/user', [UserController::class, 'prepare']);

$app->post('/signout', [LoginController::class, 'signOut']);
$app->get('/login', [LoginController::class, 'index']);
$app->post('/login', [LoginController::class, 'login']);

$app->run();
