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
use App\Validator\LoginValidator;
use Core\App\UserAuth;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LoginController
{
    private UserAuth $userAuth;
    private SessionInterface $session;
    private Twig $twig;
    private ?string $basePath;
    private Config $config;

    public function __construct(
        UserAuth         $userAuth,
        SessionInterface $session,
        Twig             $twig,
        Config           $config)
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
        $this->session->getFlash()->add('auth_info', 'Successfully logged out');
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
        if ($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();

            $loginValidator = new LoginValidator($postData);

            if (!$loginValidator->validate()) {
                $validationErrors = $loginValidator->getErrors();

                /**
                 * Set username in flash ONLY if it passed the validation
                 */
                if (!isset($validationErrors['username'])) {
                    $this->session->getFlash()->add('username', $postData['username']);
                }

                $this->session->getFlash()->set('errors', $validationErrors);

                return $response
                    ->withHeader('Location', $this->basePath . '/login')
                    ->withStatus(302);

            } else {
                // TODO: this should be the responsibility of the auth class
                $this->session->set('user_authenticated', $this->userAuth->authUser($postData['username'], $postData['password']));

                if ($this->userAuth->authenticated()) {
                    // TODO: this is not the responsibility of the login controller
                    $this->session->set('username', $postData['username']);

                    return $response
                        ->withHeader('Location', $this->basePath . '/')
                        ->withStatus(302);
                } else {
                    // TODO: last auth error should come from the Auth class
                    $this->session->getFlash()->add('last_auth_error', 'Wrong username or password');
                    $this->session->getFlash()->add('username', $postData['username'] );

                    return $response
                        ->withHeader('Location', $this->basePath . '/login')
                        ->withStatus(302);
                }
            }
        }

        return $this->twig->render($response, 'pages/login.html.twig', [
            'errors' => $this->session->getFlash()->get('errors'),
            'username' => $this->session->getFlash()->get('username'),
            'last_auth_error' => $this->session->getFlash()->get('last_auth_error'),
            'auth_info' => $this->session->getFlash()->get('auth_info')
        ]);
    }
}
