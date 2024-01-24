<?php

declare(strict_types=1);

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

namespace Core\Helpers;

class Sanitizer
{
    /**
     * @param string $value
     * @return string
     */
    public static function sanitize(string $value): string
    {
        return strip_tags(htmlentities((string)$value, ENT_QUOTES, 'UTF-8'));
    }
}
