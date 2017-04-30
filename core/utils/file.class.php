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
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
*/

require_once('core/global.inc.php');

class File
{
 
    protected static $config_file;
    protected static $config;
     
     // ==================================================================================
     // Function: 	__constructor()
     // Parameters:	none
     // Return:		none
     // ==================================================================================

    private function __construct()
    {
        // Nothing to do here
    }
     
     // ==================================================================================
     // Function: 	open()
     // Parameters:	none
     // Return:		true or false if the file doesn't exist or unreadable
     // ==================================================================================

    public static function open($file)
    {
        global $config;
        
    // static variable singleton
        if (!self::$config_file) {
            self::$config_file = $file;
        }
        
    // Check if config file exist and is readable, then include it
        if (is_readable(self::$config_file)) {
            require_once(self::$config_file);
         //echo 'configuration file <b>' . self::$config_file . '</b> succesfully loaded <br />';
            self::$config = $config;
            return true;
        } else {
            throw new Exception("Config file not found or bad file permissions");
        }
    } // end function open()
}
