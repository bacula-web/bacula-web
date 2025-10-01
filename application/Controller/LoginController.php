<?php

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

declare(strict_types=1);

namespace App\Controller;

use Core\App\UserAuth;
use Core\Controller\AbstractController;
use Core\Exception\ValidationException;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

class LoginController extends AbstractController
{
    /**
     * @param Response $response
     * @param UserAuth $userAuth
     * @return mixed
     */
    public function signOut(Response $response, UserAuth $userAuth): Response
    {
        $userAuth->destroySession($this->session);
        $this->session->getFlash()->add('auth_info', 'Successfully logged out');
        $this->session->save();

        return $response
            ->withHeader('Location', $this->basePath . '/login')
            ->withStatus(302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws NonUniqueResultException
     */
    public function index(Request $request, Response $response): ResponseInterface
    {
        return $this->view->render($response, 'pages/login.html.twig', [
            'username' => $this->session->getFlash()->get('username'),
            'last_auth_error' => $this->session->getFlash()->get('last_auth_error'),
            'auth_info' => $this->session->getFlash()->get('auth_info')
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param UserAuth $userAuth
     * @return ResponseInterface
     * @throws NonUniqueResultException
     */
    public function login(Request $request, Response $response, UserAuth $userAuth): ResponseInterface
    {
        $postData = $request->getParsedBody();

        $loginValidator = new Validator($postData, ['username', 'password']);
        $loginValidator->rules([
            'required' => [
                'username', 'password'
            ],
            'alphaNum' => ['username'],
            'lengthMin' => [
                ['password', 8]
            ]
        ]);

        if (!$loginValidator->validate()) {
            $validationErrors = $loginValidator->errors();

            /**
             * Populate username form field if it passed validation
             */
            if (!isset($validationErrors['username'])) {
                $this->session->getFlash()->add('username', $postData['username']);
            }

            throw new ValidationException(['last_auth_error' => 'Wrong username or password']);
        }

        // TODO: this should be the responsibility of the auth class
        $this->session->set('user_authenticated', $userAuth->authUser($postData['username'], $postData['password']));

        if ($userAuth->authenticated()) {
            // TODO: this is not the responsibility of the login controller
            $this->session->regenerateId();
            $this->session->set('username', $postData['username']);

            return $response
                ->withHeader('Location', $this->basePath . '/')
                ->withStatus(302);
        } else {
            // TODO: last auth error should come from the Auth class
            $this->session->getFlash()->add('last_auth_error', 'Wrong username or password');
            $this->session->getFlash()->add('username', $postData['username']);

            return $response
                ->withHeader('Location', $this->basePath . '/login')
                ->withStatus(302);
        }
    }
}
