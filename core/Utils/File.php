<?php

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

declare(strict_types=1);

namespace Core\Utils;

use Core\Exception\ConfigFileException;

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
            require_once self::$config_file;
            self::$config = $config;
            return true;
        } else {
            $message = 'Config file (<b>application/config/config.php</b>) not found or not readable. <br /> <br />
                        See how to configure Bacula-Web in 
                        <a href="https://www.bacula-web.org/docs/install/configure" 
                        target="_blank" rel="noopener noreferrer" ">
                        documentation</a>';

            throw new ConfigFileException($message);
        }
    }
}
