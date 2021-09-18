<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright 2010-2021, Davide Franco			                           |
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

class PoolsView extends CView
{
    public function __construct()
    {
        parent::__construct();

        $this->templateName = 'pools.tpl';
        $this->name = 'Pools report';
        $this->title = 'Bacula pool(s) overview';
    }

    public function prepare()
    {
        
        // Get volumes list (pools.tpl)
        $pools = new Pools_Model();
        $pools_list = array();
        $plist = $pools->getPools();

        // Add more details to each pool
        foreach ($plist as $pool) {
            // Total bytes for each pool
            $sql = "SELECT SUM(Media.volbytes) as sumbytes FROM Media WHERE Media.PoolId = '" . $pool['poolid'] . "'";
            $result = $pools->run_query($sql);
            $result = $result->fetchAll();
            $pool['totalbytes'] = CUtils::Get_Human_Size($result[0]['sumbytes']);

            $pools_list[] = $pool;
        }

        $this->assign('pools', $pools_list);
    } // end of preare() method
} // end of class
