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

use Core\Exception\NotAuthorizedException;
use Core\Middleware\MiddlewareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Throwable;

class ExceptionMiddleware implements MiddlewareInterface
{
    private Throwable $exception;

    /**
     * @param Throwable $exception
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function process(Request $request, Response $response): Response
    {
        switch (get_class($this->exception)) {
            case 'Core\Exception\PageNotFoundException':
                $response = new Response('Page not found, back to <a href=\'index.php\'>home page</a>', 404);
                break;
            case 'Core\Exception\NotAuthorizedException':
                //$response = new RedirectResponse('index.php?page=home');
                $response = new Response('', 302);
                //$response->setStatusCode(302);
                //$response->headers->set('Location', 'index.php?page=login', true);
                break;

            case 'Core\Exception\UserAuthenticatedException':
                $response = new Response();
                $response->setStatusCode(200);
                $response->headers->set('Location', 'index.php', true);
                break;

            default:
                echo '<pre>exception caught</pre>';
                $errorbody = 'something bad happen ' .
                    $this->exception->getCode() . ' ' .
                    $this->exception->getMessage() . ' ' .
                    get_class($this->exception);
                $response->setContent($response->getContent() . $errorbody);
                break;

        }

        return $response;
    }
}
