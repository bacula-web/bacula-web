<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco			                            |
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
    $view         = new CView();
    $dbSql         = new Bweb($view);
    $joblogs     = array();

    $jobid         = CHttpRequest::get_Value('jobid');
    
    // If $_GET['jobid'] is null and is not a number, throw an Exception
    if (is_null($jobid) or !is_numeric($jobid)) {
        throw new Exception('Invalid job id (invalid or null) provided in Job logs report');
    }

    // Prepare and execute SQL statment
    $statment     = array('table' => 'Log', 'where' => array("JobId = '$jobid'"), 'orderby' => 'Time');
    $result     = CDBUtils::runQuery(CDBQuery::get_Select($statment), $dbSql->db_link);

    // Processing result
    foreach ($result->fetchAll() as $log) {
       $log['logtext']  = nl2br($log['logtext']);
       $log['time']     = date( $dbSql->datetime_format, strtotime($log['time']) ); 
       $joblogs[]       = $log;
    }

    $view->assign('jobid', $jobid);
    $view->assign('joblogs', $joblogs);

    // Set page name
    $current_page = 'Job logs';
    $view->assign('page_name', $current_page);

    // Process and display the template
    $view->render('joblogs.tpl');
} catch (Exception $e) {
    CErrorHandler::displayError($e);
}
