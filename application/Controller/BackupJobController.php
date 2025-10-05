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

use App\Table\JobTable;
use Core\Controller\AbstractController;
use Core\Db\CDBQuery;
use Core\Db\DatabaseFactory;
use Core\Exception\AppException;
use Core\Exception\ValidationException;
use Core\Graph\Chart;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use Core\Helpers\Sanitizer;
use DateTimeImmutable;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

class BackupJobController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @param JobTable $jobTable
     * @return ResponseInterface
     * @throws AppException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function index(Request $request, Response $response, JobTable $jobTable): ResponseInterface
    {
        $tplData = [];
        $currentDateTime = DatabaseFactory::getDatabase($this->session->get('catalog_id'))->getServerTimestamp();
        $interval[1] = $currentDateTime;

        $daysstoredbytes = [];
        $daysstoredfiles = [];

        $tplData['periods_list'] = [
            '7' => 'Last 7 days',
            '14' => 'Last 14 days',
            '30' => 'Last 30 days'
        ];

        // Get backup job(s) list
        $jobslist = $jobTable->get_Jobs_List('B');

        $tplData['jobs_list'] = $jobslist;

        $postData = $request->getParsedBody();
        $requestData = $request->getQueryParams();

        // Check backup job name from $_POST request
        $backupjob_name = null;

        /**
         * TODO: check if request is only POST
         */
        if ($request->getMethod() === 'POST') {
            $backupjob_name = $postData['backupjob_name'];
        } elseif ($request->getMethod() === 'GET') {
            if (isset($requestData['backupjob_name'])) {
                $backupjob_name = $requestData['backupjob_name'];
            }
        }

        if ($request->getMethod() === 'POST') {
            $validator = (new Validator($request->getParsedBody(), ['backupjob_name', 'period']))
                ->rule('in', 'backupjob_name', $jobslist)->message('Invalid job name')
                ->rule('in', 'period', array_keys($tplData['periods_list']))->message('Invalid period');
            if (!$validator->validate()) {
                throw new ValidationException($validator->errors());
            }
        }
        
        $where = [];

        if ($backupjob_name == null) {
            $tplData['selected_jobname'] = '';
            $tplData['no_report_options'] = 'true';

            // Set selected period
            $tplData['selected_period'] = 7;
        } else {
            $tplData['no_report_options'] = 'false';

            $tplData['selected_jobname'] = $backupjob_name;

            /**
             * Get selected period from POST request, or set it to default value (7)
             */
            $backupjob_period = '7';

            if (isset($postData['period'])) {
                $backupjob_period = $postData['period'];
            }

            // Set selected period
            $tplData['selected_period'] = $backupjob_period;

            $perioddesc = 'From ';

            $datetimeFormatShort = $this->config->get('datetime_format_short', 'Y-m-d');

            switch ($backupjob_period) {
                case '7':
                    $start = new DateTimeImmutable('@' . $currentDateTime - WEEK);
                    $end = new DateTimeImmutable('@' . $currentDateTime);
                    $interval[0] = $currentDateTime - WEEK;
                    break;
                case '14':
                    $start = new DateTimeImmutable('@' . $currentDateTime - (2 * WEEK));
                    $end = new DateTimeImmutable('@' . $currentDateTime);
                    $interval[0] = $currentDateTime - (2 * WEEK);
                    break;
                case '30':
                    $start = new DateTimeImmutable('@' . $currentDateTime - MONTH);
                    $end = new DateTimeImmutable('@' . $currentDateTime);
                    $interval[0] = $currentDateTime - MONTH;
                    break;
                default:
                    throw new AppException('Provided backup job period not supported');
            }

            $perioddesc .= $start->format($datetimeFormatShort) . " to " . $end->format($datetimeFormatShort);

            // Get start and end datetime for backup jobs report and charts
            $periods = CDBQuery::get_Timestamp_Interval($jobTable->get_driver_name(), $interval);

            $backupjobbytes = $jobTable->getStoredBytes($interval, $backupjob_name);
            $backupjobbytes = CUtils::GetHumanSize($backupjobbytes);

            // Stored files on the defined period
            $backupjobfiles = $jobTable->getStoredFiles($interval, $backupjob_name);
            $backupjobfiles = number_format($backupjobfiles);

            // Get the last 7 days interval (start and end)
            $days = DateTimeUtil::getLastDaysIntervals($interval[1], (int) $backupjob_period);

            // Last 7 days stored files chart
            foreach ($days as $day) {
                $storedfiles = $jobTable->getStoredFiles([$day['start'], $day['end']], $backupjob_name);
                $dayStartTime = new DateTimeImmutable('@' . $day['start']);
                $daysstoredfiles[] = [
                    $dayStartTime->format('m-d'), $storedfiles
                ];
            }

            $storedfileschart = new Chart([
                'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $daysstoredfiles,
                'ylabel' => 'Files'
                ]);

            $tplData['stored_files_chart_id'] = $storedfileschart->name;
            $tplData['stored_files_chart'] = $storedfileschart->render();
            unset($storedfileschart);

            // Last 7 days stored bytes chart
            foreach ($days as $day) {
                $storedbytes = $jobTable->getStoredBytes(array($day['start'], $day['end']), $backupjob_name);
                $dayStartTime = new DateTimeImmutable('@' . $day['start']);
                $daysstoredbytes[] = [
                    $dayStartTime->format('m-d'), $storedbytes
                ];
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

            $tplData['stored_bytes_chart_id'] = $storedbyteschart->name;
            $tplData['stored_bytes_chart'] = $storedbyteschart->render();
            unset($storedbyteschart);

            // Backup job name
            $jobTable->addParameter('jobname', $backupjob_name);
            $where[] = 'Name = :jobname';

            // Backup job type
            $jobTable->addParameter('jobtype', 'B');
            $where[] = "Type = :jobtype";

            // Backup job starttime and endtime
            $where[] = '(EndTime BETWEEN ' . $periods['starttime'] . ' AND ' . $periods['endtime'] . ')';

            $query = CDBQuery::get_Select(
                [
                    'table' => $jobTable->getTableName(),
                    'fields' =>
                        [
                            'JobId',
                            'Level',
                            'JobFiles',
                            'JobBytes',
                            'ReadBytes',
                            'Job.JobStatus',
                            'StartTime',
                            'EndTime',
                            'Name',
                            'Status.JobStatusLong'
                        ],
                    'where' => $where,
                    'orderby' => 'EndTime DESC',
                    'join' => [
                        [
                            'table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus'
                        ]
                    ]
                ],
                $jobTable->get_driver_name()
            );

            $joblist = [];
            $joblevel = ['I' => 'Incr', 'D' => 'Diff', 'F' => 'Full'];
            $result = $jobTable->run_query($query);

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
                    $job['speed'] = CUtils::GetHumanSize($speed, 2) . '/s';
                } else {
                    $job['speed'] = 'N/A';
                }

                // Job bytes more easy to read
                $job['jobbytes'] = CUtils::GetHumanSize($job['jobbytes']);
                $job['jobfiles'] = number_format((float)$job['jobfiles']);

                // Format date/time
                $jobStartTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $job['starttime']);
                $job['starttime'] = $jobStartTime->format(
                    $this->config->get('datetime_format', 'Y-m-d H:i:s')
                );

                $jobEndTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $job['endtime']);
                $job['endtime'] = $jobEndTime->format(
                    $this->config->get('datetime_format', 'Y-m-d H:i:s')
                );

                $joblist[] = $job;
            } // end while

            // Assign vars to template
            $tplData['jobs'] = $joblist;
            $tplData['backupjob_name'] = $backupjob_name;
            $tplData['perioddesc'] = $perioddesc;
            $tplData['backupjobbytes'] = $backupjobbytes;
            $tplData['backupjobfiles'] = $backupjobfiles;
        }

        return $this->view->render($response, 'pages/backupjob-report.html.twig', $tplData);
    }
}
