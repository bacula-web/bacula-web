<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2018, Davide Franco			                            |
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

    /**
     * This method return a list of volumes
     *
     * @param int $pool_id
     * @param string $orderby
     * @param string $orderasc
     *
     * @return @array
     */

    public function getVolumes( $pool_id = null, $orderby = 'Name', $orderasc = 'DESC') {
       $volumes_list = array();
       $where = '';

       if( !is_null($pool_id) ) {
          $this->addParameter( 'pool_id', $pool_id);
          $where = 'WHERE Media.PoolId = :pool_id';
       }

	    $query    = "SELECT Media.volumename, Media.volbytes, Media.voljobs, Media.volstatus, Media.mediatype, Media.lastwritten, 
			           Media.volretention, Media.slot, Media.inchanger, Pool.Name AS pool_name
                    FROM Media LEFT JOIN Pool ON Media.poolid = Pool.poolid $where ORDER BY $orderby $orderasc";

       $result = $this->run_query($query);

       foreach ($result->fetchAll() as $volume) {
            $volumes_list[] = $volume;
        }

        return $volumes_list;
    }
}
