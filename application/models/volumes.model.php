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

    public function count($tablename = 'Media', $filter = null)
    {
        return CModel::count($tablename);
    }

    // ==================================================================================
    // Function: 	getDiskUsage()
    // Parameters: 	none
    // Return:		disk space usage (in Bytes) for all volumes
    // ==================================================================================

    public function getDiskUsage()
    {
        $fields        = array('SUM(Media.VolBytes) as bytes_size');
        $statment     = array( 'table' => 'Media', 'fields' => $fields );
        
     	// Run SQL query
        $result     = $this->run_query(CDBQuery::get_Select($statment));
    
        $result     = $result->fetch();
        return $result['bytes_size'];
    }

    // ==================================================================================
    // Function: 	getVolumes()
    // Parameters: 	$pool_id
    // Return:	        list of volumes (array)	
    // ==================================================================================

    public function getVolumes( $pool_id = null) {
       $volumes_list = array();

	    $query    = "SELECT Media.volumename, Media.volbytes, Media.voljobs, Media.volstatus, Media.mediatype, Media.lastwritten, 
			           Media.volretention, Media.slot, Media.inchanger, Pool.Name AS pool_name
                    FROM Media LEFT JOIN Pool ON Media.poolid = Pool.poolid ";

       if( !is_null($pool_id) ) {
         $query .= " WHERE Media.PoolId = $pool_id ";
       }

       $query .= "ORDER BY Media.Volumename";

       $result = $this->run_query($query);

       foreach ($result->fetchAll() as $volume) {
            $volumes_list[] = $volume;
        }

        return $volumes_list;
    }
}
