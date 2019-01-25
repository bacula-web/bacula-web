<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright 2010-2019, Davide Franco			                           |
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

class UserSettingsView extends CView {

    protected $userauth;

    public function __construct() {
        $this->templateName = 'usersettings.tpl';
        $this->name = 'User settings';
        $this->title = '';

        $this->userauth = new UserAuth();

        parent::init();
    }

    public function prepare() {

        $this->username = $_SESSION['username'];
        $this->assign( 'username', $this->username);

        $user = $this->userauth->getData($this->username);
        $this->assign( 'email', $user['email']);

        // Check if password reset have been requested
        if( isset( $_REQUEST['action'])) {

            switch($_REQUEST['action']) {
            case 'passwordreset':
                // Check if provided current password is correct
                if( $this->userauth->authUser( $_SESSION['username'], $_POST['oldpassword']) == 'yes') {

                    // Update user password with new one
                    if($this->userauth->setPassword($_SESSION['username'], $_POST['newpassword'])) {
                        $this->userAlert = 'Password successfuly updated';
                        $this->userAlertType = 'success';
                    }
                }else {
                    $this->userAlert = 'Current password do not match';
                    $this->userAlertType = 'danger';
                }
                break;
            }
        }

    }

} // end of class
        
