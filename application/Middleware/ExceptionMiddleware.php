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

use Core\Exception\NotAuthenticatedException;
use Core\Exception\NotAuthorizedException;
use Core\Exception\PageNotFoundException;
use Core\Middleware\MiddlewareInterface;
use Core\Exception\ConfigFileException;
use Core\Utils\ExceptionRenderer;
use Error;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        set_exception_handler([$this, 'process']);
        set_error_handler([$this, 'process']);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ConfigFileException
     */
    public function process(Request $request, Response $response): Response
    {
        $status_code = 500;

        if (is_subclass_of($this->exception, Exception::class)) {
           $response->setContent(ExceptionRenderer::renderException($this->exception));
        } elseif (is_subclass_of($this->exception, Error::class)) {
           $response->setContent(ExceptionRenderer::renderError($this->exception));
        }

        if( get_class($this->exception) === PageNotFoundException::class) {
            $status_code = 404;
        }

        if( get_class($this->exception) === NotAuthenticatedException::class || get_class($this->exception) === NotAuthorizedException::class) {
            $status_code = 403;
        }

        $response->setStatusCode($status_code);
        return $response;
    }
}
