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

namespace App\Middleware;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Csrf\Guard;
use Slim\Views\Twig;

class CsrfMiddleware implements MiddlewareInterface
{
    private Twig $twig;
    private Guard $csrf;

    /**
     * @param Twig $twig
     * @param ContainerInterface $container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(Twig $twig, ContainerInterface $container)
    {
        $this->twig = $twig;
        $this->csrf = $container->get('csrf');
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return ResponseInterface
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $csrfKeyName = $this->csrf->getTokenNameKey();
        $csrfKeyValue = $this->csrf->getTokenValueKey();
        $csrfTokenName = $this->csrf->getTokenName();
        $csrfTokenValue = $this->csrf->getTokenValue();

        $csrf = "
        <input type='hidden' name='$csrfKeyName' value='$csrfTokenName'>
        <input type='hidden' name='$csrfKeyValue' value='$csrfTokenValue'>
        ";

        $this->twig->getEnvironment()->addGlobal('csrf', $csrf);

        return $handler->handle($request);
    }
}