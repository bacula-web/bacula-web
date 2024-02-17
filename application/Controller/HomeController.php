<?php

/**
 * Copyright (C) 2004 Juan Luis Frances Jimenez
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
use App\Table\PoolTable;
use App\Table\VolumeTable;
use Core\Db\DatabaseFactory;
use Exception;
use Odan\Session\SessionInterface;
use Slim\Views\Twig;
use Core\Db\CDBQuery;
use Core\Exception\AppException;
use Core\Graph\Chart;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use Core\Helpers\Sanitizer;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController
{
    private JobTable $jobTable;
    private PoolTable $poolTable;
    private VolumeTable $volumeTable;
    private SessionInterface $session;
    private Config $config;
    private Twig $view;


    public function __construct(
        JobTable         $jobTable,
        PoolTable        $poolTable,
        VolumeTable      $volumeTable,
        SessionInterface $session,
        Config           $config,
        Twig             $view
    )
    {
        $this->jobTable = $jobTable;
        $this->poolTable = $poolTable;
        $this->volumeTable = $volumeTable;
        $this->session = $session;
        $this->config = $config;
        $this->view = $view;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws AppException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function prepare(Request $request, Response $response): Response
    {
        $tplData = [];

        $selectedPeriod = 'last_day';
        $postData = $request->getParsedBody();
        if (isset($postData['period_selector'])) {
            $selectedPeriod = Sanitizer::sanitize($postData['period_selector']);
        }

        $tplData['custom_period_list_selected'] = $selectedPeriod;

        $tplData['custom_period_list'] = [
            ['id' => 'last_day', 'label' => 'Last 24 hours'],
            ['id' => 'last_week', 'label' => 'Last week'],
            ['id' => 'last_month', 'label' => 'Last month'],
            ['id' => 'since_bot', 'label' => 'Since BOT']
        ];

        // Custom period for dashboard
        $currentDateTime = DatabaseFactory::getDatabase($this->session->get('catalog_id'))->getServerTimestamp();

        $no_period = [
            FIRST_DAY, $currentDateTime
        ];
        $last_day = [
            $currentDateTime - DAY, $currentDateTime
        ];

        // Default period (last day)
        $custom_period = $last_day;

        switch ($selectedPeriod) {
            case 'last_day':
                $custom_period = [
                    $currentDateTime - DAY, $currentDateTime
                ];
                break;
            case 'last_week':
                $custom_period = [$currentDateTime - WEEK, $currentDateTime];
                break;
            case 'last_month':
                $custom_period = [$currentDateTime - MONTH, $currentDateTime];
                break;
            case 'since_bot':
                $custom_period = $no_period;
                break;
        }

        // Set period start - end for widget header
        $tplData['literal_period'] = strftime("%a %e %b %Y", $custom_period[0]) . ' to ' . strftime("%a %e %b %Y", $custom_period[1]);

        // Running, completed, failed, waiting and canceled jobTable status over last 24 hours
        $tplData['running_jobs'] = $this->jobTable->count_Jobs($custom_period, 'running');
        $tplData['completed_jobs'] = $this->jobTable->count_Jobs($custom_period, 'completed');
        $tplData['completed_with_errors_jobs'] = $this->jobTable->count_Jobs($custom_period, 'completed with errors');
        $tplData['failed_jobs'] = $this->jobTable->count_Jobs($custom_period, 'failed');
        $tplData['waiting_jobs'] = $this->jobTable->count_Jobs($custom_period, 'waiting');
        $tplData['canceled_jobs'] = $this->jobTable->count_Jobs($custom_period, 'canceled');

        // Stored files number
        $tplData['stored_files'] = CUtils::format_Number($this->jobTable->getStoredFiles($no_period));

        // Total bytes and files stored over the last 24 hours
        $tplData['bytes_last'] = CUtils::Get_Human_Size($this->jobTable->getStoredBytes($custom_period));
        $tplData['files_last'] = CUtils::format_Number($this->jobTable->getStoredFiles($custom_period));

        // Incremental, Differential and Full jobTable over the last 24 hours
        $tplData['incr_jobs'] = $this->jobTable->count_Jobs($custom_period, null, J_INCR);
        $tplData['diff_jobs'] = $this->jobTable->count_Jobs($custom_period, null, J_DIFF);
        $tplData['full_jobs'] = $this->jobTable->count_Jobs($custom_period, null, J_FULL);

        // ==============================================================
        // Last period <Job status graph>
        // ==============================================================

        $jobs_status = array('Running', 'Completed', 'Completed with errors', 'Waiting', 'Failed', 'Canceled');
        $jobs_status_data = array();

        foreach ($jobs_status as $status) {
            $jobs_count = $this->jobTable->count_Jobs($custom_period, strtolower($status));
            $jobs_status_data[] = array($status, $jobs_count);
        }

        $last_jobs_chart = new Chart(array('type' => 'pie', 'name' => 'chart_lastjobs', 'data' => $jobs_status_data, 'linked_report' => 'jobs'));
        $tplData['last_jobs_chart_id'] = $last_jobs_chart->name;

        $tplData['last_jobs_chart'] = $last_jobs_chart->render();
        unset($last_jobs_chart);

        // ==============================================================
        // Volumes per pool widget
        // ==============================================================

        $vols_by_pool = array();
        $max_pools = '9';
        $table_pool = 'Pool';
        $sum_vols = '';

        // Count defined poolTable in catalog
        $pools_count = $this->poolTable->count();

        // Display 9 biggest poolTable and rest of volumeTable in 10th one display as Other
        if ($pools_count > $max_pools) {
            $query = array('table' => $table_pool,
                'fields' => array('SUM(numvols) AS sum_vols'),
                'limit' => array('offset' => ($pools_count - $max_pools), 'count' => $pools_count),
                'groupby' => 'name');
            $result = $this->poolTable->run_query(CDBQuery::get_Select($query, $this->poolTable->get_driver_name()));
            $sum_vols = $result->fetch();
        }

        $query = array('table' => $table_pool, 'fields' => array('poolid,name,numvols'), 'orderby' => 'numvols DESC', 'limit' => $max_pools, $this->poolTable->get_driver_name());
        $result = $this->poolTable->run_query(CDBQuery::get_Select($query));

        foreach ($result as $pool) {
            $vols_by_pool[] = array($pool['name'], $pool['numvols']);
        }

        if ($pools_count > $max_pools) {
            $vols_by_pool[] = array('Others', $sum_vols['sum_vols']);
        }

        $pools_usage_chart = new Chart(array('type' => 'pie', 'name' => 'chart_pools_usage', 'data' => $vols_by_pool, 'linked_report' => 'pools'));

        $tplData['pools_usage_chart_id'] = $pools_usage_chart->name;
        $tplData['pools_usage_chart'] = $pools_usage_chart->render();

        unset($pools_usage_chart);

        // ==============================================================
        // Last 7 days stored Bytes widget
        // ==============================================================
        $days_stored_bytes = array();
        $days = DateTimeUtil::getLastDaysIntervals($currentDateTime, 7);

        foreach ($days as $day) {
            $days_stored_bytes[] = array(date("m-d", $day['start']), $this->jobTable->getStoredBytes(array($day['start'], $day['end'])));
        }

        $storedbytes_chart = new Chart(array('type' => 'bar', 'name' => 'chart_storedbytes', 'data' => $days_stored_bytes, 'ylabel' => 'Stored Bytes', 'uniformize_data' => true));

        $tplData['storedbytes_chart_id'] = $storedbytes_chart->name;
        $tplData['storedbytes_chart'] = $storedbytes_chart->render();

        unset($storedbytes_chart);

        // ==============================================================
        // Last 7 days Stored Files widget
        // ==============================================================
        $days_stored_files = array();
        $days = DateTimeUtil::getLastDaysIntervals($currentDateTime, 7);

        foreach ($days as $day) {
            $days_stored_files[] = array(date("m-d", $day['start']), $this->jobTable->getStoredFiles(array($day['start'], $day['end'])));
        }

        $storedfiles_chart = new Chart(array('type' => 'bar', 'name' => 'chart_storedfiles', 'data' => $days_stored_files, 'ylabel' => 'Stored files'));

        $tplData['storedfiles_chart_id'] = $storedfiles_chart->name;
        $tplData['storedfiles_chart'] = $storedfiles_chart->render();

        unset($storedfiles_chart);

        // ==============================================================
        // Last used volumeTable widget
        // ==============================================================

        $last_volumes = array();

        // Building SQL statment
        $where = array();
        $tmp = "(Media.Volstatus != 'Disabled') ";

        switch ($this->volumeTable->get_driver_name()) {
            case 'pgsql':
                $tmp .= "AND (Media.LastWritten IS NOT NULL)";
                break;
            case 'mysql':
            case 'sqlite':
                $tmp .= "AND (Media.Lastwritten != 0)";
        }

        $where[] = $tmp;

        $statment = array('table' => 'Media',
            'fields' => array('Media.MediaId', 'Media.Volumename', 'Media.Lastwritten', 'Media.VolStatus', 'Media.VolJobs', 'Pool.Name AS poolname'),
            'join' => array(
                array('table' => 'Pool', 'condition' => 'Media.PoolId = Pool.poolid')
            ),
            'where' => $where,
            'orderby' => 'Media.Lastwritten DESC',
            'limit' => '10');

        // Run the query
        $result = $this->volumeTable->run_query(CDBQuery::get_Select($statment, $this->volumeTable->get_driver_name()));

        foreach ($result as $volume) {
            if ($volume['lastwritten'] != '0000-00-00 00:00:00' && !is_null($volume['lastwritten'])) {

                $volume['lastwritten'] = date(
                    $this->config->get('datetime_format', 'Y-m-d H:i:s'),
                    strtotime($volume['lastwritten'])
                );
            } else {
                $volume['lastwritten'] = 'n/a';
            }
            $last_volumes[] = $volume;
        }

        $tplData['volumes_list'] = $last_volumes;

        // Per job name backup and restore statistics
        $job_types = array('R' => 'Restore', 'B' => 'Backup');      // TO IMPROVE

        $query = "SELECT count(*) AS JobsCount, sum(JobFiles) AS JobFiles, Type, sum(JobBytes) AS JobBytes, Name AS JobName FROM Job WHERE Type in ('B','R') GROUP BY Name,Type";
        $result = $this->jobTable->run_query($query);
        $jobs_result = array();

        foreach ($result->fetchAll() as $job) {
            $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);
            $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
            $job['type'] = $job_types[$job['type']];
            $jobs_result[] = $job;
        }

        $tplData['jobnames_jobs_stats'] = $jobs_result;

        // Per job type backup and restore statistics
        $query = "SELECT count(*) AS JobsCount, sum(JobFiles) AS JobFiles, Type, sum(JobBytes) AS JobBytes FROM Job WHERE Type in ('B','R') GROUP BY Type";
        $result = $this->jobTable->run_query($query);
        $jobs_result = null;

        foreach ($result->fetchAll() as $job) {
            $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);
            $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
            $job['type'] = $job_types[$job['type']];
            $jobs_result[] = $job;
        }

        $tplData['jobtypes_jobs_stats'] = $jobs_result;

        // Weekly jobTable statistics
        $tplData['weeklyjobsstats'] = $this->jobTable->getWeeklyJobsStats();

        // 10 biggest completed backup jobTable
        $tplData['biggestjobs'] = $this->jobTable->getBiggestJobsStats();

        return $this->view->render($response, 'pages/dashboard.html.twig', $tplData);
    }
}
