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

 use App\Tables\JobTable;
 use App\Tables\PoolTable;
 use App\Tables\VolumeTable;
 use Core\App\Controller;
 use Core\Db\CDBQuery;
 use Core\Exception\AppException;
 use Core\Exception\ConfigFileException;
 use Core\Graph\Chart;
 use Core\Utils\CUtils;
 use Core\Utils\DateTimeUtil;
 use Core\Helpers\Sanitizer;
 use SmartyException;
 use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @param JobTable $jobTable
     * @param PoolTable $poolTable
     * @param VolumeTable $volumeTable
     * @return Response
     * @throws SmartyException
     * @throws AppException
     * @throws ConfigFileException
     */
    public function prepare(JobTable $jobTable, PoolTable $poolTable, VolumeTable $volumeTable): Response
    {
        require_once BW_ROOT . '/core/const.inc.php';

        // Custom period for dashboard
        $no_period = array(FIRST_DAY, NOW);
        $last_day = array(LAST_DAY, NOW);

        // Default period (last day)
        $custom_period = $last_day;
        $selected_period = 'last_day';

        if ($this->request->request->get('period_selector')) {
            $selected_period = $this->request->request->get('period_selector');
            $selected_period = Sanitizer::sanitize($selected_period);
            $this->setVar('custom_period_list_selected', $selected_period);

            switch ($selected_period) {
                case 'last_day':
                    $custom_period = array( LAST_DAY, NOW);
                    break;
                case 'last_week':
                    $custom_period = array( LAST_WEEK, NOW);
                    break;
                case 'last_month':
                    $custom_period = array( LAST_MONTH, NOW);
                    break;
                case 'since_bot':
                    $custom_period = $no_period;
                    break;
            }
        } else {
            $this->setVar('custom_period_list_selected', $selected_period);
        }

        $custom_period_list = array( 'last_day' => 'Last 24 hours',
                                'last_week' => 'Last week',
                                'last_month' => 'Last month',
                                'since_bot' => 'Since BOT');

        $this->setVar('custom_period_list', $custom_period_list);

        // Set period start - end for widget header
        $this->setVar('literal_period', strftime("%a %e %b %Y", $custom_period[0]) . ' to ' . strftime("%a %e %b %Y", $custom_period[1]));

        // Running, completed, failed, waiting and canceled jobTable status over last 24 hours
        $this->setVar('running_jobs', $jobTable->count_Jobs($custom_period, 'running'));
        $this->setVar('completed_jobs', $jobTable->count_Jobs($custom_period, 'completed'));
        $this->setVar('completed_with_errors_jobs', $jobTable->count_Jobs($custom_period, 'completed with errors'));
        $this->setVar('failed_jobs', $jobTable->count_Jobs($custom_period, 'failed'));
        $this->setVar('waiting_jobs', $jobTable->count_Jobs($custom_period, 'waiting'));
        $this->setVar('canceled_jobs', $jobTable->count_Jobs($custom_period, 'canceled'));

        // Stored files number
        $this->setVar('stored_files', CUtils::format_Number($jobTable->getStoredFiles($no_period)));

        // Total bytes and files stored over the last 24 hours
        $this->setVar('bytes_last', CUtils::Get_Human_Size($jobTable->getStoredBytes($custom_period)));
        $this->setVar('files_last', CUtils::format_Number($jobTable->getStoredFiles($custom_period)));

        // Incremental, Differential and Full jobTable over the last 24 hours
        $this->setVar('incr_jobs', $jobTable->count_Jobs($custom_period, null, J_INCR));
        $this->setVar('diff_jobs', $jobTable->count_Jobs($custom_period, null, J_DIFF));
        $this->setVar('full_jobs', $jobTable->count_Jobs($custom_period, null, J_FULL));

        // ==============================================================
        // Last period <Job status graph>
        // ==============================================================

        $jobs_status = array('Running', 'Completed', 'Completed with errors', 'Waiting', 'Failed', 'Canceled');
        $jobs_status_data = array();

        foreach ($jobs_status as $status) {
            $jobs_count = $jobTable->count_Jobs($custom_period, strtolower($status));
            $jobs_status_data[] = array($status, $jobs_count );
        }

        $last_jobs_chart = new Chart(array(   'type' => 'pie', 'name' => 'chart_lastjobs', 'data' => $jobs_status_data, 'linked_report' => 'jobs' ));
        $this->setVar('last_jobs_chart_id', $last_jobs_chart->name);
        $this->setVar('last_jobs_chart', $last_jobs_chart->render());

        unset($last_jobs_chart);

        // ==============================================================
        // Volumes per pool widget
        // ==============================================================

        $vols_by_pool = array();
        $max_pools = '9';
        $table_pool = 'Pool';
        $sum_vols = '';

        // Count defined poolTable in catalog
        $pools_count = $poolTable->count();

        // Display 9 biggest poolTable and rest of volumeTable in 10th one display as Other
        if ($pools_count > $max_pools) {
            $query = array( 'table' => $table_pool,
            'fields' => array('SUM(numvols) AS sum_vols'),
            'limit' => array( 'offset' => ($pools_count - $max_pools), 'count' => $pools_count),
            'groupby' => 'name');
            $result = $poolTable->run_query(CDBQuery::get_Select($query, $poolTable->get_driver_name()));
            $sum_vols = $result->fetch();
        }

        $query = array('table' => $table_pool, 'fields' => array('poolid,name,numvols'), 'orderby' => 'numvols DESC', 'limit' => $max_pools, $poolTable->get_driver_name());
        $result = $poolTable->run_query(CDBQuery::get_Select($query));

        foreach ($result as $pool) {
            $vols_by_pool[] = array($pool['name'], $pool['numvols']);
        }

        if ($pools_count > $max_pools) {
            $vols_by_pool[] = array('Others', $sum_vols['sum_vols']);
        }

        $pools_usage_chart = new Chart(array( 'type' => 'pie', 'name' => 'chart_pools_usage', 'data' => $vols_by_pool, 'linked_report' => 'pools' ));
        $this->setVar('pools_usage_chart_id', $pools_usage_chart->name);
        $this->setVar('pools_usage_chart', $pools_usage_chart->render());
        unset($pools_usage_chart);

        // ==============================================================
        // Last 7 days stored Bytes widget
        // ==============================================================
        $days_stored_bytes = array();
        $days = DateTimeUtil::getLastDaysIntervals(7);

        foreach ($days as $day) {
            $days_stored_bytes[] = array( date("m-d", $day['start']), $jobTable->getStoredBytes(array($day['start'], $day['end'])));
        }

        $storedbytes_chart = new Chart(array(   'type' => 'bar', 'name' => 'chart_storedbytes', 'data' => $days_stored_bytes, 'ylabel' => 'Stored Bytes', 'uniformize_data' => true ));

        $this->setVar('storedbytes_chart_id', $storedbytes_chart->name);
        $this->setVar('storedbytes_chart', $storedbytes_chart->render());

        unset($storedbytes_chart);

        // ==============================================================
        // Last 7 days Stored Files widget
        // ==============================================================
        $days_stored_files = array();
        $days = DateTimeUtil::getLastDaysIntervals(7);

        foreach ($days as $day) {
            $days_stored_files[] = array( date("m-d", $day['start']), $jobTable->getStoredFiles(array($day['start'], $day['end'])));
        }

        $storedfiles_chart = new Chart(array(   'type' => 'bar', 'name' => 'chart_storedfiles', 'data' => $days_stored_files, 'ylabel' => 'Stored files' ));

        $this->setVar('storedfiles_chart_id', $storedfiles_chart->name);
        $this->setVar('storedfiles_chart', $storedfiles_chart->render());

        unset($storedfiles_chart);

        // ==============================================================
        // Last used volumeTable widget
        // ==============================================================

        $last_volumes = array();

        // Building SQL statment
        $where = array();
        $tmp   = "(Media.Volstatus != 'Disabled') ";

        switch ($volumeTable->get_driver_name()) {
            case 'pgsql':
                $tmp .= "AND (Media.LastWritten IS NOT NULL)";
                break;
            case 'mysql':
            case 'sqlite':
                $tmp .= "AND (Media.Lastwritten != 0)";
        }

        $where[] = $tmp;

        $statment = array( 'table' => 'Media',
                       'fields' => array('Media.MediaId', 'Media.Volumename', 'Media.Lastwritten', 'Media.VolStatus', 'Media.VolJobs', 'Pool.Name AS poolname'),
                       'join' => array(
                          array('table' => 'Pool', 'condition' => 'Media.PoolId = Pool.poolid')
                       ),
                       'where' => $where,
                       'orderby' => 'Media.Lastwritten DESC',
                       'limit' => '10');

        // Run the query
        $result     = $volumeTable->run_query(CDBQuery::get_Select($statment, $volumeTable->get_driver_name()));

        foreach ($result as $volume) {
            if ($volume['lastwritten'] != '0000-00-00 00:00:00') {
                $volume['lastwritten'] = date($this->session->get('datetime_format'), strtotime($volume['lastwritten']));
                //$volume['lastwritten'] = date($_SESSION['datetime_format'], strtotime($volume['lastwritten']));
            } else {
                $volume['lastwritten'] = 'n/a';
            }
            $last_volumes[] = $volume;
        }

        $this->setVar('volumes_list', $last_volumes);

        // Per job name backup and restore statistics
        $job_types = array( 'R' => 'Restore', 'B' => 'Backup' );      // TO IMPROVE

        $query = "SELECT count(*) AS JobsCount, sum(JobFiles) AS JobFiles, Type, sum(JobBytes) AS JobBytes, Name AS JobName FROM Job WHERE Type in ('B','R') GROUP BY Name,Type";
        $result = $jobTable->run_query($query);
        $jobs_result = array();

        foreach ($result->fetchAll() as $job) {
            $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);
            $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
            $job['type'] = $job_types[ $job['type'] ];
            $jobs_result[] = $job;
        }

        $this->setVar('jobnames_jobs_stats', $jobs_result);

        // Per job type backup and restore statistics
        $query = "SELECT count(*) AS JobsCount, sum(JobFiles) AS JobFiles, Type, sum(JobBytes) AS JobBytes FROM Job WHERE Type in ('B','R') GROUP BY Type";
        $result = $jobTable->run_query($query);
        $jobs_result = null;

        foreach ($result->fetchAll() as $job) {
            $job['jobfiles'] = CUtils::format_Number($job['jobfiles']);
            $job['jobbytes'] = CUtils::Get_Human_Size($job['jobbytes']);
            $job['type'] = $job_types[ $job['type'] ];
            $jobs_result[] = $job;
        }

        $this->setVar('jobtypes_jobs_stats', $jobs_result);

        # Weekly jobTable statistics
        $this->setVar('weeklyjobsstats', $jobTable->getWeeklyJobsStats());

        # 10 biggest completed backup jobTable
        $this->setVar('biggestjobs', $jobTable->getBiggestJobsStats());

        return new Response($this->render('dashboard.tpl'));
    }
}
