<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco			                    |
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

 class FileConfig {

	static private $config_parameters;
	
    // ==================================================================================
	// Function: 	__constructor()
	// Parameters:	none
	// Return:		none
	// ==================================================================================
	
	public function __construct() {
	
	}
	
    // ==================================================================================
	// Function: 	open()
	// Parameters:	none
	// Return:		return false if the file is unreadable (not found or no enough permission) or no db connection defined
	// ==================================================================================
	
	static public function open() {
	
		// Check if config file exist and is readable, then include it
        if ( is_readable( CONFIG_FILE ) ) {
			require_once( CONFIG_FILE );
        }else {
            throw new Exception("Config file not found or bad file permissions");
            return false;
        }
		
		// Getting global $config variable
		global $config;
		var_dump($config);
		self::$config_parameters = $config;

        // Checking options and database parameters
        if ( !array_key_exists('0', self::$config_parameters) ) {
            throw new Exception("At least one catalog should be defined in the configuration");
            return false;
        }
	
		return true;
	}
	
	
 } // end class