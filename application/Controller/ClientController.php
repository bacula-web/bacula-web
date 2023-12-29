<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
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

namespace App\Controller;

use App\Libs\FileConfig;
use Core\Exception\AppException;
use Core\Exception\ConfigFileException;
use Core\Graph\Chart;
use Core\Db\CDBQuery;
use Core\Utils\DateTimeUtil;
use Core\Utils\CUtils;
use Core\Helpers\Sanitizer;
use App\Tables\JobTable;
use App\Tables\ClientTable;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use TypeError;

class ClientController
{
    private JobTable $jobTable;
    private ClientTable $clientTable;
    private Twig $view;

    /**
     * @param Twig $view
     * @param JobTable $jobTable
     * @param ClientTable $clientTable
     */
    public function __construct(Twig $view, JobTable $jobTable, ClientTable $clientTable)
    {
        $this->view = $view;
        $this->jobTable = $jobTable;
        $this->clientTable = $clientTable;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws AppException
     * @throws ConfigFileException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $tplData = [];

        $period = 7;
        $backup_jobs = array();
        $days_stored_bytes = array();
        $days_stored_files = array();

        // Clients list
        $tplData['clients_list'] = $this->clientTable->getClients();

        // Period list
        $periods_list = [
            '7' => "Last week",
            '14' => "Last 2 weeks",
            '30' => "Last month"
        ];

        $tplData['periods_list'] = $periods_list;

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

        // Check client_id and period received by POST $this->request
        $postData = $request->getParsedBody();

        $clientId = null;
        if (isset($postData['client_id'])) {
            $clientId = Sanitizer::sanitize($postData['client_id']);
        }

        if (isset($postData['period'])) {
            $period = (int) Sanitizer::sanitize($postData['period']);

            // Check if period is an integer and listed in known periods
            if (!array_key_exists($period, $periods_list)) {
                throw new TypeError('Critical: provided value for (period) is unknown or not valid');
            }

            $tplData['selected_period'] = $period;
            $tplData['selected_client'] = $clientId;
            /**
             * Filter jobTable per $this->requested period
             */

            // Get the last n days interval (start and end timestamps)
            $days = DateTimeUtil::getLastDaysIntervals($period);

            $startTime = date('Y-m-d H:i:s', $days[0]['start']);
            $endTime = date('Y-m-d H:i:s', $days[array_key_last($days)]['end']);

            $this->jobTable->addParameter('job_starttime', $startTime);
            $where[] = 'Job.endtime >= :job_starttime';
            $this->jobTable->addParameter('job_endtime', $endTime);
            $where[] = 'Job.endtime <= :job_endtime';

            $tplData['no_report_options'] = 'false';

            // Client informations
            $client_info  = $this->clientTable->getClientInfos($clientId);

            $tplData['client_name'] = $client_info['name'];
            $tplData['client_os'] = $client_info['os'];
            $tplData['client_arch'] = $client_info['arch'];
            $tplData['client_version'] = $client_info['version'];

            // Filter by Job status = Completed
            $this->jobTable->addParameter('jobstatus', 'T');
            $where[] = 'Job.JobStatus = :jobstatus';

            // // Filter by Job Type
            $this->jobTable->addParameter('jobtype', 'B');
            $where[] = 'Job.Type = :jobtype';

            // Filter by Client id
            $this->jobTable->addParameter('clientid', $clientId);
            $where[] = 'clientid = :clientid';

            $query = CDBQuery::get_Select(['table' => $this->jobTable->getTableName(),
                'fields' => ['Job.Name', 'Job.Jobid', 'Job.Level', 'Job.Endtime', 'Job.Jobbytes', 'Job.Jobfiles', 'Status.JobStatusLong'],
                'join' => [
                    ['table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus']
                ],
                'orderby' => 'Job.EndTime DESC',
                'where' => $where
                ], $this->jobTable->get_driver_name());

            $jobs_result = $this->jobTable->run_query($query);

            $totalBytes = 0;
            $totalFiles = 0;
            foreach ($jobs_result->fetchAll() as $job) {
                $totalBytes += (int) $job['jobbytes'];
                $totalFiles += (int) $job['jobfiles'];
                $job['level']     = $job_levels[$job['level']];
                $job['jobfiles']  = CUtils::format_Number($job['jobfiles']);
                $job['jobbytes']  = CUtils::Get_Human_Size($job['jobbytes']);
                $job['endtime']   = date(FileConfig::get_Value('datetime_format'), strtotime($job['endtime']));
                $backup_jobs[] = $job;
            }
            $tplData['total_bytes'] = CUtils::Get_Human_Size($totalBytes);
            $tplData['total_files'] = CUtils::format_Number($totalFiles);
            $tplData['backup_jobs'] = $backup_jobs;

            // Last n days stored Bytes graph
            foreach ($days as $day) {
                $stored_bytes = $this->jobTable->getStoredBytes(array($day['start'], $day['end']), 'ALL', $clientId);
                $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
            }

            $stored_bytes_chart = new Chart(array( 'type' => 'bar',
                'name' => 'chart_storedbytes',
                'data' => $days_stored_bytes,
                'ylabel' => 'Bytes',
                'uniformize_data' => true ));

            $tplData['stored_bytes_chart_id'] = $stored_bytes_chart->name;
            $tplData['stored_bytes_chart'] = $stored_bytes_chart->render();

            unset($stored_bytes_chart);

            // Last n days stored files graph
            foreach ($days as $day) {
                $stored_files = $this->jobTable->getStoredFiles(array($day['start'], $day['end']), 'ALL', $clientId);
                $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
            }

            $stored_files_chart = new Chart(array( 'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $days_stored_files,
                'ylabel' => 'Files' ));

            $tplData['stored_files_chart_id'] = $stored_files_chart->name;
            $tplData['stored_files_chart'] = $stored_files_chart->render();

            unset($stored_files_chart);
        } else {
            $tplData['selected_period'] = '';
            $tplData['selected_client'] = '';
            $tplData['no_report_options'] = 'true';
        }

        $tplData['period'] = $period;

        return $this->view->render($response, 'pages/client-report.html.twig', $tplData);
    }
}
