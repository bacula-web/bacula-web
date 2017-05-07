<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco                                      |
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
 require_once('core/global.inc.php');

 // Initialise model and view
 $view = new CView();
 $dbSql = new Bweb($view);
 
 require_once('core/const.inc.php');

try {
    if (!is_a($dbSql->db_link, 'PDO')) {
        throw new Exception("Application error: invalid PDO connection object provided");
    }
} catch (Exception $e) {
    CErrorHandler::displayError($e);
}

 $clientid             = '';
 $client             = '';
 $period             = '';
 $client_jobs         = array();
 $backup_jobs         = array();
 $days_stored_bytes = array();
 $days_stored_files = array();

 $job_levels = array(
      'D' => 'Differential',
      'I' => 'Incremental',
      'F' => 'Full',
      'V' => 'InitCatalog',
      'C' => 'Catalog',
      'O' => 'VolumeToCatalog',
      'd' => 'DiskToCatalog',
      'A' => 'Data'
 );

try {
    if (!is_null(CHttpRequest::get_Value('client_id'))) {
        $clientid = CHttpRequest::get_Value('client_id');
    } else {
        throw new Exception("Application error: client ID not specified as expected in Client report page");
    }

   // Check time period
    if (!is_null(CHttpRequest::get_Value('period'))) {
        $period = CHttpRequest::get_Value('period');
    } else {
        throw new Exception("Application error: the period hasn't been provided as expected");
    }
   
   // Client informations
    $client = Clients_Model::getClientInfos($dbSql->db_link, $clientid);
    
   // Get job names for the client
    foreach (Jobs_Model::get_Jobs_List($dbSql->db_link, $clientid) as $jobname) {
        // Last good client's for each backup jobs
        $query  = 'SELECT Job.Name, Job.Jobid, Job.Level, Job.Endtime, Job.Jobbytes, Job.Jobfiles, Status.JobStatusLong FROM Job ';
        $query .= "LEFT JOIN Status ON Job.JobStatus = Status.JobStatus ";
        $query .= "WHERE Job.Name = '$jobname' AND Job.JobStatus = 'T' AND Job.Type = 'B' ";
        $query .= 'ORDER BY Job.EndTime DESC ';
        $query .= 'LIMIT 1';
  
        $jobs_result = CDBUtils::runQuery($query, $dbSql->db_link);
  
        foreach ($jobs_result->fetchAll() as $job) {
            $job['level']     = $job_levels[$job['level']];
            $job['jobfiles']  = CUtils::format_Number($job['jobfiles']);
            $job['jobbytes']  = CUtils::Get_Human_Size($job['jobbytes']);
            $job['endtime']   = date( $dbSql->datetime_format, strtotime($job['endtime']));
          
            $backup_jobs[] = $job;
        }
    }
  
    $view->assign('backup_jobs', $backup_jobs);
  
   // Get the last n days interval (start and end)
    $days = DateTimeUtil::getLastDaysIntervals($period);
  
   // ===============================================================
   // Last n days stored Bytes graph
   // ===============================================================
    $graph = new CGraph("clientreport-graph01.jpg");
  
    foreach ($days as $day) {
        $stored_bytes = Jobs_Model::getStoredBytes($dbSql->db_link, array($day['start'], $day['end']), 'ALL', $clientid);
        $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
    }
  
    $graph->SetData($days_stored_bytes, 'bars', true);
  
   // Graph rendering
    $view->assign('graph_stored_bytes', $graph->Render());

    unset($graph);
  
   // ===============================================================
   // Getting last n days stored files graph
   // ===============================================================
    $graph = new CGraph("clientreport-graph03.jpg");
  
    foreach ($days as $day) {
        $stored_files = Jobs_Model::getStoredFiles($dbSql->db_link, array($day['start'], $day['end']), 'ALL', $clientid);
        $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
    }
  
    $graph->SetData($days_stored_files, 'bars');
    $graph->SetYTitle("Files");
  
   // Graph rendering
    $view->assign('graph_stored_files', $graph->Render());

    unset($graph);
} catch (Exception $e) {
    CErrorHandler::displayError($e);
}

 $view->assign('period', $period);
 $view->assign('client_name', $client['name']);
 $view->assign('client_os', $client['os']);
 $view->assign('client_arch', $client['arch']);
 $view->assign('client_version', $client['version']);

 // Set page name
 $current_page = 'Client report';
 $view->assign('page_name', $current_page);

 // Process and display the template
 $view->render('client-report.tpl');
