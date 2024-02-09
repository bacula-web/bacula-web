<?php

/**
 * Copyright (C) 2010-present Davide Franco
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

namespace App\Controller;

use App\Libs\Config;
use App\Table\JobFileTable;
use App\Table\LogTable;
use Core\Db\DBPagination;
use Core\Db\CDBQuery;
use Core\Exception\ConfigFileException;
use Core\Helpers\Sanitizer;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use App\Table\JobTable;
use App\Table\ClientTable;
use App\Table\PoolTable;
use GuzzleHttp\Psr7\Response;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use TypeError;
use Valitron\Validator;
use function Core\Helpers\getRequestParams;

class JobController
{
    private LogTable $logTable;
    private JobTable $jobTable;
    private ClientTable $clientTable;
    private PoolTable $poolTable;
    private JobFileTable $jobFileTable;
    private Twig $view;
    private SessionInterface $session;
    private ?string $basePath;
    private Config $config;

    public function __construct(
        JobTable $jobTable,
        LogTable $logTable,
        ClientTable $clientTable,
        PoolTable $poolTable,
        JobFileTable $jobFileTable,
        Twig $view,
        SessionInterface $session,
        Config $config
    )
    {
        $this->logTable = $logTable;
        $this->jobTable = $jobTable;
        $this->clientTable = $clientTable;
        $this->poolTable = $poolTable;
        $this->jobFileTable = $jobFileTable;
        $this->view = $view;
        $this->session = $session;
        $this->config = $config;

        $this->basePath = $this->config->get('basepath', null);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ConfigFileException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $tplData = [];
        $where = null;
        $params = [];
        $postRequestData = getRequestParams($request);

        $fields = [
            'Job.JobId', 'Job.Name AS Job_name', 'Job.Type', 'Job.SchedTime', 'Job.StartTime', 'Job.EndTime', 'Job.Level',
            'Job.ReadBytes', 'Job.JobBytes', 'Job.JobFiles', 'Pool.Name', 'Job.JobStatus', 'Pool.Name AS Pool_name', 'Status.JobStatusLong'
        ];

        // Order result by
        $result_order = [
            'SchedTime' => 'Job Scheduled Time',
            'starttime' => 'Job Start Date',
            'endtime'   => 'Job End Date',
            'jobid'     => 'Job Id',
            'Job.Name'  => 'Job Name',
            'jobbytes'  => 'Job Bytes',
            'jobfiles'  => 'Job Files',
            'Pool.Name' => 'Pool Name'
        ];

        // Global variables
        $job_levels = [
            'D' => 'Differential',
            'I' => 'Incremental',
            'F' => 'Full',
            'V' => 'InitCatalog',
            'C' => 'Catalog',
            'O' => 'VolumeToCatalog',
            'd' => 'DiskToCatalog',
            'A' => 'Data'
        ];

        $last_jobs = [];

        // Job Status list
        define('STATUS_ALL', 0);
        define('STATUS_RUNNING', 1);
        define('STATUS_WAITING', 2);
        define('STATUS_COMPLETED', 3);
        define('STATUS_COMPLETED_WITH_ERRORS', 4);
        define('STATUS_FAILED', 5);
        define('STATUS_CANCELED', 6);

        $job_status = [
            STATUS_ALL => 'All',
            STATUS_RUNNING => 'Running',
            STATUS_WAITING => 'Waiting',
            STATUS_COMPLETED => 'Completed',
            STATUS_COMPLETED_WITH_ERRORS => 'Completed with errors',
            STATUS_FAILED => 'Failed',
            STATUS_CANCELED => 'Canceled'
        ];

        $tplData['job_status'] = $job_status;

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
        $job_types_list = $this->jobTable->getUsedJobTypes($job_types);
        $job_types_list['0'] = 'Any';
        $tplData['job_types_list'] = $job_types_list;

        // Job client id filter
        $filter_clientid = '0';
        if (isset($postRequestData['filter_clientid'])) {
            $filter_clientid = $postRequestData['filter_clientid'];
        }
        $tplData['filter_clientid'] = $filter_clientid;

        // Job status filter
        $filter_jobstatus = '0';

        if (isset($postRequestData['filter_jobstatus'])) {
            $filter_jobstatus = (int) $postRequestData['filter_jobstatus'] ?? '0';
        }
        $tplData['filter_jobstatus'] = $filter_jobstatus;

        // Job type filter
        $filter_jobtype = '0';
        if (isset($postRequestData['filter_jobtype'])) {
            $filter_jobtype = $postRequestData['filter_jobtype'];
        }
        $tplData['filter_jobtype'] = $filter_jobtype;

        // Validate filter job type
        if (array_key_exists($filter_jobtype, $job_types)) {
            // TODO: Validate request parameter
        }

        // Levels list filter
        $levels_list = $this->jobTable->getLevels($job_levels);
        $levels_list['0']  = 'Any';
        $tplData['levels_list'] = $levels_list;

        // Job level filter
        $filter_joblevel = '0';
        if (isset($postRequestData['filter_joblevel'])) {
            $filter_joblevel = $postRequestData['filter_joblevel'];
        }
        $tplData['filter_joblevel'] = $filter_joblevel;

        // Job starttime filter
        $filter_job_starttime = null;
        if (isset($postRequestData['filter_job_starttime'])) {
            $filter_job_starttime = $postRequestData['filter_job_starttime'];
        }
        $tplData['filter_job_starttime'] = $filter_job_starttime;

        // Job endtime filter
        $filter_job_endtime = null;
        if (isset($postRequestData['filter_job_endtime'])) {
            $filter_job_endtime = $postRequestData['filter_job_endtime'];
        }
        $tplData['filter_job_endtime'] = $filter_job_endtime;

        // Job orderby filter
        $job_orderby_filter = 'jobid';
        if(isset($postRequestData['filter_job_orderby'])) {
            $job_orderby_filter = $postRequestData['filter_job_orderby'];
        }

        // Validate job order by
        if (array_key_exists($job_orderby_filter, $result_order)) {
            // TODO: Validate job order
        }

        // Job orderby asc filter
        $job_orderby_asc_filter = 'DESC';
        if( isset($postRequestData['filter_job_orderby_asc'])) {
            $job_orderby_asc_filter = 'ASC';
        }

        // Clients list filter
        $clients_list = $this->clientTable->getClients($this->config->get('show_inactive_clients'));
        $clients_list[0] = 'Any';
        $tplData['clients_list'] = $clients_list;

        /**
         * Generate drop-down job pool filter
         */
        $pools_list = [];

        foreach ($this->poolTable->getPools($this->config->get('hide_empty_pools')) as $pool) {
            $pools_list[$pool['poolid']] = $pool['name'];
        }

        $pools_list[0] = 'Any';
        $tplData['pools_list'] = $pools_list;

        $filter_poolid = '0';
        if (isset($postRequestData['filter_poolid'])) {
            $filter_poolid = $postRequestData['filter_poolid'];
        }
        $tplData['filter_poolid'] = $filter_poolid;

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
            $where[] = "Job.Level = :job_level ";
            $params['job_level'] = $filter_joblevel;
        }

        // Selected pool filter
        if ($filter_poolid !== '0') {
            $where[] = "Job.PoolId = :pool_id ";
            $params['pool_id'] = $filter_poolid;
        }

        if ($filter_jobtype !== '0') {
            $where[] = "Job.Type = :job_type";
            $params['job_type'] = $filter_jobtype;
        }

        // Selected client filter
        if ($filter_clientid !== '0') {
            $where[] = "Job.ClientId = :client_id";
            $params['client_id'] = $filter_clientid;
        }

        // Selected job start time filter
        if (!is_null($filter_job_starttime) && !empty($filter_job_starttime)) {
            if (DateTimeUtil::checkDate($filter_job_starttime)) {
                $where[] = 'Job.StartTime >= :job_start_time';
                $params['job_start_time'] = $filter_job_starttime;
            }
        }

        // Selected job end time filter
        if (!is_null($filter_job_endtime) && !empty($filter_job_endtime)) {
            if (DateTimeUtil::checkDate($filter_job_endtime)) {
                $where[] = 'Job.EndTime <= :job_end_time';
                $params['job_end_time'] = $filter_job_endtime;
            }
        }

        $tplData['result_order'] = $result_order;

        $orderby = "$job_orderby_filter $job_orderby_asc_filter ";

        // Set selected option in template for Job order and Job order asc (ascendant order)
        $tplData['result_order_field'] = $job_orderby_filter;

        if ($job_orderby_asc_filter == 'ASC') {
            $tplData['result_order_asc_checked'] = 'checked';
        } else {
            $tplData['result_order_asc_checked'] = '';
        }

        $pagination = new DBPagination($request, $this->config);

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
            ) ),$this->jobTable->get_driver_name());

        $countQuery = CDBQuery::get_Select(
            [
            'table' => 'Job',
            'fields' => ['COUNT(*) AS row_count'],
            'where' => $where
            ]
        );

        foreach ($pagination->paginate($this->jobTable, $sqlQuery, $countQuery, $params) as $job) {
            // Determine icon for job status
            switch ($job['jobstatus']) {
                case J_RUNNING:
                    $job['Job_icon'] = 'fa-solid fa-play';
                    break;
                case J_COMPLETED:
                    $job['Job_icon'] = 'fa-solid fa-check';
                    break;
                case J_CANCELED:
                    $job['Job_icon'] = 'fa-solid fa-power-off';
                    break;
                case J_VERIFY_FOUND_DIFFERENCES:
                case J_COMPLETED_ERROR:
                    $job['Job_icon'] = 'fa-solid fa-triangle-exclamation';
                    break;
                case J_FATAL:
                    $job['Job_icon'] = 'fa-solid fa-xmark';
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
                    $job['Job_icon'] = 'fa-solid fa-clock';
                    break;
            } // end switch

            $start_time = $job['starttime'];
            $end_time   = $job['endtime'];

            if ($start_time == '0000-00-00 00:00:00' || is_null($start_time) || $start_time == 0) {
                $job['starttime'] = 'n/a';
            } else {
                $job['starttime'] = date(
                    $this->config->get('datetime_format', 'Y-m-d H:i:s'), strtotime($job['starttime'])
                );
            }

            if ($end_time == '0000-00-00 00:00:00' || is_null($end_time) || $end_time == 0) {
                $job['endtime'] = 'n/a';
            } else {
                $job['endtime'] = date(
                    $this->config->get('datetime_format', 'Y-m-d H:i:s'), strtotime($job['endtime'])
                );
            }

            // Get the job elapsed time completion
            if (DateTimeUtil::checkDate($start_time) && DateTimeUtil::checkDate($end_time)) {
                $job['elapsed_time'] = DateTimeUtil::Get_Elapsed_Time($start_time, $end_time);
            } else {
                $job['elapsed_time'] = 'n/a';
            }

            $job['schedtime'] = date(
                $this->config->get('datetime_format', 'Y-m-d H:i:s'), strtotime($job['schedtime'])
            );

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
            }

            // Job size
            $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);

            // Job Pool
            if (is_null($job['pool_name'])) {
                $job['pool_name'] = 'n/a';
            }

            $last_jobs[] = $job;
        }

        $tplData['pagination'] = $pagination;
        $tplData['last_jobs'] = $last_jobs;
        $tplData['jobs_found'] = count($last_jobs);

        return $this->view->render($response, 'pages/jobs.html.twig', $tplData);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showLogs(Request $request, Response $response, $args): Response
    {
        $tplData = [];

        $v = new Validator($args);
        $v->rules(['integer' => 'jobid']);

        if (!$v->validate()) {
            $this->session->getFlash()->set('error', ['Invalid job id provided in Job logs report']);
            return $response
                ->withHeader('Location', $this->basePath . '/jobs')
                ->withStatus(302);
        }

        $jobId = (int) $args['jobid'];

        $tplData['job'] = $this->jobTable->findById($jobId);

        $sql = CDBQuery::get_Select(
            [
                'table' => 'Log',
                'where' => [ 'JobId = :jobid'],
                'orderby' => 'Time'
            ]
        );

        $jobLogs = $this->logTable->findAll($sql, ['jobid' => $jobId], 'App\Entity\Log');

        $tplData['joblogs'] = array_filter($jobLogs, function($element) {
            $element->setTime(
                date($this->config->get('datetime_format', 'Y-m-d H:i:s'),strtotime($element->getTime()))
            );
            return $element;
        });

        return $this->view->render($response, 'pages/joblogs.html.twig', $tplData);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showFiles(Request $request, Response $response, $args): Response
    {
        $tplData = [];
        $rows_per_page = 10;

        $filename = '';

        $postData = $request->getParsedBody();

        $jobId = (int) $args['jobid'];

        if ($jobId !== 0) {
            $tplData['jobid'] = $jobId;
        } else {
            throw new TypeError('Invalid or missing Job Id');
        }

        if (isset($postData['filename'])) {
            $filename = Sanitizer::sanitize($postData['filename']);
        }

        if (isset($args['filename'])) {
            $filename = $args['filename'];
        }

        $jobInfo = $this->jobFileTable->getJobNameAndJobStatusByJobId($jobId);
        $tplData['job_info'] = $jobInfo;
        $files_count = $this->jobFileTable->countJobFiles($jobId, $filename);
        $tplData['job_files_count'] = CUtils::format_Number($files_count);

        //pagination
        $pagination_active = false;
        if ($files_count > $rows_per_page) {
            $pagination_active = true;
        }

        $currentPage = $args['page'] ?? 0;

        $tplData['pagination_active'] = $pagination_active;
        $tplData['pagination_current_page'] = $currentPage;
        $tplData['pagination_rows_per_page'] = $rows_per_page;

        if (!empty($filename)) {
            // Filter with provided filename if provided
            $files = $this->jobFileTable->getJobFiles($jobId, $rows_per_page, $currentPage, $filename);
        } else {
            // otherwise, get files based on JobId only
            $files = $this->jobFileTable->getJobFiles($jobId, $rows_per_page, $currentPage);
        }

        $tplData['job_files'] = $files;
        $tplData['job_files_count_paging'] = count($files);

        $tplData['filename'] = $filename;

        return $this->view->render($response, 'pages/jobfiles.html.twig', $tplData);
    }
}
