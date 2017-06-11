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

 // Initialise view and model
 $view = new CView();
 $dbSql = new Bweb($view);

 $volumes = new Volumes_Model();
 $volumes_list = array();
 $volumes_total_bytes = 0;

 foreach ($volumes->getVolumes() as $volume) {
     // Set lastwritten for the volume
   if (empty($volume['lastwritten'])) {
       $volume['lastwritten'] = 'n/a';
   } else {
       // Format lastwritten in custom format if defined in config file
     if (FileConfig::get_Value('datetime_format') != false) {
         $volume['lastwritten'] = date(FileConfig::get_Value('datetime_format'), strtotime($volume['lastwritten']));
     }
   }

   // Sum volumes bytes
   $volumes_total_bytes += $volume['volbytes'];

   // Get volume used bytes in a human format
   $volume['volbytes'] = CUtils::Get_Human_Size($volume['volbytes']);

   // Update volume inchanger
   if ($volume['inchanger'] == '0') {
       $volume['inchanger'] = '-';
   } else {
       $volume['inchanger'] = '<span class="glyphicon glyphicon-ok"></span>';
   }

   // If volume have already been used
   if ($volume['lastwritten'] != "0000-00-00 00:00:00") {
       // Calculate expiration date if the volume status is Full or Used
     if ($volume['volstatus'] == 'Full' || $volume['volstatus'] == 'Used') {
         $expire_date = strtotime($volume['lastwritten']) + $volume['volretention'];
         $volume['expire'] = strftime("%Y-%m-%d", $expire_date);
     } else {
         $volume['expire'] = 'n/a';
     }
   } else {
       $volume['expire'] = 'n/a';
   }

   // Format voljobs
   $volume['voljobs'] = CUtils::format_Number($volume['voljobs']);

   // add volume in volumes list array
   $volumes_list[] = $volume;
 }

 $view->assign('volumes', $volumes_list);
 $view->assign('volumes_count', count($volumes_list));
 $view->assign('volumes_total_bytes', CUtils::Get_Human_Size($volumes_total_bytes));

 // Set page name
 $current_page = 'Volumes report';
 $view->assign('page_name', $current_page);

 // Process and display the template
 $view->display('volumes.tpl');
