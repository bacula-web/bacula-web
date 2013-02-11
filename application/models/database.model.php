<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2013, Davide Franco			                            |
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

 class Database_Model extends CModel {
 
 	// ==================================================================================
	// Function: 	get_Size()
	// Parameters:	$pdo_connection - valid PDO object instance
	// Return:		Database size
	// ==================================================================================
	
	public static function get_Size( $pdo_connection, $catalog_id ) {
		$db_name	= FileConfig::get_Value( 'db_name', $catalog_id);
		$statment 	= null;
		$result	 	= null;
		
		$pdo_driver = CDBUtils::getDriverName( $pdo_connection );
		
		switch( $pdo_driver )
		{
			case 'mysql':
				$statment	 = "SELECT SUM( DATA_LENGTH + INDEX_LENGTH - DATA_FREE) AS dbsize ";
				$statment	.= "FROM INFORMATION_SCHEMA.TABLES ";
				$statment	.= "where TABLE_SCHEMA = '$db_name'";			
			break;
			case 'pgsql':
				$statment 	 = "SELECT pg_database_size('bacula') AS dbsize";
				$result    	 = CDBUtils::runQuery($statment, $pdo_connection);
			break;
			case 'sqlite':
				$db_size 	 = filesize( FileConfig::get_Value( 'db_name', $catalog_id) );
				return CUtils::Get_Human_Size($db_size);
			break;
		}
		// Execute SQL statment
		$result   = CDBUtils::runQuery($statment, $pdo_connection);
		$db_size  = $result->fetch();
		
		return CUtils::Get_Human_Size( $db_size['dbsize'] );	
	}
 }
