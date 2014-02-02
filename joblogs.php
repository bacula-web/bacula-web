<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2014, Davide Franco			                            |
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
include_once( 'core/global.inc.php' );

$view 		= new CView();
$dbSql 		= new Bweb($view);
$joblogs 	= array();

$jobid 		= CHttpRequest::get_Value('jobid');

// Prepare and execute SQL statment
$statment 	= array('table' => 'Log', 'where' => array("JobId = '$jobid'"), 'orderby' => 'Time');
$result 	= CDBUtils::runQuery( CDBQuery::get_Select($statment), $dbSql->db_link );

// Processing result
foreach ($result->fetchAll() as $log) {
    // Odd or even row
    if (count($joblogs) % 2) {
        $log['class'] = 'even';
    }

    $joblogs[] = $log;
}

$view->assign('jobid', $jobid);
$view->assign('joblogs', $joblogs);

// Set page name
$view->assign('page_name', 'Job logs');

// Process and display the template 
$view->render('joblogs.tpl');
?>
