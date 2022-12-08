<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
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
    'version' => '8.6.3',
    'routes' => [
        'home' => [
            'callback' => \App\Controller\HomeController::class
        ],
        'test' => [
            'callback' => \App\Controller\TestController::class
        ],
        'jobs' => [
            'callback' => \App\Controller\JobController::class
        ],
        'joblogs' => [
            'callback' => \App\Controller\JobLogController::class
        ],
        'volumes' => [
            'callback' => \App\Controller\VolumeController::class
        ],
        'pools' => [
            'callback' => \App\Controller\PoolController::class
        ],
        'client' => [
            'callback' => \App\Controller\ClientController::class
        ],
        'backupjob' => [
            'callback' => \App\Controller\BackupJobController::class
        ],
        'login' => [
            'callback' => \App\Controller\LoginController::class
        ],
        'usersettings' => [
            'callback' => \App\Controller\UserController::class
        ],
        'settings' => [
            'callback' => \App\Controller\SettingsController::class
        ],
        'directors' => [
            'callback' => \App\Controller\DirectorController::class
        ],
        'jobfiles' => [
            'callback' => \App\Controller\JobFilesController::class
        ]
    ],
    'fallback_controller' => [
        'callback' => \App\Controller\HomeController::class
    ]
];
