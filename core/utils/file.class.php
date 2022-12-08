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

namespace Core\Utils;

class File
{
    /**
     * string @var
     */
    protected static $config_file;

    /**
     * mixed @var
     */
    protected static $config;

    /**
     * @param string $file
     * @return bool
     * @throws ConfigFileException
     */
    public static function open(string $file): bool
    {
        global $config;

        // static variable singleton
        if (!self::$config_file) {
            self::$config_file = $file;
        }

        // Check if config file exist and is readable, then include it
        if (is_readable(self::$config_file)) {
            require_once(self::$config_file);
            self::$config = $config;
            return true;
        } else {
            throw new ConfigFileException('Config file not found or not readable');
        }
    }
}
