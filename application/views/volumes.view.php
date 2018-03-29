<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright 2010-2018, Davide Franco			                           |
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

class VolumesView extends CView {
    
    public function __construct() {
        
        $this->templateName = 'volumes.tpl';
        $this->name = 'Volumes report';
        $this->title = 'Bacula volume(s) overview';
        
        parent::init();
    }
    
    public function prepare() {
        
        $volumes = new Volumes_Model();
        $volumes_list = array();
        $volumes_total_bytes = 0;

        // Volumes status icon
        $volume_status = array( 'Full' => 'fa-battery-full',
            'Archive' => 'fa-file-archive-o',
            'Append' => 'fa-battery-quarter',
            'Recycle' => 'fa-recycle',
            'Read-Only' => 'fa-lock',
            'Disabled' => 'fa-ban',
            'Error' => 'fa-times-circle',
            'Busy' => 'fa-clock-o' );

        $orderby = array('Name' => 'Name', 'MediaId' => 'Id', 'VolBytes' => 'Bytes', 'VolJobs' => 'Jobs', 'InChanger' => 'InChanger');
        $this->assign( 'orderby', $orderby);
        
        $volume_orderby_filter = 'Name';
        $volume_orderby_asc = 'DESC';

        $orderby_extra = array('Name' => 'Name', 'MediaId' => 'Id', 'VolBytes' => 'Bytes', 'VolJobs' => 'Jobs', 'InChanger' => 'InChanger', '' => 'null');
        $this->assign ( 'orderby_extra', $orderby);
        
        $volume_orderby_filter_extra = '';
        $volume_orderby_asc_extra = 'DESC';
        

        if( !is_null(CHttpRequest::get_Value('orderby_extra')) ) {
            if( array_key_exists(CHttpRequest::get_Value('orderby_extra'), $orderby_extra) ) {
                $volume_orderby_filter_extra = CHttpRequest::get_Value('orderby_extra');
            }else{
                throw new Exception("Critical: Provided orderby_extra parameter is not correct");
            }
        }
 
        if( !is_null(CHttpRequest::get_Value('orderby_asc_extra')) ) {
            $volume_orderby_asc_extra = 'ASC';
            $this->assign( 'orderby_asc_extra_checked', 'checked');
        }
   
        $this->assign( 'orderby_extra_selected', $volume_orderby_filter_extra);
   
        if( !is_null(CHttpRequest::get_Value('orderby')) ) {
            if( array_key_exists(CHttpRequest::get_Value('orderby'), $orderby) ) {
                $volume_orderby_filter = CHttpRequest::get_Value('orderby');
            }else{
                throw new Exception("Critical: Provided orderby parameter is not correct");
            }
        }
 
        if( !is_null(CHttpRequest::get_Value('orderby_asc')) ) {
            $volume_orderby_asc = 'ASC';
            $this->assign( 'orderby_asc_checked', 'checked');
        }

        $this->assign( 'orderby_selected', $volume_orderby_filter);

        $poolid = CHttpRequest::get_Value('pool_id');
   
        // If pool_id have been passed in GET request 
        if (!is_numeric($poolid) && !is_null($poolid)) {
            throw new Exception('Invalid pool id (not numeric) provided in Volumes report page');
        }

        foreach ($volumes->getVolumes( $poolid, $volume_orderby_filter, $volume_orderby_asc, $volume_orderby_filter_extra, $volume_orderby_asc_extra) as $volume) {
            // Calculate volume expiration
            // If volume have already been used
            if ($volume['lastwritten'] != "0000-00-00 00:00:00") {
                // Calculate expiration date only if volume status is Full or Used
                if ($volume['volstatus'] == 'Full' || $volume['volstatus'] == 'Used') {
                    $expire_date = strtotime($volume['lastwritten']) + $volume['volretention'];
                    $volume['expire'] = date( $_SESSION['datetime_format_short'], $expire_date);
                } else {
                    $volume['expire'] = 'n/a';
                }
            }else {
                $volume['expire'] = 'n/a';
            }

            // Set lastwritten for the volume
            if( ($volume['lastwritten'] == '0000-00-00 00:00:00') || empty($volume['lastwritten']) ) {
                $volume['lastwritten'] = 'n/a';
            } else {
                // Format lastwritten in custom format if defined in config file
                $volume['lastwritten'] = date( $_SESSION['datetime_format'], strtotime($volume['lastwritten']));
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
   
        $this->assign('volumes', $volumes_list);
        $this->assign('volumes_count', count($volumes_list));
        $this->assign('volumes_total_bytes', CUtils::Get_Human_Size($volumes_total_bytes));

    } // end of preare() mthod
} // end of class
