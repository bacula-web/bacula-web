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

	private static $global_config;
	
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
	
	public static function open() {
	
		// Check if config file exist and is readable, then include it
        if ( is_readable( CONFIG_FILE ) ) {
			require_once( CONFIG_FILE );
        }else {
            throw new Exception("Config file not found or bad file permissions");
            return false;
        }
		
		// Getting global $config variable
		FileConfig::$global_config = $config;

       	// Check if at least one catalog have been defined in the configuration file
        if( isset(FileConfig::$global_config ) ) {
			if ( empty( FileConfig::$global_config ) ) {
           		throw new Exception("The configuration is missing");
            	return false;
			}
		}else{
			throw new Exception("The configuration is missing or ther's something wrong in it");
			return false;
		}
	} // end function open()

 	// ==================================================================================
	// Function: 	get_Value()
	// Parameters:	$parameter
	//				$catalog_id (optional), take the first catalog by default
	// Return:		Database size
	// ==================================================================================
	
	public static function get_Value( $parameter, $catalog_id = null ) {
		// Check if the $global_config have been already set first
		if( !isset(FileConfig::$global_config) ) {
			throw new Exception("The configuration is missing or ther's something wrong in it");
			return false;
		}
		
		// if the catalog id have been defined in parameters
		if( !is_ntull($catalog_id) ) {
			if( isset( FileConfig::$global_config[$catalog_id][$parameter] ) ) {
			}else{
				throw new Exception("The parameter $parameter is missing in the configuration");
				return 
			}
		}
	} // end function
	
	
 } // end class
 ?>
