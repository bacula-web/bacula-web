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

namespace App\Controller;

use Core\App\Controller;
use App\Libs\FileConfig;
use App\Tables\UserTable;
use Core\Db\DatabaseFactory;
use Core\Helpers\Sanitizer;
use Core\Utils\ConfigFileException;
use Exception;
use SmartyException;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{
    /**
     * @return Response
     * @throws ConfigFileException
     * @throws SmartyException
     * @throws Exception
     */
    public function prepare(): Response
    {
        $userTable = new UserTable(DatabaseFactory::getDatabase());

        // Create new user
        if ($this->request->attributes->has('action')) {
            switch (Sanitizer::sanitize($this->request->request->get('action'))) {
                case 'createuser':
                    $username = Sanitizer::sanitize($this->request->request->get('username'));
                    $email = Sanitizer::sanitize($this->request->request->get('email'));

                    $result = $userTable->addUser(
                        $username,
                        $email,
                        $this->request->request->get('password')
                    );
                    if ($result !== false) {
                        // TODO: replace by session flash message
                        $this->setAlert($result->rowCount() . ' user created successfully');
                        $this->setAlertType('success');
                    }
                    break;
            }
        }

        // Get users list
        $this->setVar('users', $userTable->getAll());

        // Get parameters set in configuration file
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new ConfigFileException("The configuration file is missing");
        } else {
            // Check if datetime_format is set, otherwise, set default datetime_format
            if (FileConfig::get_Value('datetime_format') != null) {
                $this->setVar('config_datetime_format', FileConfig::get_Value('datetime_format'));
            } else {
                $this->setVar('config_datetime_format', 'Y-m-d H:i:s');
            }

            // Check if language is set
            if (FileConfig::get_Value('language') != null) {
                $this->setVar('config_language', FileConfig::get_Value('language'));
            }

            // Check if show_inactive_clients is set
            if (FileConfig::get_Value('show_inactive_clients') != null) {
                $config_show_inactive_clients = FileConfig::get_Value('show_inactive_clients');

                if ($config_show_inactive_clients == true) {
                    $this->setVar('config_show_inactive_clients', 'checked');
                }
            }

            // Check if hide_empty_pools is set
            if (FileConfig::get_Value('hide_empty_pools') != null) {
                $config_hide_empty_pools = FileConfig::get_Value('hide_empty_pools');

                if ($config_hide_empty_pools == true) {
                    $this->setVar('config_hide_empty_pools', 'checked');
                }
            } else {
                $this->setVar('config_hide_empty_pools', '');
            }

            // Parameter <enable_users_auth> is enabled by default (in case is not specified in config file)
            $config_enable_users_auth = true;

            // If enable_users_auth is defined in config file, take the value
            if (FileConfig::get_Value('enable_users_auth') !== null && is_bool(FileConfig::get_Value('enable_users_auth'))) {
                $config_enable_users_auth = FileConfig::get_Value('enable_users_auth');
            }

            if ($config_enable_users_auth == true) {
                $this->setVar('config_enable_users_auth', 'checked');
            }

            // Parameter <debug> is disabled by default (in case is not specified in config file)
            $config_debug = false;

            // If debug is defined in config file, take the value
            if (FileConfig::get_Value('debug') !== null && is_bool(FileConfig::get_Value('debug'))) {
                $config_debug = FileConfig::get_Value('debug');
            }

            if ($config_debug == true) {
                $this->setVar('config_debug', 'checked');
            } else {
                $this->setVar('config_debug', '');
            }
        }

        return (new Response($this->render('settings.tpl')));
    }
}
