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

namespace App;

use Closure;
use Psr\Http\Message\ResponseFactoryInterface;

class CsrfErrorHandler
{
    /**
     * @param ResponseFactoryInterface $responseFactory
     * @return Closure
     */
    public function handle(ResponseFactoryInterface $responseFactory): Closure
    {
        return function () use ($responseFactory) {
            $response = $responseFactory->createResponse()->withStatus(403);
            $response->getBody()->write('Invalid CSRF token, go back to <a href="/">Home page</a>');
            return $response;
        };
    }
}