<?php

use Core\Helpers\Sanitizer;

/**
 * Copyright (C) 2010-2022 Davide Franco
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

class SettingsView extends CView
{
    public function __construct()
    {
        parent::__construct();

        $this->templateName = 'settings.tpl';
        $this->name = 'Settings';
        $this->title = 'General settings';
    }

    public function prepare()
    {
        $userauth = new UserAuth();

        // Create new user
        if (WebApplication::getRequest()->request->has('action')) {
            switch (Sanitizer::sanitize(WebApplication::getRequest()->request->get('action'))) {
                case 'createuser':
                    $username = Sanitizer::sanitize( WebApplication::getRequest()->request->get('username'));
                    $email = Sanitizer::sanitize(WebApplication::getRequest()->request->get('email'));
                    $userauth->addUser(
                        $username,
                        $email,
                        WebApplication::getRequest()->request->get('password')
                    );
                    break;
            }
        }

        // Get users list
        $this->assign('users', $userauth->getUsers());

        // Get parameters set in configuration file
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new Exception("The configuration file is missing");
        } else {

            // Check if datetime_format is set, otherwise, set default datetime_format
            if (FileConfig::get_Value('datetime_format') != null) {
                $this->assign('config_datetime_format', FileConfig::get_Value('datetime_format'));
            }else {
                $this->assign('config_datetime_format', 'Y-m-d H:i:s');
            }

            // Check if language is set
            if (FileConfig::get_Value('language') != null) {
                $this->assign('config_language', FileConfig::get_Value('language'));
            }

            // Check if show_inactive_clients is set
            if (FileConfig::get_Value('show_inactive_clients') != null) {
                $config_show_inactive_clients = FileConfig::get_Value('show_inactive_clients');

                if ($config_show_inactive_clients == true) {
                    $this->assign('config_show_inactive_clients', 'checked');
                }
            }

            // Check if hide_empty_pools is set
            if (FileConfig::get_Value('hide_empty_pools') != null) {
                $config_hide_empty_pools = FileConfig::get_Value('hide_empty_pools');

                if ($config_hide_empty_pools == true) {
                    $this->assign('config_hide_empty_pools', 'checked');
                }
            }else{
                $this->assign('config_hide_empty_pools', '');
            }

            // Parameter <enable_users_auth> is enabled by default (in case is not specified in config file)
            $config_enable_users_auth = true;

            // If enable_users_auth is defined in config file, take the value
            if (FileConfig::get_Value('enable_users_auth') !== null && is_bool(FileConfig::get_Value('enable_users_auth'))) {
                $config_enable_users_auth = FileConfig::get_Value('enable_users_auth');
            }
            
            if ($config_enable_users_auth == true) {
                $this->assign('config_enable_users_auth', 'checked');
            }

            // Parameter <debug> is disabled by default (in case is not specified in config file)
            $config_debug = false;

            // If debug is defined in config file, take the value
            if (FileConfig::get_Value('debug') !== null && is_bool(FileConfig::get_Value('debug'))) {
                $config_debug = FileConfig::get_Value('debug');
            }

            if ($config_debug == true) {
                $this->assign('config_debug', 'checked');
            }else {
                $this->assign('config_debug', '');
            }
        }
    }
} // end of class
