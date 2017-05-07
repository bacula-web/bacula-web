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

class Pools_Model extends CModel
{

    // ==================================================================================
    // Function: 	count()
    // Parameters:	$pdo (PDO valid connection)
    // Return:	row count for one table
    // ==================================================================================

    public static function count($pdo, $tablename = 'Pool', $filter = null)
    {
        return CModel::count($pdo, $tablename);
    }
  
    // ==================================================================================
    // Function: 	getPools()
    // Parameters:	$pdo (PDO valid connection)

    // Return:	pools list in a array
    // ==================================================================================

    public static function getPools($pdo)
    {
        $pools    = null;
        $table    = 'Pool';
        $where    = null;
        $orderby  = 'Name';
        
        if (FileConfig::get_Value('hide_empty_pools')) {
            $where[] = "$table.NumVols > 0";
        }
        
        $fields = array( 'poolid', 'name', 'numvols');
        $result = CDBUtils::runQuery(CDBQuery::get_Select( array( 'table' => $table, 
                                                                  'fields' => $fields, 
                                                                  'where' => $where,
                                                                  'orderby' => $orderby )), 
                                                                  $pdo);
            
        foreach ($result->fetchAll() as $pool) {
            $pools[] = $pool;
        }

        return $pools;
    }
}
