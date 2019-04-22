<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2019, Davide Franco			                            |
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

class JobsView extends CView {

    public function __construct() {
        
        $this->templateName = 'jobs.tpl';
        $this->name = 'Jobs report';
        $this->title = 'Bacula jobs overview';

        parent::init();
    }

    public function prepare() {
        
        $jobs = new Jobs_Model();
        $where = null;

        // That's horrible, it must be improved
        require_once('core/const.inc.php');

        $fields = array( 'Job.JobId', 'Job.Name AS Job_name', 'Job.Type', 
            'Job.SchedTime', 'Job.StartTime', 'Job.EndTime', 'Job.Level', 
            'Job.ReadBytes', 'Job.JobBytes', 'Job.JobFiles', 
            'Pool.Name', 'Job.JobStatus', 'Pool.Name AS Pool_name', 'Status.JobStatusLong',
        );
   
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

        // Global variables
        $job_levels = array( 'D' => 'Differential',
            'I' => 'Incremental',
            'F' => 'Full',
            'V' => 'InitCatalog',
            'C' => 'Catalog',
            'O' => 'VolumeToCatalog',
            'd' => 'DiskToCatalog',
            'A' => 'Data'
        );

        // Levels list filter
        $levels_list = $jobs->getLevels($job_levels);
        $levels_list['0']  = 'Any';
        $this->assign('levels_list', $levels_list);

        $last_jobs = array();
        
        // Job Status list
        define('STATUS_ALL', 0);
        define('STATUS_RUNNING', 1);
        define('STATUS_WAITING', 2);
        define('STATUS_COMPLETED', 3);
        define('STATUS_COMPLETED_WITH_ERRORS', 4);
        define('STATUS_FAILED', 5);
        define('STATUS_CANCELED', 6);

        $job_status = array( STATUS_ALL => 'All',
            STATUS_RUNNING => 'Running',
            STATUS_WAITING => 'Waiting',
            STATUS_COMPLETED => 'Completed',
            STATUS_COMPLETED_WITH_ERRORS => 'Completed with errors',
            STATUS_FAILED => 'Failed',
            STATUS_CANCELED => 'Canceled'
        );

        $this->assign('job_status', $job_status);

        // Job types
        $job_types = array( 'B' => 'Backup',
            'M' => 'Migrated',
            'V' => 'Verify',
            'R' => 'Restore',
            'D' => 'Admin',
            'A' => 'Archive',
            'C' => 'Copy',
            'g' => 'Migration' 
        );
 
        // Jobs type filter
        $job_types_list = $jobs->getUsedJobTypes($job_types);
        $job_types_list['0'] = 'Any'; 
        $this->assign('job_types_list', $job_types_list);

        // Define default values for each filter 
        $job_type_filter = '0';
        $job_status_filter = STATUS_ALL;
        $job_level_filter = 0;
        $job_poolid_filter = 0;
        $job_clientid_filter = 0;
        $job_starttime_filter = null;
        $job_endtime_filter = null;
        $job_orderby_filter = 'jobid';
        $job_orderby_asc_filter = 'DESC';

        // Job client id filter
        if( CHttpRequest::get_Value('job_clientid_filter') != NULL ){
            $job_clientid_filter = (int) CHttpRequest::get_Value('job_clientid_filter');
        }

        // Job type filter
        if( CHttpRequest::get_Value('job_type_filter') != NULL ){
            // if provided job_type_filter is not part of valid job type, we simply ignore it
            if(array_key_exists( CHttpRequest::get_Value('job_type_filter'), $job_types)){
                $job_type_filter = CHttpRequest::get_Value('job_type_filter');
            }
        }

        // Job status filter
        if( CHttpRequest::get_Value('job_status_filter') != NULL ){
            $job_status_filter = (int) CHttpRequest::get_Value('job_status_filter');
        }

        // Job level id filter
        if( CHttpRequest::get_Value('job_levelid_filter') != NULL ){
            $job_level_filter = CHttpRequest::get_Value('job_levelid_filter');
        }

        // Job pool id filter
        if( CHttpRequest::get_Value('jobs_poolid_filter') != NULL ){
            $job_poolid_filter = (int) CHttpRequest::get_Value('jobs_poolid_filter');
        }

        // Job starttime filter
        if( CHttpRequest::get_Value('job_starttime_filter') != NULL ){
            $job_starttime_filter = CHttpRequest::get_Value('job_starttime_filter');
        }

        // Job endtime filter
        if( CHttpRequest::get_Value('job_endtime_filter') != NULL ){
            $job_endtime_filter = CHttpRequest::get_Value('job_endtime_filter');
        }

        // Job orderby filter
        if( CHttpRequest::get_Value('job_orderby') != NULL ){
            // if provided job_orderby is not part of valid job order field, we simply ignore it
            if(array_key_exists( CHttpRequest::get_Value('job_orderby'), $result_order)){
                $job_orderby_filter = CHttpRequest::get_Value('job_orderby');
            }
        }

        // Job orderby asc filter
        if( CHttpRequest::get_Value('job_orderby_asc') != NULL ){
            $job_orderby_asc_filter = CHttpRequest::get_Value('job_orderby_asc');
        }

        // Assign variables to template
        $this->assign( 'job_type_filter', $job_type_filter);
        $this->assign( 'job_status_filter', $job_status_filter);
        $this->assign( 'job_level_filter', $job_level_filter);
        $this->assign( 'job_poolid_filter', $job_poolid_filter);
        $this->assign( 'job_clientid_filter', $job_clientid_filter);
        $this->assign( 'job_starttime_filter', $job_starttime_filter);
        $this->assign( 'job_endtime_filter', $job_endtime_filter);

        // Clients list filter
        $clients = new Clients_Model();
        $clients_list = $clients->getClients();
        $clients_list[0] = 'Any';
        $this->assign('clients_list', $clients_list);

        // Pools list filer
        $pools = new Pools_Model();
        $pools_list = array();

        foreach( $pools->getPools() as $pool ) {
            $pools_list[$pool['poolid']] = $pool['name'];
        }

        $pools_list[0] = 'Any';
        $this->assign('pools_list', $pools_list);

        // Check job status filter
        // Selected job status filter
  
        switch($job_status_filter) {
        case STATUS_RUNNING:
            $where[] = "Job.JobStatus = 'R' ";
            break;
        case STATUS_WAITING:
            $where[] = "Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
            break;
        case STATUS_COMPLETED:
            $where[] = "Job.JobStatus = 'T' ";
            break;
        case STATUS_COMPLETED_WITH_ERRORS:
            $where[] = "Job.JobStatus = 'E' ";
            break;
        case STATUS_FAILED:
            $where[] = "Job.JobStatus = 'f' ";
            break;
        case STATUS_CANCELED:
            $where[] = "Job.JobStatus = 'A' ";
            break;
        case STATUS_ALL:
            $where[] = "Job.JobStatus != 'xxxx' "; // This code must be improved
            break;
        } // end switch

        // Selected level filter
        if( $job_level_filter != '0' ) {
            $jobs->addParameter( 'job_level', $job_level_filter);
            $where[] .= "Job.Level = :job_level ";
        }
 
        // Selected pool filter
        if( $job_poolid_filter != '0') {
            $jobs->addParameter( 'pool_id', $job_poolid_filter);
            $where[] .= "Job.PoolId = :pool_id ";
        }

        // Selected job type filter 
        if($job_type_filter != '0') {
            $where[] = "Job.Type = '" . $job_type_filter . "'";
        }

        // Selected client filter
        if( $job_clientid_filter != '0') {
            $where[] .= "Job.ClientId = '$job_clientid_filter'";
        }

        // Selected start time filter
        if(!is_null($job_starttime_filter) && !empty($job_starttime_filter)) {
            if(DateTimeUtil::checkDate($job_starttime_filter)) { 
                $where[] = "Job.StartTime >= '$job_starttime_filter'"; 
            }
        }
        
        if(!is_null($job_endtime_filter) && !empty($job_starttime_filter)) {
            if(DateTimeUtil::checkDate($job_endtime_filter)) { 
                $where[] = "Job.EndTime <= '$job_endtime_filter'"; 
            }
        }


        $this->assign('result_order', $result_order);
        $orderby = "$job_orderby_filter $job_orderby_asc_filter ";

        // Set selected option in template for Job order and Job order asc (ascendant order)
        $this->assign('result_order_field', $job_orderby_filter);

        if($job_orderby_asc_filter == 'ASC') {
            $this->assign('result_order_asc_checked', 'checked');
        }

        // Parsing jobs result
        $sqlQuery = CDBQuery::get_Select( array('table' => 'Job', 
            'fields' => $fields, 
            'where' => $where, 
            'orderby' => $orderby,
            'join' => array( 
                array('table' => 'Pool', 'condition' => 'Job.PoolId = Pool.PoolId'),
                array('table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus')
            ) ) );

        $jobsresult = $jobs->run_query($sqlQuery);

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
            case J_VERIFY_FOUND_DIFFERENCES:
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
       
            $start_time = $job['starttime'];
            $end_time   = $job['endtime'];
       
            if ($start_time == '0000-00-00 00:00:00' || is_null($start_time) || $start_time == 0) {
                $job['starttime'] = 'n/a';
            }
       
            if ($end_time == '0000-00-00 00:00:00' || is_null($end_time) || $end_time == 0) {
                $job['endtime'] = 'n/a';
            }
       
            // Get the job elapsed time completion
            $job['elapsed_time'] = DateTimeUtil::Get_Elapsed_Time($start_time, $end_time);

            // Job start time, end time and scheduled time in custom format (if defined)
            $job['starttime'] = date( $_SESSION['datetime_format'], strtotime($job['starttime']));
            $job['endtime'] = date( $_SESSION['datetime_format'], strtotime($job['endtime'])); 
            $job['schedtime'] = date( $_SESSION['datetime_format'], strtotime($job['schedtime'])); 
       
            // Job Level
            if(isset($job_levels[$job['level']])) {
                $job['level'] = $job_levels[$job['level']];
            }else
            {
                $job['level'] = 'n/a';
            }
       
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

                if($seconds !== false && $seconds > 0) {
                    $speed     = $job['jobbytes'] / $seconds;
                    $speed     = CUtils::Get_Human_Size($speed, 2) . '/s';
                    $job['speed'] = $speed;
                }else {
                    $job['speed'] = 'n/a';
                }
          
                // Job compression
                if($job['jobbytes'] > 0 && $job['type'] == 'B' && $job['jobstatus'] != J_CANCELED) {
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
            if(is_null($job['pool_name'])) {
                $job['pool_name'] = 'n/a';
            }
       
            $last_jobs[] = $job;
        } // end foreach
        
        $this->assign('last_jobs', $last_jobs);
    
        // Count jobs
        $this->assign('jobs_found', count($last_jobs));

    }
} // end of class
