<?php

declare(strict_types=1);

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

namespace App\Libs;

use Core\Exception\ConfigFileException;
use Core\Utils\File;

class FileConfig extends File
{
    /**
     * Return number of Bacula catalog(s) defined in configuration
     *
     * @return int
     */
    public static function count_Catalogs(): int
    {
        $catalog_count = 0;

        foreach ($GLOBALS['config'] as $param) {
            if (is_array($param)) {
                $catalog_count += 1;
            }
        }

        return $catalog_count;
    }

    /**
     * Return parameter value from config, or null if not set
     *
     * @param string $parameter
     * @param ?int $catalog_id
     * @return mixed|null
     * @throws ConfigFileException
     */
    public static function get_Value(string $parameter, int $catalog_id = null)
    {
        // Check if the $global_config have been already set first
        if (!isset(self::$config_file)) {
            throw new ConfigFileException("The configuration is missing or there's something wrong with it");
        }

        // If $catalog_id is not null, get value from this catalog
        if (!is_null($catalog_id)) {
            if (is_array(parent::$config[$catalog_id])) {
                return parent::$config[$catalog_id][$parameter];
            } else {
                throw new ConfigFileException("Configuration error: catalog id <$catalog_id> is empty or does not exist");
            }
        } else {
            if (isset(parent::$config[$parameter])) {
                return parent::$config[$parameter];
            } else {
                return null;
            }
        }
    } // end function

    /**
     * @param int $catalog_id
     * @return string
     */
    public static function get_DataSourceName(int $catalog_id): string
    {
        $dsn = '';
        $current_catalog = parent::$config[$catalog_id];

        switch ($current_catalog['db_type']) {
            case 'mysql':
            case 'pgsql':
                $dsn = $current_catalog['db_type'] . ':';
                $dsn .= 'dbname=' . $current_catalog['db_name'] . ';';

                if (isset($current_catalog['host'])) {
                    $dsn .= 'host=' . $current_catalog['host'] . ';';
                }

                if (isset($current_catalog['db_port']) && !empty($current_catalog['db_port'])) {
                    $dsn .= 'port=' . $current_catalog['db_port'] . ';';
                }
                break;
            case 'sqlite':
                $dsn = $current_catalog['db_type'] . ':' . $current_catalog['db_name'];
                break;
        }

        return $dsn;
    }

    /**
     * Return an array containing all catalogs labels define in the configuration
     *
     * @return (string)[]
     */
    public static function get_Catalogs(): array
    {
        $catalogs = array();

        foreach (parent::$config as $parameter) {
            if (is_array($parameter)) {
                $catalogs[] = $parameter['label'];
            }
        }

        return $catalogs;
    }

    /**
     * Verify if catalag with provided id does exist in the configuration
     *
     * @param int $catalogid
     * @return bool
     */
    public static function catalogExist(int $catalogid): bool
    {
        return array_key_exists($catalogid, parent::$config);
    }
}
