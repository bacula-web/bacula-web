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

namespace App\Views;

use Core\App\WebApplication;
use Core\App\UserAuth;
use Core\App\CView;

class UserSettingsView extends CView
{
    protected $userauth;
    protected $username;

    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'usersettings.tpl';
        $this->name = 'User settings';
        $this->title = '';
        $this->username = '';
        $this->userauth = new UserAuth();
    }

    public function prepare()
    {
        $this->username = $_SESSION['username'];
        $this->assign('username', $this->username);

        $user = $this->userauth->getData($this->username);
        $this->assign('email', $user['email']);

        // Check if password reset have been requested
        if(WebApplication::getRequest()->request->has('action')) {
            switch (Sanitizer::sanitize(WebApplication::getRequest()->request->get('action'))) {
            case 'passwordreset':
                // Check if provided current password is correct
                if ($this->userauth->authUser($_SESSION['username'], WebApplication::getRequest()->request->get('oldpassword')) == 'yes') {

                    // Update user password with new one
                    if ($this->userauth->setPassword($_SESSION['username'], WebApplication::getRequest()->request->get('newpassword'))) {
                        $this->userAlert = 'Password successfuly updated';
                        $this->userAlertType = 'success';
                    }
                } else {
                    $this->userAlert = 'Current password do not match';
                    $this->userAlertType = 'danger';
                }
                break;
            }
        }
    }
} // end of class
