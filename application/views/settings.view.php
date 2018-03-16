<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright 2010-2018, Davide Franco			                           |
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

class SettingsView extends CView {

    public function __construct() {
        $this->templateName = 'settings.tpl';
        $this->name = 'Settings';
        $this->title = 'General settings';

        parent::init();
    }

    public function prepare() {

        $userauth = new UserAuth(); 

        // Create new user
        if( isset( $_REQUEST['action'])) {
            switch($_REQUEST['action']) {
                case 'createuser':
                    $username = filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                    $email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $userauth->addUser( $username, $email, $_REQUEST['password']);
                    break;
            }
        }

        // Get users list
        $this->assign('users', $userauth->getUsers());

        // Get parameters set in configuration file 
        if (!FileConfig::open(CONFIG_FILE)) {
            throw new Exception("The configuration file is missing");
        } else {

            // Check if datetime_format is set
            if( FileConfig::get_Value('datetime_format') != NULL) {
                $this->assign( 'config_datetime_format',  FileConfig::get_Value('datetime_format'));
            }

            // Check if language is set
            if( FileConfig::get_Value('language') != NULL) {
                $this->assign( 'config_language',  FileConfig::get_Value('language'));
            }

            // Check if jobs_per_page is set
            if( FileConfig::get_Value('jobs_per_page') != NULL) {
                $this->assign( 'config_jobsperpage',  FileConfig::get_Value('jobs_per_page'));
            }

            // Check if show_inactive_clients is set
            if( FileConfig::get_Value('show_inactive_clients') != NULL) {

                $config_show_inactive_clients = FileConfig::get_Value('show_inactive_clients');

                if($config_show_inactive_clients == true) {
                    $this->assign( 'config_show_inactive_clients', 'checked');
                }
            }

            // Check if hide_empty_pools is set
            if( FileConfig::get_Value('hide_empty_pools') != NULL) {

                $config_hide_empty_pools = FileConfig::get_Value('hide_empty_pools');

                if($config_hide_empty_pools == true) {
                    $this->assign( 'config_hide_empty_pools', 'checked');
                }
            }
        }
    }
} // end of class
        
