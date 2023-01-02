<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
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

use Core\Utils\File;
use Exception;

class FileConfig extends File
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
    }

    // ==================================================================================
    // Function:    check()
    // Parameters:  none
    // Return:      false if something is wrong in the configuration file
    // ==================================================================================

    public function check()
    {
        // Check if all parameters ... to be completed
    }

    // ==================================================================================
    // Function:    count_Catalogs()
    // Parameters:  none
    // Return:      configured catalog count number or false if something goes wrong
    // ==================================================================================

    public static function count_Catalogs()
    {
        $catalog_count = 0;

        foreach ($GLOBALS['config'] as $param) {
            if (is_array($param)) {
                $catalog_count += 1;
            }
        }

        return $catalog_count;
    }

    // ==================================================================================
    // Function:    get_Value()
    // Parameters:  configuration parameter or false (if not defined)
    //              $catalog_id (optional)
    // Return:      parameter value or NULL if does not exist
    // ==================================================================================

    public static function get_Value($parameter, $catalog_id = null)
    {
        // Check if the $global_config have been already set first
        if (!isset(self::$config_file)) {
            throw new Exception("The configuration is missing or there's something wrong in it");
        }

        // If $catalog_id is not null, get value from this catalog
        if (!is_null($catalog_id)) {
            if (is_array(parent::$config[$catalog_id])) {
                return parent::$config[$catalog_id][$parameter];
            } else {
                throw new Exception("Configuration error: catalog id <$catalog_id> is empty or does not exist");
            }
        } else {
            if (isset(parent::$config[$parameter])) {
                return parent::$config[$parameter];
            } else {
                return null;
            }
        }
    } // end function

    // ==================================================================================
    // Function:    get_DataSourceName()
    // Parameters:  $catalog_id
    // Return:      dsn string
    // ==================================================================================
    public static function get_DataSourceName($catalog_id)
    {
        $dsn             = '';
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

    // ==================================================================================
    // Function:    get_Catalogs()
    // Parameters:  none
    // Return:      an array containing all catalogs labels define in the configuration
    // ==================================================================================

    public static function get_Catalogs()
    {
        $catalogs = array();

        foreach (parent::$config as $parameter) {
            if (is_array($parameter)) {
                $catalogs[] = $parameter['label'];
            }
        }

        return $catalogs;
    }

    // ==================================================================================
    // Function:    catalogExist()
    // Parameters:  $catalog_id
    // Return:      true or false (depending if catalog exist or not in the configuration)
    // ==================================================================================

    public static function catalogExist($catalog_id)
    {
        return array_key_exists($catalog_id, parent::$config);
    }
} // end class
