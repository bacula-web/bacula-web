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
use Core\Exception\ValidationException;
use Doctrine\ORM\NonUniqueResultException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class LoginController extends AbstractController
{
    /**
     * @return ResponseInterface
     */
    #[Route("/signout", name: "signout")]
    public function signOut(/* Response $response, UserAuth $userAuth */): Response
    {
        return new Response('signout');

        $userAuth->destroySession($this->session);
        $this->session->getFlash()->add('auth_info', 'Successfully logged out');
        $this->session->save();

        return $response
            ->withHeader('Location', $this->basePath . '/login')
            ->withStatus(302);
    }

    /**
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws NonUniqueResultException
     */
    #[Route("/login")]
    public function index(/* Request $request, Response $response */): Response
    {
        return new Response('login');

        return $this->view->render($response, 'pages/login.html.twig', [
            'username' => $this->session->getFlash()->get('username'),
            'last_auth_error' => $this->session->getFlash()->get('last_auth_error'),
            'auth_info' => $this->session->getFlash()->get('auth_info')
        ]);
    }

    /**
     * @return ResponseInterface
     * @throws NonUniqueResultException
     */
    #[Route("/login", methods: ["POST"])]
    public function login(/* Request $request, Response $response, UserAuth $userAuth */): Response
    {
        return new Response('POST login');

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
