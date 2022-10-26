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

declare(strict_types=1);

namespace App\Views;

use Core\App\CView;
use Core\Db\DatabaseFactory;
use Core\Db\CDBPagination;
use Core\Db\CDBQuery;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use Core\Helpers\Sanitizer;
use App\Tables\JobTable;
use App\Tables\ClientTable;
use App\Tables\PoolTable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class JobsView extends CView
{
    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->templateName = 'jobs.tpl';
        $this->name = 'Jobs report';
        $this->title = 'Bacula jobs overview';
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function prepare(): void
    {
        $jobs = new JobTable(
            DatabaseFactory::getDatabase(
                (new Session())->get('catalog_id', 0)
            )
        );

        $where = null;
        $session = new Session();
        $params = [];

        // TODO: Improve how these constants are declared and used
        require_once BW_ROOT . '/core/const.inc.php';

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

        $last_jobs = [];

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

        // Job client id filter
        $filter_clientid = (int) $this->getParameter('filter_clientid', 0);
        $this->assign('filter_clientid', $filter_clientid);

        // Job status filter
        $filter_jobstatus = (int) $this->getParameter('filter_jobstatus', 0);
        $this->assign('filter_jobstatus', $filter_jobstatus);

        // Job type filter
        $filter_jobtype = $this->getParameter('filter_jobtype', 0);
        $this->assign('filter_jobtype', $filter_jobtype);

        // Validate filter job type
        if (array_key_exists($filter_jobtype, $job_types)) {
            // TODO: Validate request parameter
        }

        // Levels list filter
        $levels_list = $jobs->getLevels($job_levels);
        $levels_list['0']  = 'Any';
        $this->assign('levels_list', $levels_list);

        // Job level filter
        $filter_joblevel = $this->getParameter('filter_joblevel', 0);
        $this->assign('filter_joblevel', $filter_joblevel);

        // Job pool filter
        $filter_poolid = (int) $this->getParameter('filter_poolid', 0);
        $this->assign('filter_poolid', $filter_poolid);

        // Job starttime filter
        $filter_job_starttime = $this->getParameter('filter_job_starttime', null);
        $this->assign('filter_job_starttime', $filter_job_starttime);

        // Job endtime filter
        $filter_job_endtime = $this->getParameter('filter_job_endtime', null);
        $this->assign('filter_job_endtime', $filter_job_endtime);

        // Job orderby filter
        $job_orderby_filter = $this->getParameter('filter_job_orderby', 'jobid');

        // Validate job order by
        if (array_key_exists($job_orderby_filter, $result_order)) {
            // TODO: Validate job order
        }

        // Job orderby asc filter
        $job_orderby_asc_filter = $this->getParameter('filter_job_orderby_asc', 'DESC');

        // Clients list filter
        $clients = new ClientTable(
            DatabaseFactory::getDatabase(
                (new Session())->get('catalog_id', 0)
            )
        );

        $clients_list = $clients->getClients();
        $clients_list[0] = 'Any';
        $this->assign('clients_list', $clients_list);

        // Pools list filer
        $pools = new PoolTable(
            DatabaseFactory::getDatabase(
                (new Session())->get('catalog_id', 0)
            )
        );
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
        if ($filter_joblevel !== '0') {
            $where[] .= "Job.Level = :job_level ";
            $params['job_level'] = $filter_joblevel;
        }

        // Selected pool filter
        if ($filter_poolid !== 0) {
            $where[] .= "Job.PoolId = :pool_id ";
            $params['pool_id'] = $filter_poolid;
        }

        if ($filter_jobtype !== '0') {
            $where[] = "Job.Type = :job_type";
            $params['job_type'] = $filter_jobtype;
        }

        // Selected client filter
        if ($filter_clientid !== 0) {
            $where[] .= "Job.ClientId = :client_id";
            $params['client_id'] = $filter_clientid;
        }

        // Selected job start time filter
        if (!is_null($filter_job_starttime) && !empty($filter_job_starttime)) {
            if (DateTimeUtil::checkDate($filter_job_starttime)) {
                $where[] = "Job.StartTime >= '$filter_job_starttime'";
                $params['Job.StartTime'] = $filter_job_starttime;
            }
        }

        // Selected job end time filter
        if (!is_null($filter_job_endtime) && !empty($filter_job_endtime)) {
            if (DateTimeUtil::checkDate($filter_job_endtime)) {
                $where[] = "Job.EndTime <= '$filter_job_endtime'";
                $params['Job.EndTime'] = $filter_job_endtime;
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

        // Paginate database query result
        $pagination = new CDBPagination($this);

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
            ) ), $jobs->get_driver_name());

        $countQuery = CDBQuery::get_Select(
            [
            'table' => 'Job',
            'fields' => ['COUNT(*) AS row_count'],
            'where' => $where
            ]
        );

        foreach ($pagination->paginate($jobs, $sqlQuery, $countQuery, $params) as $job) {
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
                $job['starttime'] = date($session->get('datetime_format'), strtotime($job['starttime']));
            }

            if ($end_time == '0000-00-00 00:00:00' || is_null($end_time) || $end_time == 0) {
                $job['endtime'] = 'n/a';
            } else {
                $job['endtime'] = date($session->get('datetime_format'), strtotime($job['endtime']));
            }

            // Get the job elapsed time completion
            $job['elapsed_time'] = DateTimeUtil::Get_Elapsed_Time($start_time, $end_time);

            $job['schedtime'] = date($session->get('datetime_format'), strtotime($job['schedtime']));

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
                        $compression        = (1 - ($job['jobbytes'] / $job['readbytes']));
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
}
