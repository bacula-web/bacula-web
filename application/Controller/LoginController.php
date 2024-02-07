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

use App\Libs\Config;
use Core\App\UserAuth;
use Core\Exception\AppException;
use Core\Helpers\Sanitizer;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

class LoginController
{
    private UserAuth $userAuth;
    private SessionInterface $session;
    private Twig $twig;
    private ?string $basePath;
    private Config $config;

    public function __construct(
        UserAuth $userAuth,
        SessionInterface $session,
        Twig $twig,
        Config $config)
    {
        $this->userAuth = $userAuth;
        $this->session = $session;
        $this->twig = $twig;
        $this->config = $config;

        $this->basePath = $this->config->get('basepath', null);
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

        return $response
            ->withHeader('Location', $this->basePath . '/login')
            ->withStatus(302);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/login.html.twig');
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
                ->withHeader('Location', $this->basePath . '/login')
                ->withStatus(302);
        }

        $this->session->set('user_authenticated', $this->userAuth->authUser($form_data['username'], $form_data['password']));

        if ($this->userAuth->authenticated()) {

            $username = Sanitizer::sanitize($form_data['username']);

            $this->session->set('username', $username);

            $this->session->getFlash()->set('info', ['Successfully authenticated']);

            return $response
                ->withHeader('Location', $this->basePath . '/')
                ->withStatus(302);
        } else {
            $this->session->getFlash()->set('error', ['Wrong username or password']);
            $this->session->save();

            return $response
                ->withHeader('Location', $this->basePath . '/login')
                ->withStatus(302);
        }
    }
}
