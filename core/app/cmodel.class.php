<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco			                          |
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

 class CModel {
	protected static $pdo_connection;
 
    // ==================================================================================
	// Function: 	get_Table()
	// Parameters:	none
	// Return:		return table with correct case
	// ==================================================================================
	
	protected static function get_Table( $tablename ) {
		
		switch( CDBUtils::getDriverName( self::$pdo_connection ) ) {
			case 'pgsql':
				return strtolower($tablename);
			break;
			default:			
				return $tablename;
			break;
		}
	}
 }