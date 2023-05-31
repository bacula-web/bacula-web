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

use Core\App\UserAuth;
use Core\App\View;
use Core\Exception\AppException;
use Core\Helpers\Sanitizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Valitron\Validator;

class LoginController
{

    private View $view;
    private UserAuth $userAuth;
    private Session $session;

    public function __construct(View $view, UserAuth $userAuth)
    {
        $this->view = $view;
        $this->userAuth = $userAuth;
        $this->session = new Session();
    }

    public function signOut(Request $request, Response $response)
    {
        //TODO: fix flash message
        /**
        $this->setAlert('Successfully logged out');
        $this->setAlertType('success');
         */

        $this->userAuth->destroySession();

        // TODO: This flash message does not appear everytime, to be investigated
        //$this->setFlash('success', "Successfully sign-out");

        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }

    public function index(Request $request, Response $response): Response
    {
        $response->getBody()->write($this->view->render('login.tpl'));
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws AppException
     */
    public function login(Request $request, Response $response): Response
    {
        $postData = $request->getParsedBody();
        $form_data = [
            'username' => Sanitizer::sanitize($postData['username']),
            'password' => $postData['password']
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
            //TODO: set flash message and redirect to login page
            //print_r($v->errors());
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        $this->session->set('user_authenticated', $this->userAuth->authUser($form_data['username'], $form_data['password']));

        if ($this->userAuth->authenticated()) {
            $username = Sanitizer::sanitize($postData['username']);
            $this->session->set('username', $username);

            // TODO: This flash message does not appear everytime, to be investigated
            //$this->setFlash('success', "Successfully authenticated");

            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        echo 'bad username/password';
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }
}
