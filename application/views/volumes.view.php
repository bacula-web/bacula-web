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

class VolumesView extends CView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'volumes.tpl';
        $this->name = 'Volumes report';
        $this->title = 'Bacula volume(s) overview';
    }
    
    public function prepare()
    {
        $volumes = new Volumes_Model();
        $filteredVolumes = new Volumes_Model();
        $volumes_list = array();
        $volumes_total_bytes = 0;
        $where = null;
        $pool_id = 0;

        // Paginate database query result
        $pagination = new CDBPagination($this);

        // Count volumes
        $volumesTotal = $volumes->count('Media');

        // Volumes status icon
        $volume_status = array( 'Full' => 'fa-battery-full',
            'Archive' => 'fa-file-archive-o',
            'Append' => 'fa-battery-quarter',
            'Recycle' => 'fa-recycle',
            'Read-Only' => 'fa-lock',
            'Disabled' => 'fa-ban',
            'Error' => 'fa-times-circle',
            'Busy' => 'fa-clock-o',
            'Used' => 'fa-battery-quarter',
            'Purged' => 'fa-battery-empty' );

        // Pools list filter
        $pools = new Pools_Model();
        $pools_list = array();
        
        // Create pools list
        foreach ($pools->getPools() as $pool) {
            $pools_list[$pool['poolid']] = $pool['name'];
        }
         
        $pools_list = array( 0 => 'Any') + $pools_list; // Add defautl pool filter
        $this->assign('pools_list', $pools_list);

        if (CHttpRequest::get_Value('filter_pool_id') != null) {
            // Ensure pool_id value is an integer
            if (!is_numeric(CHttpRequest::get_Value('filter_pool_id')) && !is_null(CHttpRequest::get_Value('filter_pool_id'))) {
                throw new Exception('Invalid pool id (not numeric) provided in Volumes report page');
            }
            
            if (CHttpRequest::get_Value('filter_pool_id') !== '0') {
                $pool_id = CHttpRequest::get_Value('filter_pool_id');
                $volumes->addParameter('pool_id', $pool_id);
                $filteredVolumes->addParameter('pool_id', $pool_id);
                $where[] = 'Media.PoolId = :pool_id';
            }
        }
        
        // Order by
        $orderby = array('Name' => 'Name', 'MediaId' => 'Id', 'VolBytes' => 'Bytes', 'VolJobs' => 'Jobs');
        
        // Set order by
        $this->assign('orderby', $orderby);
        $volume_orderby_filter = 'Name';
        $volume_orderby_asc = 'DESC';

        if (!is_null(CHttpRequest::get_Value('orderby'))) {
            if (array_key_exists(CHttpRequest::get_Value('orderby'), $orderby)) {
                $volume_orderby_filter = CHttpRequest::get_Value('orderby');
            } else {
                throw new Exception("Critical: Provided orderby parameter is not correct");
            }
        }
 
        // Set order by filter and checkbox status
        $this->assign('orderby_asc_checked', '');

        if (!is_null(CHttpRequest::get_Value('orderby_asc'))) {
            $volume_orderby_asc = 'ASC';
            $this->assign('orderby_asc_checked', 'checked');
        }
   
        $this->assign('orderby_selected', $volume_orderby_filter);

        // Set inchanger checkbox to unchecked by default
        $this->assign('inchanger_checked', '');

        if (!is_null(CHttpRequest::get_Value('filter_inchanger'))) {
            $volumes->addParameter('inchanger', 1);
            $filteredVolumes->addParameter('inchanger', 1);
            $where[] = 'Media.inchanger = :inchanger';
            $this->assign('inchanger_checked', 'checked');
        }

        $fields = array('Media.volumename', 'Media.volbytes', 'Media.voljobs', 'Media.volstatus', 'Media.mediatype', 'Media.lastwritten', 
        'Media.volretention', 'Media.slot', 'Media.inchanger', 'Pool.Name AS pool_name');

        $sqlQuery = CDBQuery::get_Select(array('table' => 'Media',
                                            'fields' => $fields,
                                            'orderby' => "$volume_orderby_filter $volume_orderby_asc",
                                            'join' => array(
                                                array('table' => 'Pool', 'condition' => 'Media.poolid = Pool.poolid')
                                            ),
                                            'where' => $where,
                                            'limit' => [
                                                'count' => $pagination->getLimit(), 
                                                'offset' => $pagination->getOffset() ]
                                            ),$volumes->get_driver_name());

        foreach($pagination->paginate($volumes->run_query($sqlQuery), $volumesTotal, $filteredVolumes->count('Media', $where)) as $volume) {
            // Calculate volume expiration
            // If volume have already been used
            if ($volume['lastwritten'] != "0000-00-00 00:00:00") {
                // Calculate expiration date only if volume status is Full or Used
                if ($volume['volstatus'] == 'Full' || $volume['volstatus'] == 'Used') {
                    $expire_date = strtotime($volume['lastwritten']) + $volume['volretention'];
                    $volume['expire'] = date($_SESSION['datetime_format_short'], $expire_date);
                } else {
                    $volume['expire'] = 'n/a';
                }
            } else {
                $volume['expire'] = 'n/a';
            }

            // Set lastwritten for the volume
            if (($volume['lastwritten'] == '0000-00-00 00:00:00') || empty($volume['lastwritten'])) {
                $volume['lastwritten'] = 'n/a';
            } else {
                // Format lastwritten in custom format if defined in config file
                $volume['lastwritten'] = date($_SESSION['datetime_format'], strtotime($volume['lastwritten']));
            }

            // Sum volumes bytes
            $volumes_total_bytes += $volume['volbytes'];

            // Get volume used bytes in a human format
            $volume['volbytes'] = CUtils::Get_Human_Size($volume['volbytes']);

            // Update volume inchanger
            if ($volume['inchanger'] == '0') {
                $volume['inchanger'] = '-';
                $volume['slot'] = 'n/a';
            } else {
                $volume['inchanger'] = '<span class="glyphicon glyphicon-ok"></span>';
            }
      
            // Set volume status icon
            $volume['status_icon'] = $volume_status[ $volume['volstatus'] ];

            // Format voljobs
            $volume['voljobs'] = CUtils::format_Number($volume['voljobs']);
      
            // add volume in volumes list array
            $volumes_list[] = $volume;
        } // end foreach
   
        $this->assign('pool_id', $pool_id);
        $this->assign('volumes', $volumes_list);

        $this->assign('volumes_count', $volumes->count());
        $this->assign('volumes_total_bytes', CUtils::Get_Human_Size($volumes_total_bytes));
    } // end of preare() method
} // end of class
