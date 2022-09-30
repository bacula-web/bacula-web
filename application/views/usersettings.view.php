<?php

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

use App\Tables\UserTable;
use Core\App\WebApplication;
use Core\App\UserAuth;
use Core\App\CView;
use Core\Db\DatabaseFactory;
use Core\Helpers\Sanitizer;

class UserSettingsView extends CView
{
    protected $username;

    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'usersettings.tpl';
        $this->name = 'User settings';
        $this->title = '';
        $this->username = '';
    }

    public function prepare()
    {
        $appDbBackend = BW_ROOT . '/application/assets/protected/application.db';
        $userTable = new UserTable(DatabaseFactory::getDatabase('sqlite:'.$appDbBackend));

        $appDbBackend = BW_ROOT . '/application/assets/protected/application.db';
        $userauth = new UserAuth(DatabaseFactory::getDatabase('sqlite:'.$appDbBackend));

        $this->username = $_SESSION['username'];
        $user = $userTable->findByName($this->username);

        $this->assign('username', $user->getUsername());
        $this->assign('email', $user->getEmail());

        // Check if password reset have been requested
        if(WebApplication::getRequest()->request->has('action')) {
            switch (Sanitizer::sanitize(WebApplication::getRequest()->request->get('action'))) {
            case 'passwordreset':
                // Check if provided current password is correct
                if ($userauth->authUser($user->getUsername(), WebApplication::getRequest()->request->get('oldpassword')) == 'yes') {

                    // Update user password with new one
                    if($userTable->setPassword( $user->getUsername(), WebApplication::getRequest()->request->get('newpassword'))) {
                        $this->userAlert = 'Password successfully updated';
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
