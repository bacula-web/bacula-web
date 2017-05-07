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
  require_once('core/global.inc.php');

  $view      = new CView();
  $dbSql     = new Bweb($view);

  // That's horrible, it must be improved
  require_once('core/const.inc.php');

  $query     = "";
  $last_jobs = array();

  // Job Status list
  define('STATUS_ALL', 0);
  define('STATUS_RUNNING', 1);
  define('STATUS_WAITING', 2);
  define('STATUS_COMPLETED', 3);
  define('STATUS_COMPLETED_WITH_ERRORS', 4);
  define('STATUS_FAILED', 5);
  define('STATUS_CANCELED', 6);

  $job_status = array(
      STATUS_ALL => 'All',
      STATUS_RUNNING => 'Running',
      STATUS_WAITING => 'Waiting',
      STATUS_COMPLETED => 'Completed',
      STATUS_COMPLETED_WITH_ERRORS => 'Completed with errors',
      STATUS_FAILED => 'Failed',
      STATUS_CANCELED => 'Canceled'
  );

  $view->assign('job_status', $job_status);

  // Global variables
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

  // Levels list filter
  $levels_list = Jobs_Model::getLevels($dbSql->db_link, $job_levels);
  $levels_list['']  = 'Any';
  $view->assign('levels_list', $levels_list);

  // Clients list filter
  $clients_list     = Clients_Model::getClients($dbSql->db_link);
  $clients_list[0]  = 'Any';
  $view->assign('clients_list', $clients_list);

  // Pools list filer
  $pools_list     = Pools_Model::getPools($dbSql->db_link);
  array_unshift( $pools_list, array('name' => 'Any', 'pool_id' => '0') );
  $view->assign('pools_list', $pools_list);

  $query .= "SELECT Job.JobId, Job.Name AS Job_name, Job.Type, Job.SchedTime, Job.StartTime, Job.EndTime, Job.Level, Job.ReadBytes, Job.JobBytes, Job.JobFiles, Pool.Name, Job.JobStatus, Pool.Name AS Pool_name, Status.JobStatusLong ";
  $query .= "FROM Job ";
  $query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
  $query .= "LEFT JOIN Status ON Job.JobStatus = Status.JobStatus ";

  // Check job status filter
  if (!is_null(CHttpRequest::get_Value('status'))) {
      // Selected job status filter
      switch(CHttpRequest::get_Value('status')) {
          case STATUS_RUNNING:
              $query .= "WHERE Job.JobStatus = 'R' ";
              break;
            case STATUS_WAITING:
                $query .= "WHERE Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
              break;
            case STATUS_COMPLETED:
                $query .= "WHERE Job.JobStatus = 'T' ";
              break;
            case STATUS_COMPLETED_WITH_ERRORS:
		$query .= "WHERE Job.JobStatus = 'E' ";
	    break;
            case STATUS_FAILED:
                $query .= "WHERE Job.JobStatus = 'f' ";
              break;
            case STATUS_CANCELED:
                $query .= "WHERE Job.JobStatus = 'A' ";
              break;
            case STATUS_ALL:
                $query .= "WHERE Job.JobStatus != 'xxxx' "; // This code must be improved
              break;
        }
        $view->assign('job_status_filter', CHttpRequest::get_Value('status'));
    }

  // Selected level filter
    if (!is_null(CHttpRequest::get_Value('level_id'))) {
        $level_id = CHttpRequest::get_Value('level_id');
        $view->assign('level_filter', $level_id);

        if (!is_null(CHttpRequest::get_value('status'))) {
            if (!empty($level_id)) {
                $query    .= "AND Job.Level = '$level_id' ";
            }
        } else {
            if (!empty($level_id)) {
                $query    .= "WHERE Job.Level = '$level_id' ";
            }
        }
    } else {
        $view->assign('level_filter', '');
    }

   // Selected pool filter
   if (!is_null(CHttpRequest::get_Value('pool_id'))) {
      $pool_id = CHttpRequest::get_Value('pool_id');
      $view->assign('pool_filter', $pool_id);

      if (!is_null(CHttpRequest::get_value('status'))) {
         if (!empty($pool_id)) {
            $query    .= "AND Job.PoolId = '$pool_id' ";
         }
      }else {
         if (!empty($pool_id)) {
            $query    .= "WHERE Job.PoolId = '$pool_id' ";
         }
      }
    }else {
      $view->assign('pool_filter', '');
    }

  // Selected client filter
    if (!is_null(CHttpRequest::get_Value('client_id'))) {
        $client_id = CHttpRequest::get_Value('client_id');
        $view->assign('client_filter', $client_id);

        if (!is_null(CHttpRequest::get_value('status'))) {
            if ($client_id != 0) {
                $query    .= "AND Job.ClientId = '$client_id' ";
            }
        } else {
            if ($client_id != 0) {
                $query    .= "WHERE Job.ClientId = '$client_id' ";
            }
        }
    } else {
        $view->assign('client_filter', 0);
    }

    // Selected start time filter
    if (!is_null(CHttpRequest::get_Value('start_time'))) {
        $start_time = CHttpRequest::get_Value('start_time');
        $view->assign('start_time_filter', $start_time);

        if (!is_null(CHttpRequest::get_value('status'))) {
            if (!empty($start_time)) {
                $query    .= "AND Job.StartTime >= '$start_time' ";
            }
        } else {
            if (!empty($start_time)) {
                $query    .= "WHERE Job.StartTime >= '$start_time' ";
            }
        }
    } else {
        $view->assign('start_time_filter', '');
    }

    // Selected end time filter
    if (!is_null(CHttpRequest::get_Value('end_time'))) {
        $end_time = CHttpRequest::get_Value('end_time');
        $view->assign('end_time_filter', $end_time);

        if (!is_null(CHttpRequest::get_value('status'))) {
            if (!empty($end_time)) {
                $query    .= "AND Job.EndTime <= '$end_time' ";
            }
        } else {
            if (!empty($end_time)) {
                $query    .= "WHERE Job.EndTime <= '$end_time' ";
            }
        }
    } else {
        $view->assign('end_time_filter', '');
    }

    $order_by                 = '';
    $order_by_asc             = 'DESC';
    $result_order_asc_checked = '';

  // Order result by
    $result_order = array(
      'SchedTime' => 'Job Scheduled Time',
    'starttime' => 'Job Start Date',
    'endtime'   => 'Job End Date',
      'jobid'     => 'Job Id',
      'Job.Name'  => 'Job Name',
      'jobbytes'  => 'Job Bytes',
      'jobfiles'  => 'Job Files',
      'Pool.Name' => 'Pool Name'
    );
  $view->assign('result_order', $result_order);

  // Order by
  if (!is_null(CHttpRequest::get_Value('orderby'))) {
      $order_by = CHttpRequest::get_Value('orderby');
    } else {
        $order_by = 'jobid';
    }

  // Order by DESC || ASC
    if (!is_null(CHttpRequest::get_Value('result_order_asc'))) {
        $order_by_asc             = CHttpRequest::get_Value('result_order_asc');
        $result_order_asc_checked = 'checked';
    }

    $query .= "ORDER BY $order_by $order_by_asc ";

  // Set selected option in template for Job order and Job order asc (ascendant order)
    $view->assign('result_order_field', $order_by);
    $view->assign('result_order_asc_checked', $result_order_asc_checked);

  // Jobs per page options
    $jobs_per_page = 25;
    $view->assign('jobs_per_page', array( 25 => '25', 50 => '50', 75 => '75', 100 => '100', 150 => '150'));

  // Determine how many jobs per page
  // From config file
    if (FileConfig::get_Value('jobs_per_page') != false) {
        $jobs_per_page = FileConfig::get_Value('jobs_per_page');
    }

  // From $_POST form
    if (!is_null(CHttpRequest::get_Value('jobs_per_page'))) {
        $jobs_per_page = CHttpRequest::get_Value('jobs_per_page');
    }

    $query .= "LIMIT $jobs_per_page";
    $view->assign('jobs_per_page_selected', $jobs_per_page);

  // Parsing jobs result
    $jobsresult = CDBUtils::runQuery($query, $dbSql->db_link);

    foreach ($jobsresult as $job) {
        // Determine icon for job status
        switch($job['jobstatus']) {
            case J_RUNNING:
                $job['Job_icon'] = "play";
                break;
            case J_COMPLETED:
                $job['Job_icon'] = "ok";
                break;
            case J_CANCELED:
                $job['Job_icon'] = "off";
                break;
            case J_COMPLETED_ERROR:
                $job['Job_icon'] = "warning-sign";
                break;
            case J_FATAL:
                $job['Job_icon'] = "remove";
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
                $job['Job_icon'] = "time";
                break;
        } // end switch

        // Job start time, end time and scheduled time in custom format (if defined)
        $job['starttime'] = date( $dbSql->datetime_format, strtotime($job['starttime']));
        $job['endtime'] = date( $dbSql->datetime_format, strtotime($job['endtime'])); 
        $job['schedime'] = date( $dbSql->datetime_format, strtotime($job['schedtime'])); 
        
        $start_time = $job['starttime'];
        $end_time   = $job['endtime'];

        if ($start_time == '0000-00-00 00:00:00' or is_null($start_time) or $start_time == 0) {
            $job['starttime'] = 'n/a';
        }

        if ($end_time == '0000-00-00 00:00:00' or is_null($end_time) or $end_time == 0) {
            $job['endtime'] = 'n/a';
        }

        // Get the job elapsed time completion
        $job['elapsed_time'] = DateTimeUtil::Get_Elapsed_Time($start_time, $end_time);

        // Job Level
        $job['level'] = $job_levels[$job['level']];

        // Job files
        $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);

        // Set default Job speed and compression rate
        $job['speed'] = '0 Mb/s';
        $job['compression'] = 'n/a';

        switch($job['jobstatus']) {
            case J_COMPLETED:
            case J_COMPLETED_ERROR:
            case J_NO_FATAL_ERROR:
            case J_CANCELED:
                // Job speed
                $seconds = DateTimeUtil::get_ElaspedSeconds($end_time, $start_time);
                if ($seconds !== false && $seconds > 0) {
                    $speed     = $job['jobbytes'] / $seconds;
                    $speed     = CUtils::Get_Human_Size($speed, 2) . '/s';
                    $job['speed'] = $speed;
                } else {
                    $job['speed'] = 'n/a';
                }

                // Job compression
                if ($job['jobbytes'] > 0 && $job['type'] == 'B') {
                    $compression        = (1-($job['jobbytes'] / $job['readbytes']));
                    $job['compression'] = number_format($compression, 2);
                } else {
                    $job['compression'] = 'n/a';
                }
            break;
        } // end switch

        // Job size
        $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);

        // Job Pool
        if (is_null($job['pool_name'])) {
            $job['pool_name'] = 'n/a';
        }

        $last_jobs[] = $job;
    } // end foreach




    $view->assign('last_jobs', $last_jobs);

  // Count jobs
    $view->assign('jobs_found', count($last_jobs));
    $view->assign('total_jobs', Jobs_Model::count($dbSql->db_link));

  // Set page name
    $current_page = 'Jobs report';
    $view->assign('page_name', $current_page);

    // Language
    $view->assign('config_language', FileConfig::get_Value('language'));

  // Process and display the template
    $view->render('jobs.tpl');
