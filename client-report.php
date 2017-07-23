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
    $client       = new Clients_Model();
    $client_info  = $client->getClientInfos($clientid);
    
    // Get job names for the client
    $jobs = new Jobs_Model();

    foreach ($jobs->get_Jobs_List($clientid) as $jobname) {
        // Last good client's for each backup jobs
        $query  = 'SELECT Job.Name, Job.Jobid, Job.Level, Job.Endtime, Job.Jobbytes, Job.Jobfiles, Status.JobStatusLong FROM Job ';
        $query .= "LEFT JOIN Status ON Job.JobStatus = Status.JobStatus ";
        $query .= "WHERE Job.Name = '$jobname' AND Job.JobStatus = 'T' AND Job.Type = 'B' ";
        $query .= 'ORDER BY Job.EndTime DESC ';
        $query .= 'LIMIT 1';
  
        $jobs_result = $jobs->run_query($query);
  
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
   foreach ($days as $day) {
      $stored_bytes = $jobs->getStoredBytes(array($day['start'], $day['end']), 'ALL', $clientid);
      $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
   }
    
    $stored_bytes_chart = new Chart( array( 'type' => 'bar', 'name' => 'chart_storedbytes',
       'data' => $days_stored_bytes ) );
    
    $view->assign('stored_bytes_chart_id', $stored_bytes_chart->name);
    $view->assign('stored_bytes_chart', $stored_bytes_chart->render());
   
    unset($stored_bytes_chart);

   // ===============================================================
   // Getting last n days stored files graph
   // ===============================================================

    foreach ($days as $day) {
        $stored_files = $jobs->getStoredFiles(array($day['start'], $day['end']), 'ALL', $clientid);
        $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
    }
    
    $stored_files_chart = new Chart( array( 'type' => 'bar', 'name' => 'chart_storedfiles', 'data' => $days_stored_files ) );
    
    $view->assign('stored_files_chart_id', $stored_files_chart->name);
    $view->assign('stored_files_chart', $stored_files_chart->render());
    
    unset($stored_files_chart);

} catch (Exception $e) {
    CErrorHandler::displayError($e);
}

 $view->assign('period', $period);
 $view->assign('client_name', $client_info['name']);
 $view->assign('client_os', $client_info['os']);
 $view->assign('client_arch', $client_info['arch']);
 $view->assign('client_version', $client_info['version']);

 // Set page name
 $current_page = 'Client report';
 $view->assign('page_name', $current_page);

 // Process and display the template
 $view->render('client-report.tpl');
