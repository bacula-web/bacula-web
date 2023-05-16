<?php

/**
 * Copyright (C) 2004 Juan Luis Frances Jimenez
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

namespace App\Controller;

use Core\App\Controller;
use App\Tables\CatalogTable;
use Core\Exception\AppException;
use Core\Exception\ConfigFileException;
use PDO;
use Core\Graph\Chart;
use SmartyException;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    /**
     * @param CatalogTable $catalogTable
     * @return Response
     * @throws SmartyException
     * @throws AppException
     * @throws ConfigFileException
     */
    public function prepare(CatalogTable $catalogTable): Response
    {
        // Installed PDO drivers
        $pdo_drivers = PDO::getAvailableDrivers();

        // Check result icon
        //$icon_result = array( true => 'ok.png', false => 'error.png');
        $icon_result = array( true => 'glyphicon-ok', false => 'glyphicon-remove');

        // Checks list
        $check_list = array(
            array(  'check_cmd' => 'php-gettext',
                    'check_label' => 'PHP - Gettext support',
                    'check_descr' => 'If you want Bacula-web in your language, please compile PHP with Gettext support'),
            array(  'check_cmd' => 'php-session',
                    'check_label' => 'PHP - Session support',
                    'check_descr' => 'PHP session support is required'),
            array(  'check_cmd' => 'php-mysql',
                    'check_label' => 'PHP - MySQL support',
                    'check_descr' => 'PHP MySQL support must be installed in order to run bacula-web with MySQL bacula catalog'),
            array(  'check_cmd' => 'php-postgres',
                    'check_label' => 'PHP - PostgreSQL support',
                    'check_descr' => 'PHP PostgreSQL support must be installed in order to run bacula-web with PostgreSQL bacula catalog'),
            array(  'check_cmd' => 'php-sqlite',
                    'check_label' => 'PHP - SQLite support',
                    'check_descr' => 'PHP SQLite support must be installed to use SQLite bacula catalog and for Bacula-Web back-end'),
            array(  'check_cmd' => 'php-pdo',
                    'check_label' => 'PHP - PDO support',
                    'check_descr' => 'PHP PDO support is required, please compile PHP with this option'),
            array(  'check_cmd' => 'php-posix',
                    'check_label' => 'PHP - Posix support',
                    'check_descr' => 'PHP Posix support is required, please compile PHP with this option'),
            array(  'check_cmd' => 'db-connection',
                    'check_label' => 'Database connection status (MySQL and postgreSQL only)',
                    'check_descr' => 'Current status: ' . $catalogTable->getConnectionStatus() ),
            array(  'check_cmd' => 'smarty-cache',
                    'check_label' => 'Smarty cache folder write permission',
                    'check_descr' => $this->view->getCacheDir() . ' must be writable by Apache'),
            array(  'check_cmd' => 'users-db',
                    'check_label' => 'Protected assets folder write permission',
                    'check_descr' => 'application/assets/protected folder must be writable by Apache'),
            array(  'check_cmd' => 'php-version',
                    'check_label' => 'PHP version',
                    'check_descr' => 'PHP version must be at least 7.4 (current version = ' . PHP_VERSION . ')'),
            array(  'check_cmd' => 'php-timezone',
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
                case 'php-posix':
                    $check['check_result'] = $icon_result[function_exists('posix_getpwuid')];
                    break;
                case 'smarty-cache':
                    $check['check_result'] = $icon_result[is_writable($this->view->getCacheDir())];
                    break;
                case 'users-db':
                    $check['check_result'] = $icon_result[is_writable(BW_ROOT . '/application/assets/protected')];
                    break;
                case 'php-version':
                    $check['check_result'] = $icon_result[version_compare(PHP_VERSION, '7.4', '>=')];
                    break;
                case 'db-connection':
                    $check['check_result'] = $icon_result[$catalogTable->isConnected()];
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
        $data = array( array('test', 100),
            array('test1', 150),
            array('test2', 180),
            array('test3', 270),
            array('test4', 456)
        );

        // Dummy Pie chart
        $pie_chart = new Chart(array(   'type' => 'pie',
            'name' => 'chart_pie_test',
            'data' => $data ));

        $this->setVar('pie_graph_id', $pie_chart->name);
        $this->setVar('pie_graph', $pie_chart->render());

        unset($pie_chart);

        // Dummy bar graph
        $bar_chart = new Chart(array(   'type' => 'bar',
            'name' => 'chart_bar_test',
            'data' => $data,
            'ylabel' => 'Coffee cups' ));

        $this->setVar('bar_chart_id', $bar_chart->name);
        $this->setVar('bar_chart', $bar_chart->render());

        unset($bar_chart);

        // Template rendering
        $this->setVar('checks', $check_list);

        return new Response($this->render('test.tpl'));
    }
}
