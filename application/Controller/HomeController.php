<?php

/**
 * Copyright (C) 2004 Juan Luis Frances Jimenez
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
use App\Tables\PoolTable;
use App\Tables\VolumeTable;
use Core\App\View;
use Core\Db\CDBQuery;
use Core\Exception\AppException;
use Core\Exception\ConfigFileException;
use Core\Graph\Chart;
use Core\Utils\CUtils;
use Core\Utils\DateTimeUtil;
use Core\Helpers\Sanitizer;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SmartyException;

class HomeController
{
    private JobTable $jobTable;
    private PoolTable $poolTable;
    private VolumeTable $volumeTable;
    private View $view;

    public function __construct(
        JobTable    $jobTable,
        PoolTable   $poolTable,
        VolumeTable $volumeTable,
        View        $view,
    )
    {
        $this->jobTable = $jobTable;
        $this->poolTable = $poolTable;
        $this->volumeTable = $volumeTable;
        $this->view = $view;
        $this->view->setTemplate('dashboard.tpl');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws AppException
     * @throws ConfigFileException
     * @throws SmartyException
     */
    public function prepare(Request $request, Response $response): Response
    {
        require_once BW_ROOT . '/core/const.inc.php';

        $selectedPeriod = 'last_day';
        $postData = $request->getParsedBody();
        if ( isset($postData['period_selector'])) {
            $selectedPeriod = Sanitizer::sanitize($postData['period_selector']);
        }

        $this->view->set('custom_period_list_selected', $selectedPeriod);

        $this->view->set('custom_period_list', [
            'last_day' => 'Last 24 hours',
            'last_week' => 'Last week',
            'last_month' => 'Last month',
            'since_bot' => 'Since BOT'
        ]);

        // Custom period for dashboard
        $no_period = [FIRST_DAY, NOW];
        $last_day = [LAST_DAY, NOW];

        // Default period (last day)
        $custom_period = $last_day;

        switch ($selectedPeriod) {
            case 'last_day':
                $custom_period = [LAST_DAY, NOW];
                break;
            case 'last_week':
                $custom_period = [LAST_WEEK, NOW];
                break;
            case 'last_month':
                $custom_period = [LAST_MONTH, NOW];
                break;
            case 'since_bot':
                $custom_period = $no_period;
                break;
        }

        // Set period start - end for widget header
        $this->view->set('literal_period', strftime("%a %e %b %Y", $custom_period[0]) . ' to ' . strftime("%a %e %b %Y", $custom_period[1]));

        // Running, completed, failed, waiting and canceled jobTable status over last 24 hours
        $this->view->set('running_jobs', $this->jobTable->count_Jobs($custom_period, 'running'));
        $this->view->set('completed_jobs', $this->jobTable->count_Jobs($custom_period, 'completed'));
        $this->view->set('completed_with_errors_jobs', $this->jobTable->count_Jobs($custom_period, 'completed with errors'));
        $this->view->set('failed_jobs', $this->jobTable->count_Jobs($custom_period, 'failed'));
        $this->view->set('waiting_jobs', $this->jobTable->count_Jobs($custom_period, 'waiting'));
        $this->view->set('canceled_jobs', $this->jobTable->count_Jobs($custom_period, 'canceled'));

        // Stored files number
        $this->view->set('stored_files', CUtils::format_Number($this->jobTable->getStoredFiles($no_period)));

        // Total bytes and files stored over the last 24 hours
        $this->view->set('bytes_last', CUtils::Get_Human_Size($this->jobTable->getStoredBytes($custom_period)));
        $this->view->set('files_last', CUtils::format_Number($this->jobTable->getStoredFiles($custom_period)));

        // Incremental, Differential and Full jobTable over the last 24 hours
        $this->view->set('incr_jobs', $this->jobTable->count_Jobs($custom_period, null, J_INCR));
        $this->view->set('diff_jobs', $this->jobTable->count_Jobs($custom_period, null, J_DIFF));
        $this->view->set('full_jobs', $this->jobTable->count_Jobs($custom_period, null, J_FULL));

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
        $this->view->set('last_jobs_chart_id', $last_jobs_chart->name);
        $this->view->set('last_jobs_chart', $last_jobs_chart->render());

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
        $this->view->set('pools_usage_chart_id', $pools_usage_chart->name);
        $this->view->set('pools_usage_chart', $pools_usage_chart->render());
        unset($pools_usage_chart);

        // ==============================================================
        // Last 7 days stored Bytes widget
        // ==============================================================
        $days_stored_bytes = array();
        $days = DateTimeUtil::getLastDaysIntervals(7);

        foreach ($days as $day) {
            $days_stored_bytes[] = array(date("m-d", $day['start']), $this->jobTable->getStoredBytes(array($day['start'], $day['end'])));
        }

        $storedbytes_chart = new Chart(array('type' => 'bar', 'name' => 'chart_storedbytes', 'data' => $days_stored_bytes, 'ylabel' => 'Stored Bytes', 'uniformize_data' => true));

        $this->view->set('storedbytes_chart_id', $storedbytes_chart->name);
        $this->view->set('storedbytes_chart', $storedbytes_chart->render());

        unset($storedbytes_chart);

        // ==============================================================
        // Last 7 days Stored Files widget
        // ==============================================================
        $days_stored_files = array();
        $days = DateTimeUtil::getLastDaysIntervals(7);

        foreach ($days as $day) {
            $days_stored_files[] = array(date("m-d", $day['start']), $this->jobTable->getStoredFiles(array($day['start'], $day['end'])));
        }

        $storedfiles_chart = new Chart(array('type' => 'bar', 'name' => 'chart_storedfiles', 'data' => $days_stored_files, 'ylabel' => 'Stored files'));

        $this->view->set('storedfiles_chart_id', $storedfiles_chart->name);
        $this->view->set('storedfiles_chart', $storedfiles_chart->render());

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
            if ($volume['lastwritten'] != '0000-00-00 00:00:00') {
                $volume['lastwritten'] = date(FileConfig::get_Value('datetime_format'), strtotime($volume['lastwritten']));
                //$volume['lastwritten'] = date($this->session->get('datetime_format'), strtotime($volume['lastwritten']));
                //$volume['lastwritten'] = date($_SESSION['datetime_format'], strtotime($volume['lastwritten']));
            } else {
                $volume['lastwritten'] = 'n/a';
            }
            $last_volumes[] = $volume;
        }

        $this->view->set('volumes_list', $last_volumes);

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

        $this->view->set('jobnames_jobs_stats', $jobs_result);

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

        $this->view->set('jobtypes_jobs_stats', $jobs_result);

        # Weekly jobTable statistics
        $this->view->set('weeklyjobsstats', $this->jobTable->getWeeklyJobsStats());

        # 10 biggest completed backup jobTable
        $this->view->set('biggestjobs', $this->jobTable->getBiggestJobsStats());

        //$response->getBody()->write($this->view->render());
        return $response;
    }
}
