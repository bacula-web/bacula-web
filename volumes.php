<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright 2010-2017, Davide Franco			                   |
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

 session_start();

include_once('core/global.inc.php');

try {
   // Initialise view and model
   $view = new CView();
   $dbSql = new Bweb($view);
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

   $orderby = array('Name' => 'Name', 'MediaId' => 'Id', 'VolBytes' => 'Bytes', 'VolJobs' => 'Jobs');
   $view->assign( 'orderby', $orderby);

   $volume_orderby_filter = 'Name';
   $volume_orderby_asc = 'DESC';

   if( !is_null(CHttpRequest::get_Value('orderby')) ) {
      if( in_array(CHttpRequest::get_Value('orderby'), $orderby) ) {
         $volume_orderby_filter = CHttpRequest::get_Value('orderby');
      }else{
         throw new Exception("Critical: Provided orderby parameter is not correct");
      }
   }
 
   if( !is_null(CHttpRequest::get_Value('orderby_asc')) ) {
      $volume_orderby_asc = 'ASC';
      $view->assign( 'orderby_asc_checked', 'checked');
   }
   
   $view->assign( 'orderby_selected', $volume_orderby_filter);
   
   $poolid = CHttpRequest::get_Value('pool_id');
   
   // If pool_id have been passed in GET request 
   if (!is_numeric($poolid) && !is_null($poolid)) {
      throw new Exception('Invalid pool id (not numeric) provided in Volumes report page');
   }

   foreach ($volumes->getVolumes( $poolid, $volume_orderby_filter, $volume_orderby_asc) as $volume) {
      // Calculate volume expiration
      // 
      // // If volume have already been used
      if ($volume['lastwritten'] != "0000-00-00 00:00:00") {
         // Calculate expiration date only if volume status is Full or Used
         if ($volume['volstatus'] == 'Full' || $volume['volstatus'] == 'Used') {
            $expire_date = strtotime($volume['lastwritten']) + $volume['volretention'];
            $volume['expire'] = date( $dbSql->datetime_format_short, $expire_date);
         } else {
            $volume['expire'] = 'n/a';
         }
      } else {
         $volume['expire'] = 'n/a';
      }

      // Set lastwritten for the volume
      if (empty($volume['lastwritten'])) {
         $volume['lastwritten'] = 'n/a';
      } else {
         // Format lastwritten in custom format if defined in config file
         $volume['lastwritten'] = date( $dbSql->datetime_format, strtotime($volume['lastwritten']));
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
   }
   
   $view->assign('volumes', $volumes_list);
   $view->assign('volumes_count', count($volumes_list));
   $view->assign('volumes_total_bytes', CUtils::Get_Human_Size($volumes_total_bytes));
   
   // Set page name
   $view->assign('page_name', 'Volumes report');

   // Process and display the template
   $view->display('volumes.tpl');

} catch (Exception $e) {
   CErrorHandler::displayError($e);
}
