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

class ClientView extends CView
{
    public function __construct()
    {
        parent::__construct();

        $this->templateName = 'client-report.tpl';
        $this->name = 'Client report';
        $this->title = 'Report per Bacula client';
    }

    public function prepare()
    {
        require_once('core/const.inc.php');
        
        $period = 7;
        $clientid = null;
        $backup_jobs = array();
        $days_stored_bytes = array();
        $days_stored_files = array();

        // Get job names for the client
        $jobs = new Jobs_Model();

        $client = new Clients_Model();
 
        // Clients list
        $this->assign('clients_list', $client->getClients());

        // Period list
        $periods_list = array( '7' => "Last week", '14' => "Last 2 weeks", '30' => "Last month");
        $this->assign('periods_list', $periods_list);

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

        // Check client_id and period received by POST request
        if (!is_null(CHttpRequest::get_Value('client_id'))) {
            $clientid = CHttpRequest::get_Value('client_id');

            // Verify if client_id is a valid integer
            if (!filter_var($clientid, FILTER_VALIDATE_INT)) {
                throw new Exception('Critical: provided parameter (client_id) is not valid');
            }

            $period = CHttpRequest::get_Value('period');

            $this->assign('selected_period', CHttpRequest::get_Value('period'));
            $this->assign('selected_client', $clientid);

            // Check if period is an integer and listed in known periods
            if (!array_key_exists($period, $periods_list)) {
                throw new Exception('Critical: provided value for (period) is unknown or not valid');
            }

            if (!filter_var($period, FILTER_VALIDATE_INT)) {
                throw new Exception('Critical: provided value for (period) is unknown or not valid');
            }

            /**
             * Filter jobs per requested period 
             */
            
            // Get the last n days interval (start and end timestamps)
            $days = DateTimeUtil::getLastDaysIntervals($period);

            $startTime = date('Y-m-d H:i:s', $days[0]['start']);
            $endTime = date('Y-m-d H:i:s', $days[array_key_last($days)]['end']);

            $jobs->addParameter('job_starttime', $startTime);
            $where[] = 'Job.endtime >= :job_starttime';
            $jobs->addParameter('job_endtime', $endTime);
            $where[] = 'Job.endtime <= :job_endtime';

            $this->assign('no_report_options', 'false');

            // Client informations
            $client_info  = $client->getClientInfos($clientid);
            
            $this->assign('client_name', $client_info['name']);
            $this->assign('client_os', $client_info['os']);
            $this->assign('client_arch', $client_info['arch']);
            $this->assign('client_version', $client_info['version']);
                   
            // // Filter by Job status = Completed
            $jobs->addParameter('jobstatus', 'T');
            $where[] = 'Job.JobStatus = :jobstatus';

            // // Filter by Job Type
            $jobs->addParameter('jobtype', 'B');
            $where[] = 'Job.Type = :jobtype';
            
            // Filter by Client id
            $jobs->addParameter('clientid', $clientid);
            $where[] = 'clientid = :clientid';

            $query = CDBQuery::get_Select( ['table' => 'Job',
                'fields' => ['Job.Name', 'Job.Jobid', 'Job.Level', 'Job.Endtime', 'Job.Jobbytes', 'Job.Jobfiles', 'Status.JobStatusLong'],
                'join' => [
                    ['table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus']
                ], 
                'orderby' => 'Job.EndTime DESC',
                'where' => $where 
                ], $jobs->get_driver_name() );

            $jobs_result = $jobs->run_query($query);

            foreach ($jobs_result->fetchAll() as $job) {
                $job['level']     = $job_levels[$job['level']];
                $job['jobfiles']  = CUtils::format_Number($job['jobfiles']);
                $job['jobbytes']  = CUtils::Get_Human_Size($job['jobbytes']);
                $job['endtime']   = date($_SESSION['datetime_format'], strtotime($job['endtime']));
            
                $backup_jobs[] = $job;
            } // end foreach
       
            $this->assign('backup_jobs', $backup_jobs);
       
            $jobsStats = new Jobs_Model();
            // Last n days stored Bytes graph
            foreach ($days as $day) {
                $stored_bytes = $jobsStats->getStoredBytes(array($day['start'], $day['end']), 'ALL', $clientid);
                $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
            } // end foreach
       
            $stored_bytes_chart = new Chart(array( 'type' => 'bar',
                'name' => 'chart_storedbytes',
                'data' => $days_stored_bytes,
                'ylabel' => 'Bytes',
                'uniformize_data' => true ));
       
            $this->assign('stored_bytes_chart_id', $stored_bytes_chart->name);
            $this->assign('stored_bytes_chart', $stored_bytes_chart->render());
       
            unset($stored_bytes_chart);
       
            $jobsStats = new Jobs_Model();

            // Last n days stored files graph
            foreach ($days as $day) {
                $stored_files = $jobsStats->getStoredFiles(array($day['start'], $day['end']), 'ALL', $clientid);
                $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
            }
       
            $stored_files_chart = new Chart(array( 'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $days_stored_files,
                'ylabel' => 'Files' ));
       
            $this->assign('stored_files_chart_id', $stored_files_chart->name);
            $this->assign('stored_files_chart', $stored_files_chart->render());
       
            unset($stored_files_chart);
        } else {
            $this->assign('selected_period', '');
            $this->assign('selected_client', '');
            $this->assign('no_report_options', 'true');
        }
        
        $this->assign('period', $period);
    } // end of prepare() method
} // end of class
