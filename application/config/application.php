<?php

/**
 * Bacula-Web Core Web App config file
 *
 * This config file contains Bacula-Web core web application settings
 * Important: This file should NOT be modified, except for developpment purpose
 *
 * @copyright 2010-2021 Davide Franco
 * @author Davide Franco
 * @since 8.0.0-rc.1
 */


$app = [ 'name' => 'Bacula-Web', 'version' => '8.4.3',
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
