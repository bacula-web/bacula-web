<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco			                            |
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
		$db_name	= 'bacula';
		$statment 	= array();
		$result	 	= '';
		
		$pdo_driver = CDBUtils::getDriverName( $pdo_connection );
		
		switch( $pdo_driver )
		{
			case 'mysql':
				$statment 	= array( 	'table'  => 'information_schema.TABLES', 
										'fields' => array("table_schema AS 'database', sum( data_length + index_length) AS 'dbsize'"),
										'where'  => array( "table_schema = '$db_name'" ),
										'groupy' => 'table_schema' );

				$statment 	= CDBQuery::get_Select( $statment );
				
				$result   	= CDBUtils::runQuery($statment, $pdo_connection);
			break;
			case 'pgsql':
				$statment 	= "SELECT pg_database_size('bacula') AS dbsize";
				$result    	= CDBUtils::runQuery($statment, $pdo_connection);
			break;
			case 'sqlite':
				$db_size = filesize( FileConfig::get_Value( 'db_name', $catalog_id) );
				return CUtils::Get_Human_Size($db_size);
			break;
		}
		// Execute SQL statment
		$db_size = $result->fetch();
		
		return CUtils::Get_Human_Size( $db_size['dbsize'] );	
	}
 }
