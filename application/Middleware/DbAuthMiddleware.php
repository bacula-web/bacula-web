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

namespace App\Middleware;

use App\Libs\FileConfig;
use Core\App\UserAuth;
use Core\Exception\AppException;
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

    /**
     * @throws AppException
     */
    public function __construct(UserAuth $userAuth, SessionInterface $session, Twig $twig)
    {
        $this->dbAuth = new $userAuth;

        // Check if database exists and is writable
        $this->dbAuth->check();
        $this->dbAuth->checkSchema();

        $this->session = $session;

        $this->twig = $twig;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        FileConfig::open(CONFIG_FILE);
        if (!FileConfig::get_Value('enable_users_auth')) {
            return $handler->handle($request);
        }

        if ($this->session->has('user_authenticated')) {
            if ($this->session->get('user_authenticated') === 'yes') {
                $this->twig->getEnvironment()->addGlobal('username', $this->session->get('username'));
                $this->twig->getEnvironment()->addGlobal('user_authenticated', true);

                return $handler->handle($request);
            }
        }

        $this->session->getFlash()->set('error',  ['You must be authenticated']);
        $response = new Response();

        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }
}
