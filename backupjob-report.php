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
 include_once( 'core/global.inc.php' );

try {
    // Initialise view and model
    $view = new CView();
    $dbSql = new Bweb($view);
    
    require_once('core/const.inc.php');
    
    $backupjob_name = "";
    $backupjob_bytes = 0;
    $backupjob_files = 0;
    
    $days = array();
    $days_stored_bytes = array();
    $days_stored_files = array();
    
    // Backup job name
    if (!is_null(CHttpRequest::get_value('backupjob_name'))) {
        $backupjob_name = CHttpRequest::get_value('backupjob_name');
    } else {
        throw new Exception("Error: Backup job name not specified");
    }
    
    // Generate Backup Job report period string
    $backupjob_period = "From " . date("Y-m-d", (NOW - WEEK)) . " to " . date("Y-m-d", NOW);
    
    // Stored Bytes on the defined period
    $backupjob_bytes = Jobs_Model::getStoredBytes($dbSql->db_link, array(LAST_WEEK, NOW), $backupjob_name);
    $backupjob_bytes = CUtils::Get_Human_Size($backupjob_bytes);
    
    // Stored files on the defined period
    $backupjob_files = Jobs_Model::getStoredFiles($dbSql->db_link, array(LAST_WEEK, NOW), $backupjob_name);
    $backupjob_files = CUtils::format_Number($backupjob_files);
    
    // Get the last 7 days interval (start and end)
    $days = DateTimeUtil::getLastDaysIntervals(7);
    
    // ===============================================================
    // Last 7 days stored Bytes graph
    // ===============================================================
    $graph = new CGraph("backupjobreport-graph01.jpg");
    
    foreach ($days as $day) {
        $stored_bytes = Jobs_Model::getStoredBytes($dbSql->db_link, array($day['start'], $day['end']), $backupjob_name);
        $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
    }
    
    $graph->SetData($days_stored_bytes, 'bars', true);

    // Graph rendering
    $view->assign('graph_stored_bytes', $graph->Render());

    unset($graph);
    
    // ===============================================================
    // Getting last 7 days stored files graph
    // ===============================================================
    $graph = new CGraph("backupjobreport-graph02.jpg");
    
    foreach ($days as $day) {
        $stored_files = Jobs_Model::getStoredFiles($dbSql->db_link, array($day['start'], $day['end']), $backupjob_name);
        $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
    }
    
    $graph->SetData($days_stored_files, 'bars');
    $graph->SetYTitle("Files");
    
    // Graph rendering
    $view->assign('graph_stored_files', $graph->Render());
    
    unset($graph);
    
    // Get last 10 jobs list
    $query = "SELECT JobId, Level, JobFiles, JobBytes, ReadBytes, JobStatus, StartTime, EndTime, Name ";
    $query .= "FROM Job ";
    $query .= "WHERE Name = '$backupjob_name' ";
    $query .= "ORDER BY EndTime DESC ";
    $query .= "LIMIT 7 ";
    
    $jobs         = array();
    $joblevel     = array('I' => 'Incr', 'D' => 'Diff', 'F' => 'Full');
    
    $result = CDBUtils::runQuery($query, $dbSql->db_link);
    
    foreach ($result->fetchAll() as $job) {
        // Job level description
        $job['joblevel'] = $joblevel[$job['level']];
    
        // Job execution execution time
        $job['elapsedtime'] = DateTimeUtil::Get_Elapsed_Time($job['starttime'], $job['endtime']);
    
        // Compression
        if ($job['jobbytes'] > 0) {
            $compression        = (1-($job['jobbytes'] / $job['readbytes']));
            $job['compression'] = number_format($compression, 2);
        } else {
            $job['compression'] = 'N/A';
        }
                
        // Job speed
        $start         = $job['starttime'];
        $end           = $job['endtime'];
        $seconds 	   = DateTimeUtil::get_ElaspedSeconds($end, $start);

        if ($seconds !== false && $seconds > 0) {
            $speed        = $job['jobbytes'] / $seconds;
            $job['speed'] = CUtils::Get_Human_Size($speed, 2) . '/s';
        } else {
            $job['speed'] = 'N/A';
        }
        
        // Job bytes more easy to read
        $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
        $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);

        // Format date/time 
        $job['starttime'] = date( $dbSql->datetime_format, strtotime($job['starttime']));
        $job['endtime'] = date( $dbSql->datetime_format, strtotime($job['endtime']));

        $jobs[]     = $job;
    } // end while

} catch (Exception $e) {
    CErrorHandler::displayError($e);
}

 $view->assign('jobs', $jobs);
 $view->assign('backupjob_name', $backupjob_name);
 $view->assign('backupjob_period', $backupjob_period);
 $view->assign('backupjob_bytes', $backupjob_bytes);
 $view->assign('backupjob_files', $backupjob_files);

 // Set page name
 $current_page = 'Backup job report';
 $view->assign('page_name', $current_page);
 
 // Process and display the template
 $view->display('backupjob-report.tpl');
