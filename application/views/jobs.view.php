<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
 * 
 * This file is part of Bacula-Web.
 * 
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see 
 * <https://www.gnu.org/licenses/>.
 */

class JobsView extends CView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'jobs.tpl';
        $this->name = 'Jobs report';
        $this->title = 'Bacula jobs overview';
    }

    public function prepare()
    {
        $jobs = new JobTable(DatabaseFactory::getDatabase());
        $filteredJobs = new JobTable(DatabaseFactory::getDatabase());

        $where = null;

        // Total of Jobs
        $totalJobs = $jobs->count();

        // Paginate database query result
        $pagination = new CDBPagination($this);

        // This is horrible, it must be improved :(
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
        $filter_jobtype = '0';
        $filter_jobstatus = STATUS_ALL;
        $filter_joblevel = 0;
        $filter_poolid = 0;
        $filter_clientid = 0;
        $filter_job_starttime = null;
        $filter_job_endtime = null;
        $job_orderby_filter = 'jobid';
        $job_orderby_asc_filter = 'DESC';

        // Job client id filter
        if (CHttpRequest::get_Value('filter_clientid') != null) {
            $filter_clientid = (int) CHttpRequest::get_Value('filter_clientid');
        }

        // Job type filter
        if (CHttpRequest::get_Value('filter_jobtype') !== null) {
            // if provided filter_jobtype is not part of valid job type, we simply ignore it
            if (array_key_exists(CHttpRequest::get_Value('filter_jobtype'), $job_types)) {
                $filter_jobtype = CHttpRequest::get_Value('filter_jobtype');
            }
        }

        // Job status filter
        if (CHttpRequest::get_Value('filter_jobstatus') != null) {
            $filter_jobstatus = (int) CHttpRequest::get_Value('filter_jobstatus');
        }

        // Job level id filter
        if (CHttpRequest::get_Value('filter_joblevel') != null) {
            $filter_joblevel = CHttpRequest::get_Value('filter_joblevel');
        }

        // Job pool id filter
        if (CHttpRequest::get_Value('filter_poolid') != null) {
            $filter_poolid = (int) CHttpRequest::get_Value('filter_poolid');
        }

        // Job starttime filter
        if (CHttpRequest::get_Value('filter_job_starttime') != null) {
            $filter_job_starttime = CHttpRequest::get_Value('filter_job_starttime');
        }

        // Job endtime filter
        if (CHttpRequest::get_Value('filter_job_endtime') != null) {
            $filter_job_endtime = CHttpRequest::get_Value('filter_job_endtime');
        }

        // Job orderby filter
        if (CHttpRequest::get_Value('job_orderby') != null) {
            // if provided job_orderby is not part of valid job order field, we simply ignore it
            if (array_key_exists(CHttpRequest::get_Value('job_orderby'), $result_order)) {
                $job_orderby_filter = CHttpRequest::get_Value('job_orderby');
            }
        }

        // Job orderby asc filter
        if (CHttpRequest::get_Value('job_orderby_asc') != null) {
            $job_orderby_asc_filter = CHttpRequest::get_Value('job_orderby_asc');
        }

        // Assign variables to template
        $this->assign('filter_jobtype', $filter_jobtype);
        $this->assign('filter_jobstatus', $filter_jobstatus);
        $this->assign('filter_joblevel', $filter_joblevel);
        $this->assign('filter_poolid', $filter_poolid);
        $this->assign('filter_clientid', $filter_clientid);
        $this->assign('filter_job_starttime', $filter_job_starttime);
        $this->assign('filter_job_endtime', $filter_job_endtime);

        // Clients list filter
        $clients = new ClientTable(DatabaseFactory::getDatabase());
        $clients_list = $clients->getClients();
        $clients_list[0] = 'Any';
        $this->assign('clients_list', $clients_list);

        // Pools list filer
        $pools = new PoolTable(DatabaseFactory::getDatabase());
        $pools_list = array();

        foreach ($pools->getPools() as $pool) {
            $pools_list[$pool['poolid']] = $pool['name'];
        }

        $pools_list[0] = 'Any';
        $this->assign('pools_list', $pools_list);

        /**
         * Job status filter
         */
        switch ($filter_jobstatus) {
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
        if ($filter_joblevel != '0') {
            $jobs->addParameter('job_level', $filter_joblevel);
            $filteredJobs->addParameter('job_level', $filter_joblevel);
            $where[] .= "Job.Level = :job_level ";
        }
 
        // Selected pool filter
        if ($filter_poolid != '0') {
            $jobs->addParameter('pool_id', $filter_poolid);
            $filteredJobs->addParameter('pool_id', $filter_poolid);
            $where[] .= "Job.PoolId = :pool_id ";
        }

        if($filter_jobtype !== '0') {
            $jobs->addParameter('job_type', $filter_jobtype);
            $filteredJobs->addParameter('job_type', $filter_jobtype);
            $where[] = "Job.Type = :job_type";
        }

        // Selected client filter
        if ($filter_clientid != '0') {
            $jobs->addParameter('client_id', $filter_clientid);
            $filteredJobs->addParameter('client_id', $filter_clientid);
            $where[] .= "Job.ClientId = :client_id";
        }

        // Selected job start time filter
        if (!is_null($filter_job_starttime) && !empty($filter_job_starttime)) {
            if (DateTimeUtil::checkDate($filter_job_starttime)) {
                $where[] = "Job.StartTime >= '$filter_job_starttime'";
            }
        }
        
        // Selected job end time filter
        if (!is_null($filter_job_endtime) && !empty($filter_job_starttime)) {
            if (DateTimeUtil::checkDate($filter_job_endtime)) {
                $where[] = "Job.EndTime <= '$filter_job_endtime'";
            }
        }

        $this->assign('result_order', $result_order);
        $orderby = "$job_orderby_filter $job_orderby_asc_filter ";

        // Set selected option in template for Job order and Job order asc (ascendant order)
        $this->assign('result_order_field', $job_orderby_filter);

        if ($job_orderby_asc_filter == 'ASC') {
            $this->assign('result_order_asc_checked', 'checked');
        } else {
            $this->assign('result_order_asc_checked', '');
        }

        // Parsing jobs result
        $sqlQuery = CDBQuery::get_Select(array('table' => 'Job',
            'fields' => $fields,
            'where' => $where,
            'orderby' => $orderby,
            'limit' => [
                'count' => $pagination->getLimit(), 
                'offset' => $pagination->getOffset() 
            ],
            'join' => array(
                array('table' => 'Pool', 'condition' => 'Job.PoolId = Pool.PoolId'),
                array('table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus')
            ) ),$jobs->get_driver_name());
        
        foreach( $pagination->paginate($jobs->run_query($sqlQuery), $totalJobs, $filteredJobs->count('Job', $where)) as $job) {
            // Determine icon for job status
            switch ($job['jobstatus']) {
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
            } else {
                $job['starttime'] = date($_SESSION['datetime_format'], strtotime($job['starttime']));
            }
       
            if ($end_time == '0000-00-00 00:00:00' || is_null($end_time) || $end_time == 0) {
                $job['endtime'] = 'n/a';
            } else {
                $job['endtime'] = date($_SESSION['datetime_format'], strtotime($job['endtime']));
            }
       
            // Get the job elapsed time completion
            $job['elapsed_time'] = DateTimeUtil::Get_Elapsed_Time($start_time, $end_time);

            $job['schedtime'] = date($_SESSION['datetime_format'], strtotime($job['schedtime']));
       
            // Job Level
            if (isset($job_levels[$job['level']])) {
                $job['level'] = $job_levels[$job['level']];
            } else {
                $job['level'] = 'n/a';
            }
       
            // Job files
            $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);

            // Set default Job speed and compression rate
            $job['speed'] = '0 Mb/s';
            $job['compression'] = 'n/a';
       
            switch ($job['jobstatus']) {
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
                if ($job['jobbytes'] > 0 && $job['type'] == 'B' && $job['jobstatus'] != J_CANCELED && ($job['jobbytes'] < $job['readbytes'])) {
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
        
        $this->assign('last_jobs', $last_jobs);
    
        // Count jobs
        $this->assign('jobs_found', count($last_jobs));
    }
} // end of class
