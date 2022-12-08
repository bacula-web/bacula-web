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

use Core\App\Controller;
use Core\Graph\Chart;
use Core\Db\DatabaseFactory;
use Core\Db\CDBQuery;
use Core\Utils\DateTimeUtil;
use Core\Utils\CUtils;
use Core\Helpers\Sanitizer;
use App\Tables\JobTable;
use App\Tables\ClientTable;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use TypeError;

class ClientController extends Controller
{
    /**
     * @return Response
     * @throws Exception
     */
    public function prepare(): Response
    {
        require_once BW_ROOT . '/core/const.inc.php';

        $session = new Session();

        $period = 7;
        $backup_jobs = array();
        $days_stored_bytes = array();
        $days_stored_files = array();

        // Get job names for the client
        $catalogid = $session->get('catalogid', 0);
        $jobs = new JobTable(DatabaseFactory::getDatabase($catalogid));
        $client = new ClientTable(DatabaseFactory::getDatabase($catalogid));

        // Clients list
        $this->setVar('clients_list', $client->getClients());

        // Period list
        $periods_list = array( '7' => "Last week", '14' => "Last 2 weeks", '30' => "Last month");
        $this->setVar('periods_list', $periods_list);

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
        if ($this->request->request->has('client_id')) {
            $clientid = $this->request->request->getInt('client_id');
            $clientid = Sanitizer::sanitize($clientid);

            // Verify if client_id is a valid integer
            if ($clientid === 0) {
                throw new TypeError('Critical: provided parameter (client_id) is not valid');
            }

            $period = $this->request->request->getInt('period');
            $period = Sanitizer::sanitize($period);

            // Check if period is an integer and listed in known periods
            if (!array_key_exists($period, $periods_list)) {
                throw new TypeError('Critical: provided value for (period) is unknown or not valid');
            }

            $this->setVar('selected_period', $period);
            $this->setVar('selected_client', $clientid);

            /**
             * Filter jobs per $this->requested period
             */

            // Get the last n days interval (start and end timestamps)
            $days = DateTimeUtil::getLastDaysIntervals($period);

            $startTime = date('Y-m-d H:i:s', $days[0]['start']);
            $endTime = date('Y-m-d H:i:s', $days[array_key_last($days)]['end']);

            $jobs->addParameter('job_starttime', $startTime);
            $where[] = 'Job.endtime >= :job_starttime';
            $jobs->addParameter('job_endtime', $endTime);
            $where[] = 'Job.endtime <= :job_endtime';

            $this->setVar('no_report_options', 'false');

            // Client informations
            $client_info  = $client->getClientInfos($clientid);

            $this->setVar('client_name', $client_info['name']);
            $this->setVar('client_os', $client_info['os']);
            $this->setVar('client_arch', $client_info['arch']);
            $this->setVar('client_version', $client_info['version']);

            // // Filter by Job status = Completed
            $jobs->addParameter('jobstatus', 'T');
            $where[] = 'Job.JobStatus = :jobstatus';

            // // Filter by Job Type
            $jobs->addParameter('jobtype', 'B');
            $where[] = 'Job.Type = :jobtype';

            // Filter by Client id
            $jobs->addParameter('clientid', $clientid);
            $where[] = 'clientid = :clientid';

            $query = CDBQuery::get_Select(['table' => $jobs->getTableName(),
                'fields' => ['Job.Name', 'Job.Jobid', 'Job.Level', 'Job.Endtime', 'Job.Jobbytes', 'Job.Jobfiles', 'Status.JobStatusLong'],
                'join' => [
                    ['table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus']
                ],
                'orderby' => 'Job.EndTime DESC',
                'where' => $where
                ], $jobs->get_driver_name());

            $jobs_result = $jobs->run_query($query);

            foreach ($jobs_result->fetchAll() as $job) {
                $job['level']     = $job_levels[$job['level']];
                $job['jobfiles']  = CUtils::format_Number($job['jobfiles']);
                $job['jobbytes']  = CUtils::Get_Human_Size($job['jobbytes']);
                $job['endtime']   = date($session->get('datetime_format'), strtotime($job['endtime']));

                $backup_jobs[] = $job;
            } // end foreach

            $this->setVar('backup_jobs', $backup_jobs);

            $jobsStats = new JobTable(DatabaseFactory::getDatabase($catalogid));
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

            $this->setVar('stored_bytes_chart_id', $stored_bytes_chart->name);
            $this->setVar('stored_bytes_chart', $stored_bytes_chart->render());

            unset($stored_bytes_chart);

            $jobsStats = new JobTable(DatabaseFactory::getDatabase($catalogid));

            // Last n days stored files graph
            foreach ($days as $day) {
                $stored_files = $jobsStats->getStoredFiles(array($day['start'], $day['end']), 'ALL', $clientid);
                $days_stored_files[] = array(date("m-d", $day['start']), $stored_files);
            }

            $stored_files_chart = new Chart(array( 'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $days_stored_files,
                'ylabel' => 'Files' ));

            $this->setVar('stored_files_chart_id', $stored_files_chart->name);
            $this->setVar('stored_files_chart', $stored_files_chart->render());

            unset($stored_files_chart);
        } else {
            $this->setVar('selected_period', '');
            $this->setVar('selected_client', '');
            $this->setVar('no_report_options', 'true');
        }

        $this->setVar('period', $period);

        return (new Response($this->render('client-report.tpl')));
    }
}
