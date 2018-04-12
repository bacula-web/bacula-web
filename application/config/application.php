<?php

$app = [ 'name' => 'Bacula-Web', 'version' => '8.0.0-rc3',
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
						'historyfiles' => 'HistoryFiles'
			],
    'defaultview' => 'DashboardView' ];
