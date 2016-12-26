<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2004 Juan Luis Frances Jimenez							   |
 | Copyright 2010-2016, Davide Franco			                           |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
*/

session_start();
require_once("core/global.inc.php");

// Initialise model and view
$dbSql = null;
$view = new CView();

try {
    $dbSql = new Bweb($view);
} catch (Exception $e) {
    CErrorHandler::displayError($e);
}

// Installed PDO drivers
$pdo_drivers = PDO::getAvailableDrivers();

// Check result icon
//$icon_result = array( true => 'ok.png', false => 'error.png');
$icon_result = array( true => 'glyphicon-ok', false => 'glyphicon-remove');

// Checks list
$check_list = array(
    array('check_cmd' => 'php-gettext',
        'check_label' => 'PHP - Gettext support',
        'check_descr' => 'If you want Bacula-web in your language, please compile PHP with Gettext support'),
    array('check_cmd' => 'php-session',
        'check_label' => 'PHP - Session support',
        'check_descr' => 'PHP session support is required'),
    array('check_cmd' => 'php-gd',
        'check_label' => 'PHP - GD support',
        'check_descr' => 'This is required by phplot, please compile PHP with GD support'),
    array('check_cmd' => 'php-mysql',
        'check_label' => 'PHP - MySQL support',
        'check_descr' => 'PHP MySQL support must be installed in order to run bacula-web with MySQL bacula catalog'),
    array('check_cmd' => 'php-postgres',
        'check_label' => 'PHP - PostgreSQL support',
        'check_descr' => 'PHP PostgreSQL support must be installed in order to run bacula-web with PostgreSQL bacula catalog'),
    array('check_cmd' => 'php-sqlite',
        'check_label' => 'PHP - SQLite support',
        'check_descr' => 'PHP SQLite support musts be installed in order to run bacula-web with SQLite bacula catalog'),
    array('check_cmd' => 'php-pdo',
        'check_label' => 'PHP - PDO support',
        'check_descr' => 'PHP PDO support is required, please compile PHP with this option'),
    array('check_cmd' => 'db-connection',
        'check_label' => 'Database connection status (MySQL and postgreSQL only)',
        'check_descr' => 'Current status: ' . CDBUtils::getConnectionStatus($dbSql->db_link) ),
    array('check_cmd' => 'smarty-cache',
        'check_label' => 'Smarty cache folder write permission',
        'check_descr' => realpath(VIEW_CACHE_DIR) . ' must be writable by Apache'),
    array('check_cmd' => 'php-version',
        'check_label' => 'PHP version',
        'check_descr' => 'PHP version must be at least 5.3 (current version = ' . PHP_VERSION . ')'),
    array('check_cmd' => 'php-timezone',
        'check_label' => 'PHP timezone',
        'check_descr' => 'Timezone must be configured in php.ini (current timezone = ' . ini_get('date.timezone') . ')')
);

// Doing all checks
foreach ($check_list as &$check) {
    switch ($check['check_cmd']) {
        case 'php-session':
            $check['check_result'] = $icon_result[function_exists('session_start')];
            break;
        case 'php-gettext':
            $check['check_result'] = $icon_result[function_exists('gettext')];
            break;
        case 'php-gd':
            $check['check_result'] = $icon_result[function_exists('gd_info')];
            break;
        case 'pear-db':
            $check['check_result'] = $icon_result[class_exists('DB')];
            break;
        case 'php-mysql':
            $check['check_result'] = $icon_result[in_array('mysql', $pdo_drivers)];
            break;
        case 'php-postgres':
            $check['check_result'] = $icon_result[in_array('pgsql', $pdo_drivers)];
            break;
        case 'php-sqlite':
            $check['check_result'] = $icon_result[in_array('sqlite', $pdo_drivers)];
            break;
        case 'php-pdo':
            $check['check_result'] = $icon_result[class_exists('PDO')];
            break;
        case 'smarty-cache':
            $check['check_result'] = $icon_result[is_writable(VIEW_CACHE_DIR)];
            break;
        case 'php-version':
            $check['check_result'] = $icon_result[version_compare(PHP_VERSION, '5.4', '>=')];
            break;
        case 'db-connection':
            $check['check_result'] = $icon_result[ CDBUtils::isConnected($dbSql->db_link)];
            break;
        case 'php-timezone':
            $timezone = ini_get('date.timezone');
            if (!empty($timezone)) {
                $check['check_result'] = $icon_result[true];
            } else {
                $check['check_result'] = $icon_result[false];
            }
            break;
    }
}

// Testing graph capabilities
$data = array(
    array('test1', 100),
    array('test2', 150),
    array('test3', 180),
    array('test4', 456)
);

// Pie graph
$graph = new Highcharts('pie_graph', 'pie', 'Pie Graph', $data, null, array('showLegend' => 1));
$view->assign('pie_graph_js', $graph->get_graph_js());
unset($graph);

// Bar graph
$graph = new Highcharts('bar_graph', 'column', 'Bar Graph', $data, null, array('colorByPoint' => 0));
$view->assign('bar_graph_js', $graph->get_graph_js());
unset($graph);

// Set page name
$current_page = 'Test page';
$view->assign('page_name', $current_page);
 
// Template rendering
$view->assign('checks', $check_list);
$view->display('test.tpl');
