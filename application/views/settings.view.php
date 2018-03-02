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

    }
} // end of class
        
