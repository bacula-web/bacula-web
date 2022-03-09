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

class CHttpRequest
{
    private static $value_list;
    
    // ==================================================================================
    // Function: 	__construct()
    // Parameters:	none
    // Return:
    // ==================================================================================

    private function __construct()
    {
        self::$value_list = array();
    }
    
    // ==================================================================================
    // Function: 	getSafeValue( $value )
    // Parameters:	$value
    // Return:		secured value
    // ==================================================================================

    private static function getSafeValue($value)
    {
        return htmlspecialchars(strip_tags($value));
    }

    // ==================================================================================
    // Function: 	count()
    // Parameters:	$tablename
    //				$filter (optional)
    // Return:		array containing all passed values by $_POST or $_GET
    // ==================================================================================

    public static function get_Vars()
    {
        
        // $_POST
        foreach ($_POST as $var => $value) {
            self::$value_list[$var] = self::getSafeValue($value);
        }
        
        // $_GET
        foreach ($_GET as $var => $value) {
            self::$value_list[$var] = self::getSafeValue($value);
        }
    }

    // ==================================================================================
    // Function: 	get_Value()
    // Parameters:	$var
    // Return:		value of $var, or NULL if not defined
    // ==================================================================================
    public static function get_Value($var)
    {
        if (isset(self::$value_list[$var]) && strlen(self::$value_list[$var]) > 0) {
            return self::$value_list[$var];
        } else {
            return null;
        }
    }

    public static function getAll() {
        return self::$value_list;
    }
}

// end class
