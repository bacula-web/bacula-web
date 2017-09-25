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

 $clientid = '';
 $period = 7;
 $client = '';
 $client_jobs = array();
 $backup_jobs = array();
 $days_stored_bytes = array();
 $days_stored_files = array();

 $client = new Clients_Model();
 
 // Clients list
 $view->assign('clients_list', $client->getClients());

 // Period list
 $periods_list = array( '7' => "Last week", '14' => "Last 2 weeks", '30' => "Last month");
 $view->assign('periods_list', $periods_list);

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
    // Check client_id and period received by POST request
    if (!is_null(CHttpRequest::get_Value('client_id'))) {
       
       $clientid = CHttpRequest::get_Value('client_id');

       // Verify if client_id is a valid integer
       if( !filter_var( $clientid, FILTER_VALIDATE_INT)) {
          throw new Exception('Critical: provided parameter (client_id) is not valid');
       }

       $period = CHttpRequest::get_Value('period');

       // Check if period is an integer and listed in known periods
       if(!array_key_exists( $period, $periods_list)) {
          throw new Exception('Critical: provided value for (period) is unknown or not valid');
       }

       if(!filter_var($period, FILTER_VALIDATE_INT)) {
          throw new Exception('Critical: provided value for (period) is unknown or not valid');
       }

       $view->assign( 'no_report_options', 'false');
       
       // Client informations
       $client_info  = $client->getClientInfos($clientid);
       $view->assign('client_name', $client_info['name']);
       $view->assign('client_os', $client_info['os']);
       $view->assign('client_arch', $client_info['arch']);
       $view->assign('client_version', $client_info['version']);
       
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
          } // end foreach
       } // end foreach
       
       $view->assign('backup_jobs', $backup_jobs);
       
       // Get the last n days interval (start and end)
       $days = DateTimeUtil::getLastDaysIntervals($period);
       
       // Last n days stored Bytes graph
       foreach ($days as $day) {
          $stored_bytes = $jobs->getStoredBytes(array($day['start'], $day['end']), 'ALL', $clientid);
          $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
       } // end foreach
       
       $stored_bytes_chart = new Chart( array( 'type' => 'bar', 
          'name' => 'chart_storedbytes',
          'data' => $days_stored_bytes, 
          'ylabel' => 'Bytes', 
          'uniformize_data' => true ) );
       
       $view->assign('stored_bytes_chart_id', $stored_bytes_chart->name);
       $view->assign('stored_bytes_chart', $stored_bytes_chart->render());
       
       unset($stored_bytes_chart);
       
       // Last n days stored files graph
       foreach ($days as $day) {
          $stored_files = $jobs->getStoredFiles(array($day['start'], $day['end']), 'ALL', $clientid);
          $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
       }
       
       $stored_files_chart = new Chart( array( 'type' => 'bar', 
          'name' => 'chart_storedfiles', 
          'data' => $days_stored_files, 
          'ylabel' => 'Files' ) );
       
       $view->assign('stored_files_chart_id', $stored_files_chart->name);
       $view->assign('stored_files_chart', $stored_files_chart->render());
       
       unset($stored_files_chart);
    }else {
       $view->assign( 'no_report_options', 'true');
    }

} catch (Exception $e) {
    CErrorHandler::displayError($e);
}

 $view->assign('period', $period);

 // Set page name
 $view->assign('page_name', 'Client report');

 // Process and display the template
 $view->render('client-report.tpl');
