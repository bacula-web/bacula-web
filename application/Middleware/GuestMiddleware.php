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

    public function __construct(UserAuth $userAuth, SessionInterface $session )
    {
        $this->userAuth = $userAuth;
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        FileConfig::open(CONFIG_FILE);

        if ($request->getMethod() == 'GET') {
            if ($this->session->get('user_authenticated') === 'yes' || !FileConfig::get_Value('enable_users_auth')) {
                $response = new Response();
                return $response
                    ->withHeader('Location', '/')
                    ->withStatus(302);
            }
        }

        return $handler->handle($request);
    }
}