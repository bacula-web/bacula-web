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

use App\Tables\UserTable;
use Core\App\Controller;
use Core\App\UserAuth;
use Core\Db\DatabaseFactory;
use Core\Helpers\Sanitizer;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    /**
     * @var string
     */
    protected string $username = '';

    /**
     * @return Response
     * @throws Exception
     */
    public function prepare(): Response
    {
        $userTable = new UserTable(DatabaseFactory::getDatabase());

        $userauth = new UserAuth();

        $this->username = $this->session->get('username');
        $user = $userTable->findByName($this->username);

        $this->setVar('username', $user->getUsername());
        $this->setVar('email', $user->getEmail());

        // Check if password reset have been requested
        if ($this->request->request->has('action')) {
            switch (Sanitizer::sanitize($this->request->request->get('action'))) {
                case 'passwordreset':
                    // Check if provided current password is correct
                    if ($userauth->authUser($user->getUsername(), $this->request->request->get('oldpassword')) == 'yes') {
                        // Update user password with new one
                        $result = $userTable->setPassword(
                            $user->getUsername(),
                            $this->request->request->get('newpassword')
                        );

                        if ($result !== false) {
                            $this->userAlert = 'Password successfully updated';
                            $this->userAlertType = 'success';
                        } else {
                            // TODO: do we need to check something here ?
                        }
                    } else {
                        $this->userAlert = 'Current password do not match';
                        $this->userAlertType = 'danger';
                    }
                    break;
            }
        }

        return (new Response($this->render('usersettings.tpl')));
    }
}
