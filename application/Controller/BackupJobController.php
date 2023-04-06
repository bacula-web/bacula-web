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

use App\Tables\JobTable;
use Core\App\Controller;
use Core\Db\CDBQuery;
use Core\Db\DatabaseFactory;
use Core\Exception\AppException;
use Core\Graph\Chart;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use Core\Helpers\Sanitizer;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BackupJobController extends Controller
{
    /**
     * @return Response
     * @throws Exception|AppException
     */
    public function prepare(): Response
    {
        require_once BW_ROOT . '/core/const.inc.php';

        $interval = array();
        $interval[1] = NOW;

        $daysstoredbytes = array();
        $daysstoredfiles = array();

        // Period list
        $periods_list = array( '7' => "Last week", '14' => "Last 2 weeks", '30' => "Last month");
        $this->setVar('periods_list', $periods_list);

        // Stored Bytes on the defined period
        $jobs = new JobTable(DatabaseFactory::getDatabase($this->session->get('catalog_id', 0)));

        // Get backup job(s) list
        $jobslist = $jobs->get_Jobs_List(null, 'B');
        $this->setVar('jobs_list', $jobslist);

        // Check backup job name from $_POST request
        $backupjob_name = null;

        if ($this->request->getMethod() === 'POST') {
            $backupjob_name = $this->request->request->get('backupjob_name');
        } elseif ($this->request->getMethod() === 'GET') {
            $backupjob_name = $this->request->query->get('backupjob_name');
        }
        $backupjob_name = Sanitizer::sanitize($backupjob_name);

        $where = array();

        if ($backupjob_name == null) {
            $this->setVar('selected_jobname', '');
            $this->setVar('no_report_options', 'true');

            // Set selected period
            $this->setVar('selected_period', 7);
        } else {
            $this->setVar('no_report_options', 'false');

            // Make sure provided backupjob_name exist
            if (!in_array($backupjob_name, $jobslist)) {
                // TODO: Below should be included in flash and redirect user to another page (maybe referer)
                throw new AppException('Wrong user input: invalid backupjob_name');
            }

            $this->setVar('selected_jobname', $backupjob_name);

            /**
             * Get selected period from POST request, or set it to default value (7)
             */
            $backupjob_period = $this->request->request->getInt('period', 7);

            // Set selected period
            $this->setVar('selected_period', $backupjob_period);

            $perioddesc = 'From ';

            switch ($backupjob_period) {
                case '7':
                    $perioddesc .= date($this->session->get('datetime_format_short'), (NOW - WEEK)) . " to " . date($this->session->get('datetime_format_short'), NOW);
                    $interval[0] = NOW - WEEK;
                    break;
                case '14':
                    $perioddesc .= date($this->session->get('datetime_format_short'), (NOW - (2 * WEEK))) . " to " . date($this->session->get('datetime_format_short'), NOW);
                    $interval[0] = NOW - (2 * WEEK);
                    break;
                case '30':
                    $perioddesc .= date($this->session->get('datetime_format_short'), (NOW - MONTH)) . " to " . date($this->session->get('datetime_format_short'), NOW);
                    $interval[0] = NOW - MONTH;
                    break;
                default:
                    throw new AppException('Provided backup job period not supported');
            }

            // Get start and end datetime for backup jobs report and charts
            $periods = CDBQuery::get_Timestamp_Interval($jobs->get_driver_name(), $interval);

            $backupjobbytes = $jobs->getStoredBytes($interval, $backupjob_name);
            $backupjobbytes = CUtils::Get_Human_Size($backupjobbytes);

            // Stored files on the defined period
            $backupjobfiles = $jobs->getStoredFiles($interval, $backupjob_name);
            $backupjobfiles = CUtils::format_Number($backupjobfiles);

            // Get the last 7 days interval (start and end)
            $days = DateTimeUtil::getLastDaysIntervals($backupjob_period);

            // Last 7 days stored files chart
            foreach ($days as $day) {
                $storedfiles = $jobs->getStoredFiles(array($day['start'], $day['end']), $backupjob_name);
                $daysstoredfiles[] = array(date("m-d", $day['start']), $storedfiles);
            }

            $storedfileschart = new Chart( [
                'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $daysstoredfiles,
                'ylabel' => 'Files'
                ]
            );

            $this->setVar('stored_files_chart_id', $storedfileschart->name);
            $this->setVar('stored_files_chart', $storedfileschart->render());

            unset($storedfileschart);

            // Last 7 days stored bytes chart
            foreach ($days as $day) {
                $storedbytes = $jobs->getStoredBytes(array($day['start'], $day['end']), $backupjob_name);
                $daysstoredbytes[] = array(date("m-d", $day['start']), $storedbytes);
            }

            $storedbyteschart = new Chart(
                [
                    'type' => 'bar',
                    'name' => 'chart_storedbytes',
                    'uniformize_data' => true,
                    'data' => $daysstoredbytes,
                    'ylabel' => 'Bytes'
                ]
            );

            $this->setVar('stored_bytes_chart_id', $storedbyteschart->name);
            $this->setVar('stored_bytes_chart', $storedbyteschart->render());
            unset($storedbyteschart);

            // Backup job name
            $jobs->addParameter('jobname', $backupjob_name);
            $where[] = 'Name = :jobname';

            // Backup job type
            $jobs->addParameter('jobtype', 'B');
            $where[] = "Type = :jobtype";

            // Backup job starttime and endtime
            $where[] = '(EndTime BETWEEN ' . $periods['starttime'] . ' AND ' . $periods['endtime'] . ')';

            $query = CDBQuery::get_Select(
                [
                    'table' => $jobs->getTableName(),
                    'fields' =>
                        ['JobId', 'Level', 'JobFiles', 'JobBytes', 'ReadBytes', 'Job.JobStatus', 'StartTime', 'EndTime', 'Name', 'Status.JobStatusLong'],
                    'where' => $where,
                    'orderby' => 'EndTime DESC',
                    'join' => [
                        [
                            'table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus'
                        ]
                    ]
                ], $jobs->get_driver_name()
            );

            $joblist = [];
            $joblevel = ['I' => 'Incr', 'D' => 'Diff', 'F' => 'Full'];
            $result = $jobs->run_query($query);

            foreach ($result->fetchAll() as $job) {
                // Job level description
                $job['joblevel'] = $joblevel[$job['level']];

                // Job execution execution time
                $job['elapsedtime'] = DateTimeUtil::Get_Elapsed_Time($job['starttime'], $job['endtime']);

                // Compression
                if (($job['jobbytes'] > 0) && ($job['readbytes'] > 0)) {
                    $compression = (1 - ($job['jobbytes'] / $job['readbytes']));
                    $job['compression'] = number_format($compression, 2);
                } else {
                    $job['compression'] = 'N/A';
                }

                // Job speed
                $start = $job['starttime'];
                $end = $job['endtime'];
                $seconds = DateTimeUtil::get_ElaspedSeconds($end, $start);

                if ($seconds !== false && $seconds > 0) {
                    $speed = $job['jobbytes'] / $seconds;
                    $job['speed'] = CUtils::Get_Human_Size($speed, 2) . '/s';
                } else {
                    $job['speed'] = 'N/A';
                }

                // Job bytes more easy to read
                $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
                $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);

                // Format date/time
                $job['starttime'] = date($this->session->get('datetime_format'), strtotime($job['starttime']));
                $job['endtime'] = date($this->session->get('datetime_format'), strtotime($job['endtime']));

                $joblist[] = $job;
            } // end while

            // Assign vars to template
            $this->setVar('jobs', $joblist);
            $this->setVar('backupjob_name', $backupjob_name);
            $this->setVar('perioddesc', $perioddesc);
            $this->setVar('backupjobbytes', $backupjobbytes);
            $this->setVar('backupjobfiles', $backupjobfiles);
        }

        return (new Response($this->render('backupjob-report.tpl')));
    }
}
