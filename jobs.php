<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco			                          |
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
$query 		= "";
$last_jobs 	= array();

// Job Status list
define('STATUS_ALL', 0);
define('STATUS_RUNNING', 1);
define('STATUS_WAITING', 2);
define('STATUS_COMPLETED', 3);
define('STATUS_FAILED', 4);
define('STATUS_CANCELED', 5);

$job_status = array(STATUS_ALL => 'All',
    STATUS_RUNNING => 'Running',
    STATUS_WAITING => 'Waiting',
    STATUS_COMPLETED => 'Completed',
    STATUS_FAILED => 'Failed',
    STATUS_CANCELED => 'Canceled');

$view->assign('job_status', $job_status);

// Global variables
$job_levels = array('D' => 'Diff', 'I' => 'Incr', 'F' => 'Full');

$query .= "SELECT Job.JobId, Job.Name AS Job_name, Job.Type, Job.StartTime, Job.EndTime, Job.Level, Job.JobBytes, Job.JobFiles, Pool.Name, Job.JobStatus, Pool.Name AS Pool_name, Status.JobStatusLong ";
$query .= "FROM Job ";
$query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
$query .= "LEFT JOIN Status ON Job.JobStatus = Status.JobStatus ";

$posts = CHttpRequest::getRequestVars($_POST);

if ($posts != false) {
    switch ($posts['status']) {
        case STATUS_RUNNING:
            $query .= "WHERE Job.JobStatus = 'R' ";
            break;
        case STATUS_WAITING:
            $query .= "WHERE Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
            break;
        case STATUS_COMPLETED:
            $query .= "WHERE Job.JobStatus = 'T' ";
            break;
        case STATUS_FAILED:
            $query .= "WHERE Job.JobStatus IN ('f', 'E') ";
            break;
        case STATUS_CANCELED:
            $query .= "WHERE Job.JobStatus = 'A' ";
            break;
    }
    $view->assign('job_status_filter', $posts['status']);
}

$order_by	  				= '';
$order_by_asc 				= 'DESC';
$result_order_asc_checked	= '';

// Order result by
$result_order = array( 'jobid' => 'Job Id', 'Job.Name' => 'Job name', 'jobbytes' => 'Job Bytes', 'jobfiles' => 'Job Files', 'Pool.Name' => 'Pool name' );
$view->assign('result_order', $result_order);

// Order by
if( isset($posts['orderby']) ) {
	$order_by = $posts['orderby'];
}else{
    $order_by = 'jobid';
}

// Order by DESC | ASC
if( isset( $posts['result_order_asc'] ) ) {
    $order_by_asc = $posts['result_order_asc'];
	$result_order_asc_checked = 'checked';
}

$query .= "ORDER BY $order_by $order_by_asc ";

$view->assign( 'result_order_field', $posts['orderby']);
$view->assign( 'result_order_asc_checked' ,$result_order_asc_checked);

// Jobs per page
$jobs_per_page = array(25 => '25', 50 => '50', 75 => '75', 100 => '100', 150 => '150');

// Determine how many jobs to display
if (isset($posts['jobs_per_page'])) {
    $query .= "LIMIT " . $posts['jobs_per_page'];
    $view->assign('jobs_per_page_selected', $posts['jobs_per_page']);
}else
    $query .= "LIMIT 25 ";

$view->assign('jobs_per_page', $jobs_per_page);

$jobsresult = CDBUtils::runQuery( $query, $dbSql->db_link );

foreach ($jobsresult as $job) {

    // Determine icon for job status
    switch ($job['jobstatus']) {
        case J_RUNNING:
            $job['Job_icon'] = "running.png";
            break;
        case J_COMPLETED:
            $job['Job_icon'] = "ok.png";
            break;
        case J_CANCELED:
            $job['Job_icon'] = "canceled.png";
            break;
        case J_COMPLETED_ERROR:
            $job['Job_icon'] = "warning.png";
            break;
        case J_FATAL:
            $job['Job_icon'] = "error.png";
            break;
        case J_WAITING_CLIENT:
        case J_WAITING_SD:
        case J_WAITING_MOUNT_MEDIA:
        case J_WAITING_NEW_MEDIA:
        case J_WAITING_STORAGE_RES:
        case J_WAITING_JOB_RES:
        case J_WAITING_CLIENT_RES:
        case J_WAITING_MAX_JOBS:
        case J_WAITING_START_TIME:
        case J_NOT_RUNNING:
            $job['Job_icon'] = "waiting.png";
            break;
    } // end switch
	
    // Odd or even row
    if (count($last_jobs) % 2)
        $job['odd_even'] = 'even';

    // Job start time, end time and elapsed time
    $start_time 	= $job['starttime'];
    $end_time 		= $job['endtime'];

    if ($start_time == '0000-00-00 00:00:00' or is_null($start_time) or $start_time == 0)
        $job['starttime'] = 'N/A';

    if ($end_time == '0000-00-00 00:00:00' or is_null($end_time) or $end_time == 0)
        $job['endtime'] = 'N/A';
    
    // Get the job elapsed time completion
	$job['elapsed_time'] = CTimeUtils::Get_Elapsed_Time($start_time, $end_time);

    // Job Level
    $job['level'] = $job_levels[$job['level']];

    // Job files
    $job['jobfiles'] = $dbSql->translate->get_Number_Format($job['jobfiles']);

    // Job size
    $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);

    // Job Pool
    if (is_null($job['pool_name']))
        $job['pool_name'] = 'N/A';

    $last_jobs[] = $job;
} // end foreach

$view->assign('last_jobs', $last_jobs);

// Count jobs
$view->assign('jobs_found', count($last_jobs) );
$view->assign( 'total_jobs', Jobs_Model::count_Jobs( $dbSql->db_link, array( FIRST_DAY, NOW) ) );

// Process and display the template 
$view->render('jobs.tpl');
?>
