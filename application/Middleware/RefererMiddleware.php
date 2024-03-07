<?php

/**
 * Copyright (C) 2024-present Davide Franco
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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

/**
 * Set referer in Twig environment if present in request headers
 * and the host is the same as web application url
 */
class RefererMiddleware implements MiddlewareInterface
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $referer = $request->getHeader('referer');
        $refererUrl = $referer[0] ?? null;

        if ($referer) {
            $requestUrlHost = parse_url($refererUrl, PHP_URL_HOST);

            /**
             * disable back button if previous url is the login page
             */
            if (parse_url($refererUrl, PHP_URL_PATH) === '/login') {
                return $handler->handle($request);
            }

            /**
             * enable back button only if host in referer url is the same as the web app
             */
            if ($requestUrlHost === $request->getUri()->getHost()) {
                $requestUrl = (string)$request->getUri();

                /**
                 * enable back button if previous page and current page urls are different
                 */
                if ($refererUrl !== $requestUrl) {
                    $this->twig->getEnvironment()->addGlobal('referer', $refererUrl);
                }
            }
        }

        return $handler->handle($request);
    }
}
