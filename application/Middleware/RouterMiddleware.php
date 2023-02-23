<?php

declare(strict_types=1);

/**
 * Copyright (C) 2023 Davide Franco
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

use Core\App\View;
use Core\App\WebApplication;
use Core\Exception\AppException;
use Core\Exception\PageNotFoundException;
use Core\Middleware\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RouterMiddleware is a "simple pass" psr-15 middleware implementation
 *
 * The process method checks if page request param has been provided, if
 * the requested page does not exist, it throw a PageNotFoundException.
 * If page request param is not provided, it return the Response from the
 * default controller (Home dashboard)
 *
 */
class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws PageNotFoundException
     * @throws AppException
     */
    public function process(Request $request, Response $response): Response
    {
        if ($request->getPathInfo() !== '/') {
            throw new AppException('Invalid requested path');
        }

        // Inject request params into Request object instance
        $params = $request->query->all();
        $params = array_merge($request->request->all(), $params);
        $request->attributes->add($params);
        $requestedpage = $request->attributes->get('page');

        $routes = WebApplication::getRoutes();

        if ($requestedpage === null) {
            $fallback = $routes['home']['callback'];
            $response = call_user_func([(new $fallback($request, (new View()))), 'prepare']);
        } elseif ((array_key_exists($requestedpage, $routes))) {
            $callback = $routes[$requestedpage]['callback'];
            $response = call_user_func([(new $callback($request, (new View()))), 'prepare']);
            $response->setStatusCode(200);
        }else {
            throw new PageNotFoundException();
        }
        return $response;
    }
}
