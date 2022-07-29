<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
 * 
 * This file is part of Bacula-Web.
 * 
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see 
 * <https://www.gnu.org/licenses/>.
 */

class Volumes_Model extends CModel
{
 
    // ==================================================================================
    // Function: 	getDiskUsage()
    // Parameters: 	none
    // Return:		disk space usage (in Bytes) for all volumes
    // ==================================================================================

    public function getDiskUsage()
    {
        $fields        = array('SUM(Media.VolBytes) as bytes_size');
        $statment     = array( 'table' => $this->tablename, 'fields' => $fields );
        
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
     * @param boolean $inchanger
     * @param  mixed $view
     *
     * @return @array
     */
    
    public function getVolumes($pool_id = null, $orderby = 'Name', $orderasc = 'DESC', $inchanger = false, $view = null)
    {
        $volumes_list = array();
        $where = null;

        $pagination = new CDBPagination($view);
        $limit = [ 'count' => $pagination->getLimit(), 'offset' => $pagination->getOffset()];

        if (!is_null($pool_id)) {
            $this->addParameter('pool_id', $pool_id);
            $where[] = 'Media.PoolId = :pool_id';
        }

        if ($inchanger === true) {
            $this->addParameter('inchanger', 1);
            $where[] = 'Media.inchanger = :inchanger';
        }

        $fields = array('Media.volumename', 'Media.volbytes', 'Media.voljobs', 'Media.volstatus', 'Media.mediatype', 'Media.lastwritten', 
        'Media.volretention', 'Media.slot', 'Media.inchanger', 'Pool.Name AS pool_name');

        $query = CDBQuery::get_Select( array('table'=> $this->tablename,
                                            'fields' => $fields,
                                            'orderby' => "$orderby $orderasc",
                                            'join' => array(
                                                array('table' => 'Pool', 'condition' => 'Media.poolid = Pool.poolid')
                                            ),
                                            'where' => $where,
                                            'limit' => $limit
                                        ), $this->get_driver_name());

        $result = $this->run_query($query);

        foreach ($result->fetchAll() as $volume) {
            $volumes_list[] = $volume;
        }

        return $volumes_list;
    }
}
