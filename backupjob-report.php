<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2013, Davide Franco			                          |
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

 try{
    // Initialise view and model
    $view = new CView();
    $dbSql = new Bweb($view);
    
    $backupjob_name = "";
    $backupjob_bytes = 0;
    $backupjob_files = 0;
    
    $days = array();
    $days_stored_bytes = array();
    $days_stored_files = array();
    
    // ===============================================================
    // Get Backup Job name from GET or POST
    // ===============================================================
    $http_post = CHttpRequest::getRequestVars($_POST);
    $http_get = CHttpRequest::getRequestVars($_GET);
    
    // Backup job name
    if (isset($http_post['backupjob_name']))
        $backupjob_name = $http_post['backupjob_name'];
    elseif (isset($http_get['backupjob_name']))
        $backupjob_name = $http_get['backupjob_name'];
    else
        die("Please specify a backup job name ");
    
    // Generate Backup Job report period string
    $backupjob_period = "From " . date("Y-m-d", (NOW - WEEK)) . " to " . date("Y-m-d", NOW);
    
    // Stored Bytes on the defined period
    $backupjob_bytes = Jobs_Model::getStoredBytes( $dbSql->db_link, array(LAST_WEEK, NOW), $backupjob_name );
    $backupjob_bytes = CUtils::Get_Human_Size($backupjob_bytes);
    
    // Stored files on the defined period
    $backupjob_files = Jobs_Model::getStoredFiles( $dbSql->db_link, array(LAST_WEEK, NOW), $backupjob_name);
    $backupjob_files = CUtils::format_Number( $backupjob_files );
    
    // Get the last 7 days interval (start and end)
    $days = CTimeUtils::getLastDaysIntervals(7);
    
    // ===============================================================
    // Last 7 days stored Bytes graph
    // ===============================================================  
    $graph = new CGraph("graph8.png");
    
    foreach ($days as $day) {
        $stored_bytes = Jobs_Model::getStoredBytes( $dbSql->db_link, array($day['start'], $day['end']), $backupjob_name );
        $stored_bytes = CUtils::Get_Human_Size($stored_bytes, 1, 'GB', false);
        $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
    }
    
    $graph->SetData($days_stored_bytes, 'bars');
    $graph->SetGraphSize(400, 230);
    $graph->SetYTitle("GB");
    
    // Graph rendering
    $view->assign( 'graph_stored_bytes', $graph->Render() );
    
    // ===============================================================
    // Getting last 7 days stored files graph
    // ===============================================================
    $graph = new CGraph("graph9.png");
    
    foreach ($days as $day) {
        $stored_files = Jobs_Model::getStoredFiles( $dbSql->db_link, array($day['start'], $day['end']), $backupjob_name);
        $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
    }
    
    $graph->SetData($days_stored_files, 'bars');
    $graph->SetGraphSize(400, 230);
    $graph->SetYTitle("Files");
    
    // Graph rendering
    $view->assign( 'graph_stored_files', $graph->Render() );
    
    // Get last 10 jobs list
    $query = "SELECT JobId, Level, JobFiles, JobBytes, JobStatus, StartTime, EndTime, Name ";
    $query .= "FROM Job ";
    $query .= "WHERE Name = '$backupjob_name' ";
    $query .= "ORDER BY EndTime DESC ";
    $query .= "LIMIT 7 ";
    
    $jobs 		= array();
    $joblevel 	= array('I' => 'Incr', 'D' => 'Diff', 'F' => 'Full');
    
    $result = CDBUtils::runQuery( $query, $dbSql->db_link );
    
    foreach ($result->fetchAll() as $job) {
        // Job level description
        $job['joblevel'] = $joblevel[$job['level']];
    
        // Job execution execution time
        $job['elapsedtime'] = CTimeUtils::Get_Elapsed_Time($job['starttime'], $job['endtime']);
    
        // odd and even row
        if (count($jobs) % 2)
            $job['odd_even'] = 'even';
    
        // Job bytes more easy to read
        $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
        $job['jobfiles'] = CUtils::format_Number( $job['jobfiles'] );
    
        $jobs[] = $job;
    } // end while

 }catch (Exception $e) {
    CErrorHandler::displayError($e);
 }

 $view->assign('jobs', $jobs);
 $view->assign('backupjob_name', $backupjob_name);
 $view->assign('backupjob_period', $backupjob_period);
 $view->assign('backupjob_bytes', $backupjob_bytes);
 $view->assign('backupjob_files', $backupjob_files);

 // Set page name
 $view->assign('page_name', 'Backup job report');
 
 // Process and display the template 
 $view->display('backupjob-report.tpl');
?>
