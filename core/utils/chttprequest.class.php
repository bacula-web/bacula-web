<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco                                      |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the            |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
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
    // Function: 	getSaveValue( $value )
    // Parameters:	$value
    // Return:		secured value
    // ==================================================================================

    private static function getSafeValue($value)
    {
        return strip_tags($value);
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
    // Return:		value of $var, or null if not defined
    // ==================================================================================
    public static function get_Value($var)
    {
        if (isset(self::$value_list[$var])) {
            return self::$value_list[$var];
        } else {
            return null;
        }
    }
}

// end class
