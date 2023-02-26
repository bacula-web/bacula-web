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
use Core\Exception\AppException;
use Core\Helpers\Sanitizer;
use Core\Exception\ConfigFileException;
use SmartyException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Valitron\Validator;

class LoginController extends Controller
{
    /**
     * @return Response
     * @throws ConfigFileException
     * @throws SmartyException
     * @throws AppException
     */
    public function prepare(): Response
    {
        $dbAuth = new UserAuth();

        if ($this->request->request->has('action') ) {
            if( $this->request->request->get('action') === 'login') {

                $form_data = [
                    'username' => Sanitizer::sanitize($this->request->request->get('username')),
                    'password' => $this->request->request->get('password')
                ];

                $v = new Validator($form_data);

                $v->rules([
                   'required' => [
                       'username', 'password'
                   ],
                    'alphaNum' => ['username'],
                    'lengthMin' => ['password', 8]
                ]);

                if (!$v->validate()) {
                    throw new AppException('Missing or invalid username and password');
                }

                $this->session->set('user_authenticated', $dbAuth->authUser($form_data['username'], $form_data['password']));

                if ($dbAuth->authenticated()) {
                    $username = Sanitizer::sanitize($this->request->request->get('username'));
                    $this->session->set('username', $username);

                    // TODO: This flash message does not appear everytime, to be investigated
                    $this->setFlash('success', "Successfully authenticated");

                    return new RedirectResponse('index.php');
                }
            } elseif ($this->request->request->get('action') === 'logout') {
                $this->setAlert('Successfully logged out');
                $this->setAlertType('success');

                $dbAuth->destroySession();

                // TODO: This flash message does not appear everytime, to be investigated
                $this->setFlash('success', "Successfully sign-out");

                return new RedirectResponse('index.php?page=login');
            }
        }

        return (new Response($this->render('login.tpl')));
    }
}
