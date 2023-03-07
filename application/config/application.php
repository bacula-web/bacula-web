<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

/**
 * Bacula-Web Core Web App config file
 *
 * This config file contains Bacula-Web core web application settings
 * Important: This file should NOT be modified, except for developpment purpose
 *
 * @author Davide Franco
 * @since 8.0.0-rc.1
 */

return [
    'name' => 'Bacula-Web',
    'version' => '8.8.0',
    'routes' => [
        'home' => [
            'callback' => \App\Controller\HomeController::class,
            'name' => 'Dashboard'
        ],
        'test' => [
            'callback' => \App\Controller\TestController::class,
            'name' => 'Test'
        ],
        'jobs' => [
            'callback' => \App\Controller\JobController::class,
            'name' => 'Jobs'
        ],
        'joblogs' => [
            'callback' => \App\Controller\JobLogController::class,
            'name' => 'Job logs'
        ],
        'volumes' => [
            'callback' => \App\Controller\VolumesController::class,
            'name' => 'Volumes'
        ],
        'volume' => [
            'callback' => \App\Controller\VolumeController::class,
            'name' => 'Volume details'
        ],
        'pools' => [
            'callback' => \App\Controller\PoolController::class,
            'name' => 'Pools'
        ],
        'client' => [
            'callback' => \App\Controller\ClientController::class,
            'name' => 'Clients'
        ],
        'backupjob' => [
            'callback' => \App\Controller\BackupJobController::class,
            'name' => 'Backup job'
        ],
        'login' => [
            'callback' => \App\Controller\LoginController::class,
            'name' => 'Login'
        ],
        'usersettings' => [
            'callback' => \App\Controller\UserController::class,
            'name' => 'User settings'
        ],
        'settings' => [
            'callback' => \App\Controller\SettingsController::class,
            'name' => 'Settings'
        ],
        'directors' => [
            'callback' => \App\Controller\DirectorController::class,
            'name' => 'Directors'
        ],
        'jobfiles' => [
            'callback' => \App\Controller\JobFilesController::class,
            'name' => 'Job files'
        ]
    ],
    'fallback_controller' => [
        'callback' => \App\Controller\HomeController::class
    ]
];
