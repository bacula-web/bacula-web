<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright 2010-2016, Davide Franco			                           |
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

 $result = $dbSql->GetVolumeList();
 // Looping over the array to change the 'expire' and 'lastwritten' fields with the date/time format
 foreach ($result as $key1 => $value1) {
    foreach ($result[$key1]['volumes'] as $key2 => $value2) {
       $expire      =  $result[$key1]['volumes'][$key2]['expire'];
       $lastwritten =  $result[$key1]['volumes'][$key2]['lastwritten'];
       $result[$key1]['volumes'][$key2]['expire'] = CUtils::format_DateTime($expire, $config['date_format']);;
       $result[$key1]['volumes'][$key2]['lastwritten'] = CUtils::format_DateTime($lastwritten, $config['datetime_format']);;
    }
 }
 // Get volumes list (pools.tpl)
 $view->assign('pools', $result);

 // Set page name
 $current_page = 'Pools and volumes report';
 $view->assign('page_name', $current_page);
 
 // Process and display the template
 $view->display('pools.tpl');
