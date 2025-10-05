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

namespace App\Table;

use Core\Db\CDBQuery;
use Core\Db\Table;
use Core\Exception\AppException;
use Core\Exception\DatabaseException;
use Core\Utils\CUtils;
use Exception;

class JobTable extends Table
{
    protected ?string $tablename = 'Job';

    /**
     * @param array<int,int> $period_timestamps
     * @param string $job_status
     * @param string $job_level
     * @return int
     * @throws Exception
     */
    public function count_Jobs(array $period_timestamps, string $job_status = null, string $job_level = null): int
    {
        $where = null;
        $fields = ['COUNT(*) as job_count'];

        // Getting timestamp interval
        $intervals = CDBQuery::get_Timestamp_Interval($this->db->getDriverName(), $period_timestamps);

        // Defining interval depending on job status
        if (!is_null($job_status)) {
            switch ($job_status) {
                // Using Bacula version 5.0.3, waiting jobs have both starttime and endtime set 0000-00-00 00:00:00
                // Running set to YYYY-mm-dd hh:mm:ss (replace by real time) and endtime set to 0000-00-00 00:00:00
                // So, I'd not use starttime and endtime for waiting and running jobs here
                case 'waiting':
                case 'running':
                    break;
                default:
                    $where = [
                        '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') '
                    ];
            } // end switch
        } else {
            $where[] = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        }

        // Job status
        if (!is_null($job_status)) {
            switch ($job_status) {
                case 'running':
                    $where[] = "JobStatus = 'R'" ;
                    break;
                case 'completed':
                    $where[] = "JobStatus = 'T' ";
                    break;
                case 'completed with errors':
                    $where[] = "JobStatus IN ('E', 'e') ";
                    break;
                case 'failed':
                    $where[] = "JobStatus = 'f' ";
                    break;
                case 'canceled':
                    $where[] = "JobStatus = 'A' ";
                    break;
                case 'waiting':
                    $where[] = "JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
                    break;
                default:
                    throw new AppException('Provided job status is not supported');
            }
        }

        // Job level
        if (!is_null($job_level)) {
            $where[] = "Level = '$job_level' ";
        }

        // Building SQL statment
        $statment = ['table' => $this->tablename, 'fields' => $fields, 'where' => $where];
        $statment = CDBQuery::get_Select($statment);

        // Execute SQL statment
        $result = $this->run_query($statment);
        $result = $result->fetch();
        return (int) $result['job_count'];
    }

    /**
     * @param array<int,int> $period_timestamps Array containing start and end date (unix timestamp format)
     * @param string $job_name
     * @param string $client_id
     * @return int|mixed
     * @throws Exception
     */
    public function getStoredFiles(array $period_timestamps = [], string $job_name = 'ALL', string $client_id = 'ALL'): float
    {
        $where      = [];
        $fields     = ['SUM(JobFiles) AS stored_files'];

        // Defined period
        if (!empty($period_timestamps)) {
            $intervals     = CDBQuery::get_Timestamp_Interval($this->db->getDriverName(), $period_timestamps);
            $where[] = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        }

        if ($job_name != 'ALL') {
            $this->addParameter('jobname', $job_name);
            $where[] = 'name = :jobname';
        }

        if ($client_id != 'ALL') {
            $this->addParameter('clientid', $client_id);
            $where[] = 'clientid = :clientid';
        }

        // Get stored files only for Bacula job type <Backup>
        $this->addParameter('jobtype', 'B');
        $where[] = 'Type = :jobtype';

        // Building SQL statment
        $statment = ['table' => $this->tablename, 'fields' => $fields, 'where' => $where];
        $statment = CDBQuery::get_Select($statment);

        // Execute query
        $result = $this->run_query($statment);
        $result = $result->fetch();

        // If result == null, return 0 instead
        if (is_null($result['stored_files'])) {
            return 0;
        } else {
            return (float)$result['stored_files'];
        }
    }

    /**
     * @param array<int,int> $period_timestamps
     * @param string $job_name
     * @param string $client_id
     * @return int|mixed
     * @throws Exception
     */
    public function getStoredBytes(array $period_timestamps = [], string $job_name = 'ALL', string $client_id = 'ALL')
    {
        $where = [];
        $fields  = ['SUM(JobBytes) AS stored_bytes'];
        $jobtype  = 'B';

        // Defined period
        if (!empty($period_timestamps)) {
            $intervals = CDBQuery::get_Timestamp_Interval($this->db->getDriverName(), $period_timestamps);
            $where[] = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
        }

        if ($job_name != 'ALL') {
            $this->addParameter('jobname', $job_name);
            $where[] = 'name = :jobname';
        }

        if ($client_id != 'ALL') {
            $this->addParameter('clientid', $client_id);
            $where[] = 'clientid = :clientid';
        }

        // // Get stored files only for Bacula job with type = 'B'
        $this->addParameter('jobtype', $jobtype);
        $where[] = 'Type = :jobtype';

        // Building SQL statment
        $statment = ['table' => $this->tablename, 'fields' => $fields, 'where' => $where];
        $statment = CDBQuery::get_Select($statment);

        // Execute query
        $result = $this->run_query($statment);
        $result = $result->fetch();

        // If result == null, return 0 instead
        if (is_null($result['stored_bytes'])) {
            return 0;
        } else {
            return $result['stored_bytes'];
        }
    }

    /**
     * @return int
     */
    public function count_Job_Names(): int
    {
        $fields = ['COUNT(DISTINCT Name) AS job_name_count'];

        // Prepare and execute query
        $statment = CDBQuery::get_Select(
            [
                'table' => $this->tablename, 'fields' => $fields
            ]
        );

        $result = $this->run_query($statment);
        $result = $result->fetch();
        return (int) $result['job_name_count'];
    }

    /**
     * @param $client_id
     * @param string $job_type
     * @return array<int,string>
     */
    public function get_Jobs_List(string $job_type = null): array
    {
        $jobs   = [];
        $fields = ['Name'];
        $where  = null;

        // Job type filter
        if (!is_null($job_type)) {
            $this->addParameter('jobtype', $job_type);
            $where[] = 'type = :jobtype';
        }

        $statment   = [
            'table' => $this->tablename,
            'fields' => $fields,
            'groupby' => 'Name',
            'orderby' => 'Name',
            'where' => $where
        ];

        $result = $this->run_query(CDBQuery::get_Select($statment));

        foreach ($result->fetchAll() as $job) {
            $jobs[] = $job['name'];
        }

        return $jobs;
    }

    /**
     * Return an array which contains stored bytes and files of completed backup jobs of each day of the week
     *
     * @return array<array<string,string>>|null
     * @throws AppException
     */
    public function getWeeklyJobsStats()
    {
        $fields = ['SUM(Job.Jobbytes) as jobbytes' , 'SUM(Job.Jobfiles) as jobfiles'];
        $where = ["Job.JobStatus = 'T'", "Job.Type = 'B'"];
        $orderby = 'JobBytes DESC';
        $groupby = 'dayofweek';
        $res = [];

        switch ($this->db->getDriverName()) {
            case 'mysql':
                $fields[] = "FROM_UNIXTIME(Job.JobTDate, '%W') AS dayofweek";
                break;
            case 'pgsql':
                $fields[] = 'extract(dow from Job.EndTime::timestamp) AS dayofweek';
                break;
            case 'sqlite':
                return null;
                break;
            default:
                throw new AppException('This driver is not supported');
        }

        $query = CDBQuery::get_Select(
            [
                'table' => $this->tablename,
                'fields' => $fields,
                'where' => $where,
                'groupby' => $groupby,
                'orderby' => $orderby
            ]
        );

        $result = $this->run_query($query);

        $week = [0 => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($result->fetchAll() as $day) {
            $day['jobbytes'] = CUtils::GetHumanSize($day['jobbytes']);
            $day['jobfiles'] = number_format((float)$day['jobfiles']);

            // Simply fix day name for postgreSQL
            // It could be improved but I lack some SQL (postgreSQL skills)
            if ($this->db->getDriverName() == 'pgsql') {
                $day['dayofweek'] = $week[ $day['dayofweek'] ];
            }

            $res[] = $day;
        }

        return $res;
    }

    /**
     * Return an array of the top 10 backup jobs (used stored bytes)
     *
     * @return array<array<string,string>>
     */
    public function getBiggestJobsStats(): array
    {
        $fields = ['Job.Jobbytes', 'Job.Jobfiles', 'Job.Name'];
        $where = ["Job.JobStatus = 'T'", "Job.Type = 'B'"];
        $res = [];

        $query = CDBQuery::get_Select(
            [
                'table' => $this->tablename,
                'fields' => $fields,
                'where' => $where,
                'orderby' => 'jobbytes DESC',
                'limit' => '10'
            ]
        );

        $result = $this->run_query($query);

        foreach ($result->fetchAll() as $job) {
            $job['jobbytes'] = CUtils::GetHumanSize($job['jobbytes']);
            $job['jobfiles'] = number_format((float)$job['jobfiles']);
            $res[] = $job;
        }

        return $res;
    }
}
