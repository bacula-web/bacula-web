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
use Core\App\UserAuth;
use Core\App\View;
use Core\Helpers\Sanitizer;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use SmartyException;
use GuzzleHttp\Psr7\Response;

class UserController
{
    /**
     * @var string
     */
    protected string $username = '';
    private View $view;
    private UserTable $userTable;
    private UserAuth $userAuth;
    private SessionInterface $session;

    /**
     * @param View $view
     * @param UserTable $userTable
     * @param UserAuth $userAuth
     * @param SessionInterface $session
     */
    public function __construct(View $view, UserTable $userTable, UserAuth $userAuth, SessionInterface $session)
    {
        $this->view = $view;
        $this->userTable = $userTable;
        $this->userAuth = $userAuth;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws SmartyException
     */
    public function prepare(Request $request, Response $response): Response
    {
        $postData = $request->getParsedBody();

        $this->username = $this->session->get('username');
        $user = $this->userTable->findByName($this->username);

        $this->view->set('username', $user->getUsername());
        $this->view->set('email', $user->getEmail());

        // Check if password reset have been requested
        if (isset($postData['action'])) {
            switch (Sanitizer::sanitize($postData['action'])) {
                case 'passwordreset':
                    // Check if provided current password is correct
                    if ($this->userAuth->authUser($user->getUsername(), $postData['oldpassword']) == 'yes') {
                        // Update user password with new one
                        $result = $this->userTable->setPassword(
                            $user->getUsername(),
                            $postData['newpassword']
                        );

                        // TODO: fix flash message bloe
                        /**
                        if ($result !== false) {
                            $this->userAlert = 'Password successfully updated';
                            $this->userAlertType = 'success';
                        } else {
                            // TODO: do we need to check something here ?
                        }
                         */
                    } else {
                         /**
                        $this->userAlert = 'Current password do not match';
                        $this->userAlertType = 'danger';
                          * */
                    }
                    break;
            }
        }

        $response->getBody()->write($this->view->render('usersettings.tpl'));
        return $response;
    }
}
