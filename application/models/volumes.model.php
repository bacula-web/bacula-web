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

class Volumes_Model extends CModel
{
 
    // ==================================================================================
    // Function: 	count()
    // Parameters:	$tablename
    //				$filter (optional)
    // Return:		return row count for one table
    // ==================================================================================

    public static function count($pdo, $tablename = 'Media', $filter = null)
    {
        return CModel::count($pdo, $tablename);
    }

    // ==================================================================================
    // Function: 	getDiskUsage()
    // Parameters: 	none
    // Return:		disk space usage (in Bytes) for all volumes
    // ==================================================================================

    public static function getDiskUsage($pdo)
    {
        $fields        = array('SUM(Media.VolBytes) as bytes_size');
        $statment     = array( 'table' => 'Media', 'fields' => $fields );
        
     // Run SQL query
        $result     = CDBUtils::runQuery(CDBQuery::get_Select($statment), $pdo);
    
        $result     = $result->fetch();
        return $result['bytes_size'];
    }
}
