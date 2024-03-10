<?php

/**
 * Copyright (C) 2022-present Davide Franco
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

namespace Core\Helpers;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Sanitize GET query and POST request user input.
 *
 * @param ServerRequestInterface $request
 * @return array
 */
function getRequestParams(ServerRequestInterface $request): array
{
    if ($request->getMethod() === 'POST') {
        $params = $request->getParsedBody();
    } else {
        $params = $request->getQueryParams();
    }

    foreach ($params as $key => $value) {
        $params[$key] = Sanitizer::sanitize($value);
    }
    return $params;
}
