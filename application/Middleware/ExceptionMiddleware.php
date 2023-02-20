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
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ConfigFileException
     */
    public function process(Request $request, Response $response): Response
    {
        if (is_subclass_of($this->exception, Exception::class)) {
           return (new Response())->setContent(ExceptionRenderer::renderException($this->exception));
       } elseif (is_subclass_of($this->exception, Error::class)) {
           return (new Response())->setContent(ExceptionRenderer::renderError($this->exception));
       }

       return $response;
    }
}
