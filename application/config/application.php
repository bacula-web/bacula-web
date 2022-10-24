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

$app = [ 'name' => 'Bacula-Web', 'version' => '8.6.2',
        'routes' => [   'home' => 'Dashboard',
                        'test' => 'Test',
                        'jobs' => 'Jobs',
                        'joblogs' => 'JobLogs',
                        'volumes' => 'Volumes',
                        'pools' => 'Pools',
                        'client' => 'Client',
                        'backupjob' => 'BackupJob',
                        'login' => 'Login',
                        'usersettings' => 'UserSettings',
                        'settings' => 'Settings',
                        'directors' => 'Directors',
                        'jobfiles' => 'JobFiles'
            ],
    'defaultview' => 'DashboardView' ];

return $app;
