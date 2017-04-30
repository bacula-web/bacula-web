<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco			                               |
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

class CModel
{
    protected static $pdo_connection;
 
    // ==================================================================================
    // Function: 	count()
    // Parameters:	$tablename
    //				$filter (optional)
    // Return:		return row count for one table
    // ==================================================================================

    protected static function count($pdo, $tablename, $filter = null)
    {
        $fields        = array( 'COUNT(*) as row_count' );

     // Prepare and execute query
        $statment   = CDBQuery::get_Select(array( 'table' => $tablename, 'fields' => $fields, $filter));
        $result     = CDBUtils::runQuery($statment, $pdo);

        $result     = $result->fetch();
        
        // If SQL count result is null, return 0 instead (much better when plotting data)
        if( is_null($result['row_count']) ) {
        	return 0;
        }else {
        	return $result['row_count'];
        }
    }
}
