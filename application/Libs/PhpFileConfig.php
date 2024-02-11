<?php

/**
 * Copyright (C) 2024-present Davide Franco
 *
 * This file is part of Bacula-Web project.
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

namespace App\Libs;

use Core\Exception\ConfigFileException;

class PhpFileConfig
{
    /**
     * @param string $phpConfigFile
     * @return array
     * @throws ConfigFileException
     */
    public static function load(string $phpConfigFile): array
    {
        global $config;

        try {
            if (is_readable($phpConfigFile))
            {
                require_once $phpConfigFile;
                return $config;
            } else {
                throw new ConfigFileException();
            }
        } catch (ConfigFileException $e)
        {
            throw new ConfigFileException("PHP config file {$phpConfigFile} does not exists or is not readable");
        }
    }
}
