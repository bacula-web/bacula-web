<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-present Davide Franco
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

use App\Table\UserTable;
use Core\App\UserAuth;
use Slim\Views\Twig;
use Core\Helpers\Sanitizer;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController
{
    /**
     * @var string
     */
    protected string $username = '';
    private Twig $view;
    private UserTable $userTable;
    private UserAuth $userAuth;
    private SessionInterface $session;

    /**
     * @param Twig $view
     * @param UserTable $userTable
     * @param UserAuth $userAuth
     * @param SessionInterface $session
     */
    public function __construct(Twig $view, UserTable $userTable, UserAuth $userAuth, SessionInterface $session)
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function prepare(Request $request, Response $response): Response
    {
        $tplData = [];
        $postData = $request->getParsedBody();

        $this->username = $this->session->get('username');
        $user = $this->userTable->findByName($this->username);

        $tplData['username'] = $this->username;
        $tplData['email'] = $user->getEmail();

        // Check if password reset have been requested
        if (isset($postData['action'])) {
            switch (Sanitizer::sanitize($postData['action'])) {
                case 'passwordreset':
                    // Check if provided current password is correct
                    if ($this->userAuth->authUser($user->getUsername(), $postData['oldpassword']) == 'yes') {
                        // Reset password
                        $result = $this->userTable->setPassword(
                            $user->getUsername(),
                            $postData['newpassword']
                        );

                        if ($result !== false) {
                            $this->session->getFlash()->set('info', ['Password successfully updated']);
                        } else {
                            $this->session->getFlash()->set('error', ['Password not updated']);
                        }
                    } else {
                        $this->session->getFlash()->set('error', ['Current password is not valid']);
                    }
                    break;
            }
        }

        return $this->view->render($response, 'pages/usersettings.html.twig', $tplData);
    }
}
