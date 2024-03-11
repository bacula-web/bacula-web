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

namespace App\Middleware;

use App\Libs\Config;
use Core\App\UserAuth;
use Core\Exception\AppException;
use Core\Exception\ConfigFileException;
use GuzzleHttp\Psr7\Response;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class DbAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var UserAuth
     */
    private UserAuth $dbAuth;
    private SessionInterface $session;
    private Twig $twig;
    private ?string $basePath;
    private Config $config;

    /**
     * @param UserAuth $userAuth
     * @param SessionInterface $session
     * @param Twig $twig
     * @param Config $config
     * @throws AppException
     * @throws ConfigFileException
     */
    public function __construct(UserAuth $userAuth, SessionInterface $session, Twig $twig, Config $config)
    {
        $this->dbAuth = $userAuth;

        // Check if database exists and is writable
        $this->dbAuth->check();
        $this->dbAuth->checkSchema();

        $this->session = $session;
        $this->twig = $twig;
        $this->config = $config;

        $this->basePath = $this->config->get('basepath', null);
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ConfigFileException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->config->get('enable_users_auth') === false) {
            return $handler->handle($request);
        }

        if ($this->session->has('user_authenticated')) {
            if ($this->session->get('user_authenticated') === 'yes') {
                $this->twig->getEnvironment()->addGlobal('username', $this->session->get('username'));
                $this->twig->getEnvironment()->addGlobal('user_authenticated', true);

                return $handler->handle($request);
            }
        }

        /**
         * If the user is not authenticated, Redirect to login page with a flash message
         */
        $this->session->getFlash()->set('last_auth_error', ['Authentication is required']);
        $response = new Response();

        return $response
            ->withHeader('Location', $this->basePath . '/login')
            ->withStatus(302);
    }
}
