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
use App\Tables\JobTable;
use Core\Db\CDBQuery;
use Core\Exception\AppException;
use Core\Exception\ConfigFileException;
use Core\Graph\Chart;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use Core\Helpers\Sanitizer;
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

    public function __construct(Twig $view, JobTable $jobTable, SessionInterface $session) {
        $this->view = $view;
        $this->jobTable = $jobTable;
        $this->session = $session;

        FileConfig::open(CONFIG_FILE);
        $this->basePath = FileConfig::get_Value('basepath') ?? null;
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
        require_once BW_ROOT . '/core/const.inc.php';

        $tplData = [];

        $interval = array();
        $interval[1] = NOW;

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

            $datetimeFormatShort = FileConfig::get_Value('datetime_format_short') ?? null;

            if (is_null($datetimeFormatShort)) {
                $datetimeFormatShort = explode(' ', FileConfig::get_Value('datetime_format'));
                $datetimeFormatShort = $datetimeFormatShort[0];
            }

            switch ($backupjob_period) {
                case '7':
                    $perioddesc .= date( $datetimeFormatShort, (NOW - WEEK)) . " to " . date( $datetimeFormatShort, NOW);
                    $interval[0] = NOW - WEEK;
                    break;
                case '14':
                    $perioddesc .= date( $datetimeFormatShort, (NOW - (2 * WEEK))) . " to " . date( $datetimeFormatShort, NOW);
                    $interval[0] = NOW - (2 * WEEK);
                    break;
                case '30':
                    $perioddesc .= date($datetimeFormatShort, (NOW - MONTH)) . " to " . date($datetimeFormatShort, NOW);
                    $interval[0] = NOW - MONTH;
                    break;
                default:
                    throw new AppException('Provided backup job period not supported');
            }

            // Get start and end datetime for backup jobs report and charts
            $periods = CDBQuery::get_Timestamp_Interval($this->jobTable->get_driver_name(), $interval);

            $backupjobbytes = $this->jobTable->getStoredBytes($interval, $backupjob_name);
            $backupjobbytes = CUtils::Get_Human_Size($backupjobbytes);

            // Stored files on the defined period
            $backupjobfiles = $this->jobTable->getStoredFiles($interval, $backupjob_name);
            $backupjobfiles = CUtils::format_Number($backupjobfiles);

            // Get the last 7 days interval (start and end)
            $days = DateTimeUtil::getLastDaysIntervals($backupjob_period);

            // Last 7 days stored files chart
            foreach ($days as $day) {
                $storedfiles = $this->jobTable->getStoredFiles(array($day['start'], $day['end']), $backupjob_name);
                $daysstoredfiles[] = array(date("m-d", $day['start']), $storedfiles);
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
                $job['starttime'] = date(FileConfig::get_Value('datetime_format'), strtotime($job['starttime']));
                $job['endtime'] = date(FileConfig::get_Value('datetime_format'), strtotime($job['endtime']));

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
