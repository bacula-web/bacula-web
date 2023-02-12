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
use Core\App\UserAuth;
use Core\Helpers\Sanitizer;
use Core\Utils\ConfigFileException;
use SmartyException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginController extends Controller
{
    /**
     * @return Response
     * @throws ConfigFileException
     * @throws SmartyException
     */
    public function prepare(): Response
    {
        $session = new Session();
        $dbAuth = new UserAuth();

        if ($this->request->request->has('action') ) {
            if( $this->request->request->get('action') === 'login') {
                $input_username = Sanitizer::sanitize($this->request->request->get('username'));
                $input_password = $this->request->request->get('password');

                $session->set(
                    'user_authenticated',
                    $dbAuth->authUser($input_username, $input_password)
                );

                if ($dbAuth->authenticated()) {
                    $username = Sanitizer::sanitize($this->request->request->get('username'));
                    $session->set('username', $username);

                    return new RedirectResponse('index.php');
                }
            } elseif ($this->request->request->get('action') === 'logout') {
                $this->setAlert('Successfully logged out');
                $this->setAlertType('success');

                $dbAuth->destroySession();

                return new RedirectResponse('index.php?page=login');
            }
        }

        return (new Response($this->render('login.tpl')));
    }
}
