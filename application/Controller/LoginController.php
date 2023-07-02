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
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Slim\Views\Twig;
use Valitron\Validator;

class LoginController
{
    private UserAuth $userAuth;
    private SessionInterface $session;

    public function __construct(UserAuth $userAuth, SessionInterface $session)
    {
        $this->userAuth = $userAuth;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function signOut(Request $request, Response $response): Response
    {
        $this->userAuth->destroySession($this->session);
        $this->session->getFlash()->add('info', 'Logged out successfully.');
        $this->session->save();

        $this->view->setTemplate('login.tpl');

        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }

    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'pages/login.html.twig', [
            'flash' => $this->session->getFlash()
        ]);

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
            'lengthMin' => [
                ['password', 8]
            ]
        ]);

        if (!$v->validate()) {
            $this->session->getFlash()->set('error', ['Wrong username or password']);
            $this->session->save();

            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        $this->session->set('user_authenticated', $this->userAuth->authUser($form_data['username'], $form_data['password']));

        // TODO: fix $userAuth->authenticated()
        //if ($this->userAuth->authenticated()) {

        if ($this->session->get('user_authenticated') === 'yes') {
            $username = Sanitizer::sanitize($postData['username']);
            $this->session->set('username', $username);

            $this->session->getFlash()->set('info', ['Successfully authenticated']);
            $this->session->save();

            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        } else {
            $this->session->getFlash()->set('error', ['Wrong username or password']);
            $this->session->save();

            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }
    }
}
