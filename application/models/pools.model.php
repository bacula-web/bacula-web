<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2020, Davide Franco			                            |
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

    public function count($tablename = 'Pool', $filter = null)
    {
        return parent::count($tablename);
    }
  
    // ==================================================================================
    // Function: 	getPools()
    // Parameters: 	none	
    // Return:	pools list in a array
    // ==================================================================================

    public function getPools()
    {
        $pools    = null;
        $table    = 'Pool';
        $where    = null;
        $orderby  = 'Name';
        
        if (FileConfig::get_Value('hide_empty_pools')) {
            $where[] = "$table.NumVols > 0";
        }
        
        $fields = array( 'poolid', 'name', 'numvols');
        $result = $this->run_query(CDBQuery::get_Select( array( 'table' => $table, 
                                                                  'fields' => $fields, 
                                                                  'where' => $where,
                                                                  'orderby' => $orderby )));
            
        foreach ($result->fetchAll() as $pool) {
            $pools[] = $pool;
        }

        return $pools;
    }
}
