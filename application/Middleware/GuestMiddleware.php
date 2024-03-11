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
use GuzzleHttp\Psr7\Response;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GuestMiddleware implements MiddlewareInterface
{
    private UserAuth $userAuth;
    private SessionInterface $session;
    private ?string $basePath;
    private Config $config;

    public function __construct(UserAuth $userAuth, SessionInterface $session, Config $config)
    {
        $this->userAuth = $userAuth;
        $this->session = $session;
        $this->config = $config;
        $this->basePath = $config->get('basepath', null);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() == 'GET') {
            if ($this->config->get('enable_users_auth') === false) {
                $response = new Response();
                return $response
                    ->withHeader('Location', $this->basePath . '/')
                    ->withStatus(302);
            } else {
                if ($this->session->get('user_authenticated') === 'yes') {
                    $response = new Response();

                    return $response
                        ->withHeader('Location', $this->basePath . '/')
                        ->withStatus(302);
                }
            }
        }

        return $handler->handle($request);
    }
}
