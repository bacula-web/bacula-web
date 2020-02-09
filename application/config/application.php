<?php

/**
 * Bacula-Web Core Web App config file
 *
 * This config file contains Bacula-Web core web application settings
 * Do not modify it unless you know what you're doing.
 *
 * @copyright 2010-2020 Davide Franco
 * @author Davide Franco
 * @since 8.0.0-rc.1
 */


$app = [ 'name' => 'Bacula-Web', 'version' => '8.3.3',
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
