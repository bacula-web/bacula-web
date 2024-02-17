<?php

declare(strict_types=1);

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

namespace App\Controller;

use App\Libs\Config;
use App\Table\JobTable;
use Core\Db\CDBQuery;
use Core\Db\DatabaseFactory;
use Core\Exception\AppException;
use Core\Exception\ConfigFileException;
use Core\Graph\Chart;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use Core\Helpers\Sanitizer;
use Exception;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BackupJobController
{
    private Twig $view;
    private JobTable $jobTable;
    private SessionInterface $session;

    /**
     * @var string|null
     */
    private ?string $basePath;

    private Config $config;

    public function __construct(Twig $view, JobTable $jobTable, SessionInterface $session, Config $config)
    {
        $this->view = $view;
        $this->jobTable = $jobTable;
        $this->session = $session;
        $this->config = $config;

        $this->basePath = $this->config->get('basepath', null);
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
     * @throws Exception
     */
    public function index(Request $request, Response $response): Response
    {
        $tplData = [];
        $currentDateTime = DatabaseFactory::getDatabase($this->session->get('catalog_id'))->getServerTimestamp();
        $interval[1] = $currentDateTime;

        $daysstoredbytes = [];
        $daysstoredfiles = [];

        $tplData['periods_list'] = [
            ['days' => '7', 'label' => 'Last week'],
            ['days' => '14', 'label' => 'Last 2 weeks'],
            ['days' => '30', 'label' => 'Last month']
        ];

        // Get backup job(s) list
        $jobslist = $this->jobTable->get_Jobs_List(null, 'B');

        $tplData['jobs_list'] = $jobslist;

        $postData = $request->getParsedBody();
        $requestData = $request->getQueryParams();

        // Check backup job name from $_POST request
        $backupjob_name = null;

        if ($request->getMethod() === 'POST') {
            $backupjob_name = $postData['backupjob_name'];
        } elseif ($request->getMethod() === 'GET') {
            if (isset($requestData['backupjob_name'])) {
                $backupjob_name = $requestData['backupjob_name'];
            }
        }

        $backupjob_name = Sanitizer::sanitize($backupjob_name);

        $where = [];

        if ($backupjob_name == null) {
            $tplData['selected_jobname'] = '';
            $tplData['no_report_options'] = 'true';

            // Set selected period
            $tplData['selected_period'] = 7;
        } else {
            $tplData['no_report_options'] = 'false';

            // Make sure provided backupjob_name does exists
            if (!in_array($backupjob_name, $jobslist)) {
                $this->session->getFlash()->set('error', ['Invalid Backup Job name']);
                $this->session->save();

                return $response
                    ->withHeader('Location', $this->basePath . '/backupjob')
                    ->withStatus(302);
            }

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
                    $start = new \DateTimeImmutable('@' . $currentDateTime - WEEK);
                    $end = new \DateTimeImmutable('@' . $currentDateTime);
                    $interval[0] = $currentDateTime - WEEK;
                    break;
                case '14':
                    $start = new \DateTimeImmutable('@' . $currentDateTime - (2 * WEEK));
                    $end = new \DateTimeImmutable('@' . $currentDateTime);
                    $interval[0] = $currentDateTime - (2 * WEEK);
                    break;
                case '30':
                    $start = new \DateTimeImmutable('@' . $currentDateTime - MONTH);
                    $end = new \DateTimeImmutable('@' . $currentDateTime);
                    $interval[0] = $currentDateTime - MONTH;
                    break;
                default:
                    throw new AppException('Provided backup job period not supported');
            }

            $perioddesc .= $start->format($datetimeFormatShort) . " to " . $end->format($datetimeFormatShort);

            // Get start and end datetime for backup jobs report and charts
            $periods = CDBQuery::get_Timestamp_Interval($this->jobTable->get_driver_name(), $interval);

            $backupjobbytes = $this->jobTable->getStoredBytes($interval, $backupjob_name);
            $backupjobbytes = CUtils::Get_Human_Size($backupjobbytes);

            // Stored files on the defined period
            $backupjobfiles = $this->jobTable->getStoredFiles($interval, $backupjob_name);
            $backupjobfiles = CUtils::format_Number($backupjobfiles);

            // Get the last 7 days interval (start and end)
            $days = DateTimeUtil::getLastDaysIntervals($interval[1], (int) $backupjob_period);

            // Last 7 days stored files chart
            foreach ($days as $day) {
                $storedfiles = $this->jobTable->getStoredFiles([$day['start'], $day['end']], $backupjob_name);
                $dayStartTime = new \DateTimeImmutable('@' . $day['start']);
                $daysstoredfiles[] = [
                    $dayStartTime->format('m-d'), $storedfiles
                ];
            }

            $storedfileschart = new Chart( [
                'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $daysstoredfiles,
                'ylabel' => 'Files'
                ]
            );

            $tplData['stored_files_chart_id'] = $storedfileschart->name;
            $tplData['stored_files_chart'] = $storedfileschart->render();
            unset($storedfileschart);

            // Last 7 days stored bytes chart
            foreach ($days as $day) {
                $storedbytes = $this->jobTable->getStoredBytes(array($day['start'], $day['end']), $backupjob_name);
                $dayStartTime = new \DateTimeImmutable('@' . $day['start']);
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
            $this->jobTable->addParameter('jobname', $backupjob_name);
            $where[] = 'Name = :jobname';

            // Backup job type
            $this->jobTable->addParameter('jobtype', 'B');
            $where[] = "Type = :jobtype";

            // Backup job starttime and endtime
            $where[] = '(EndTime BETWEEN ' . $periods['starttime'] . ' AND ' . $periods['endtime'] . ')';

            $query = CDBQuery::get_Select(
                [
                    'table' => $this->jobTable->getTableName(),
                    'fields' =>
                        ['JobId', 'Level', 'JobFiles', 'JobBytes', 'ReadBytes', 'Job.JobStatus', 'StartTime', 'EndTime', 'Name', 'Status.JobStatusLong'],
                    'where' => $where,
                    'orderby' => 'EndTime DESC',
                    'join' => [
                        [
                            'table' => 'Status', 'condition' => 'Job.JobStatus = Status.JobStatus'
                        ]
                    ]
                ], $this->jobTable->get_driver_name()
            );

            $joblist = [];
            $joblevel = ['I' => 'Incr', 'D' => 'Diff', 'F' => 'Full'];
            $result = $this->jobTable->run_query($query);

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
                $jobStartTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $job['starttime']);
                $job['starttime'] = $jobStartTime->format(
                    $this->config->get('datetime_format', 'Y-m-d H:i:s')
                );

                $jobEndTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $job['endtime']);
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
