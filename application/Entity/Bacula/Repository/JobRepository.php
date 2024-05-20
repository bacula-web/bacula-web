<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of the Bacula-Web project.
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

namespace App\Entity\Bacula\Repository;

use App\Entity\Bacula\Job;
use Carbon\Carbon;
use App\Service\Chart;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[] findAll()
 * @method Job[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    private VersionRepository $catalog;

    /**
     * @param ManagerRegistry $registry
     * @param VersionRepository $catalog
     */
    public function __construct(
        ManagerRegistry $registry,
        VersionRepository $catalog
    ) {
        parent::__construct($registry, Job::class);
        $this->catalog = $catalog;
    }

    /**
     * Return used Bacula job types
     *
     * @return array
     */
    public function getUsedJobTypes(): array
    {
        $usedTypes = [];

        $jobTypes = ['B' => 'Backup',
            'M' => 'Migrated',
            'V' => 'Verify',
            'R' => 'Restore',
            'D' => 'Admin',
            'A' => 'Archive',
            'C' => 'Copy',
            'g' => 'Migration'
        ];

        $types = $this
            ->createQueryBuilder('j')
            ->select('j.type')
            ->distinct()
            ->getQuery()
            ->getSingleColumnResult();

        foreach ($types as $type) {
            $usedTypes[$type] = $jobTypes[$type];
        }

        return $usedTypes;
    }

    /**
     * Return a distinct list of used Bacula job levels
     *
     * @return array
     */
    public function getUsedLevels(): array
    {
        $levelList = [];

        $jobLevels = [
            'D' => 'Differential',
            'I' => 'Incremental',
            'F' => 'Full',
            'V' => 'InitCatalog',
            'C' => 'Catalog',
            'O' => 'VolumeToCatalog',
            'd' => 'DiskToCatalog',
            'A' => 'Data'
        ];

        $levels = $this
            ->createQueryBuilder('j')
            ->select('j.level')
            ->distinct()
            ->getQuery()
            ->getSingleColumnResult();

        foreach ($levels as $level) {
            $levelList[$level] = $jobLevels[$level];
        }
        return $levelList;
    }

    /**
     * Return distinct backup job name list
     *
     * @return array
     */
    public function getBackupJobsList(): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return $queryBuilder
            ->select('j.name')
            ->distinct()
            ->where("j.type = 'B'")
            ->orderBy('j.name')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return sum of jobs stored bytes within a specific period of time
     *
     * @param DateTime $from
     * @param DateTime $to
     * @param string|null $jobName
     * @param int|null $clientId
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getStoredBytesSum(DateTime $from, DateTime $to, string $jobName = null, int $clientId = null): int
    {
        $queryBuilder = $this->createQueryBuilder('j');
        $query = $queryBuilder
            ->select('SUM(j.jobbytes)')
            ->where('j.endtime BETWEEN :start AND :end')
            ->setParameter('start', $from)
            ->setParameter('end', $to)
            ->andWhere('j.type = :type')
            ->setParameter('type', 'B')
        ;

        if ($jobName) {
            $query
                ->andWhere('j.name = :jobname')
                ->setParameter('jobname', $jobName);
        }

        if ($clientId) {
            $query
                ->andWhere('j.clientid = :client')
                ->setParameter('client', $clientId);
        }

        return (int) $query
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return sum of files stored bytes within a specific period of time
     *
     * @param DateTime $from
     * @param DateTime $to
     * @param string|null $jobName
     * @param int|null $clientId
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getStoredFilesSum(DateTime $from, DateTime $to, string $jobName = null, int $clientId = null): int
    {
        $queryBuilder = $this->createQueryBuilder('j');
        $query = $queryBuilder
            ->select('SUM(j.jobfiles)')
            ->where('j.endtime BETWEEN :start AND :end')
            ->setParameter('start', $from)
            ->setParameter('end', $to)
            ->andWhere('j.type = :type')
            ->setParameter('type', 'B')
        ;

        if ($jobName) {
            $query
                ->andWhere('j.name = :jobname')
                ->setParameter('jobname', $jobName);
        }

        if ($clientId) {
            $query
                ->andWhere('j.clientid = :client')
                ->setParameter('client', $clientId);
        }

        return (int) $query
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return an array with sum of stored bytes for each day
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param string|null $jobName
     * @param int|null $clientId
     * @return array
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getJobStoredBytes(
        DateTimeInterface $from,
        DateTimeInterface $to,
        string $jobName = null,
        int $clientId = null
    ): array {
        $storedBytes = [];
        $diff = $from->diff($to);

        for ($day = $diff->days; $day >= 0; $day--) {
            $d = new Carbon(sprintf('-%d days', $day));
            $from = Carbon::createFromTimeString(sprintf('%s-%s-%s 0:0:0', $d->year, $d->month, $d->day));
            $to = Carbon::createFromTimeString(sprintf('%s-%s-%s 23:59:59', $d->year, $d->month, $d->day));

            if ($jobName) {
                $dailyBytesSum = $this->getStoredBytesSum($from, $to, $jobName);
            } elseif ($clientId) {
                $dailyBytesSum = $this->getStoredBytesSum($from, $to, null, $clientId);
            } else {
                $dailyBytesSum = $this->getStoredBytesSum($from, $to);
            }

            $storedBytes[$d->format('m-d')] = $dailyBytesSum;
        }

        return $storedBytes;
    }

    /**
     * Return an array with sum of stored bytes for each day
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param string|null $jobName
     * @param int|null $clientId
     * @return array
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getJobStoredFiles(
        DateTimeInterface $from,
        DateTimeInterface $to,
        string $jobName = null,
        int $clientId = null
    ): array {
        $storedFiles = [];
        $diff = $from->diff($to);

        for ($day = $diff->days; $day >= 0; $day--) {
            $d = new Carbon(sprintf('-%d days', $day));
            $from = Carbon::createFromTimeString(sprintf('%s-%s-%s 0:0:0', $d->year, $d->month, $d->day));
            $to = Carbon::createFromTimeString(sprintf('%s-%s-%s 23:59:59', $d->year, $d->month, $d->day));

            if ($jobName) {
                $dailyFilesSum = $this->getStoredFilesSum($from, $to, $jobName);
            } elseif ($clientId) {
                $dailyFilesSum = $this->getStoredFilesSum($from, $to, null, $clientId);
            } else {
                $dailyFilesSum = $this->getStoredFilesSum($from, $to);
            }

            $storedFiles[$d->format('m-d')] = $dailyFilesSum;
        }

        return $storedFiles;
    }

    /**
     * Return list of completed backup jobs within provided datetime range
     *
     * @param int $clientId
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @return array
     */
    public function getClientJobs(int $clientId, DateTimeInterface $from, DateTimeInterface $to): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $query = $queryBuilder
            ->select('j', 's')
            ->join('j.status', 's')
            ->andWhere('j.clientid = :client')
            ->setParameter('client', $clientId)
            ->andWhere('j.type = :type')
            ->setParameter('type', 'B')
            ->andWhere('j.status = :status')
            ->setParameter('status', 'T')
            ->andWhere('j.endtime BETWEEN :start AND :end')
            ->setParameter('start', $from)
            ->setParameter('end', $to)
            ->orderBy('j.endtime', 'DESC')
            ->getQuery()
            ;

        return $query->getResult();
    }

    /**
     * Return amount of jobs filtered by status.
     *
     * @param string $status
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countJobsByStatus(string $status, ?Carbon $from, ?Carbon $to): int
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $queryBuilder
            ->select('count(j.id)');

        switch ($status) {
            case 'running':
                $queryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'R');
                break;
            case 'completed':
                $queryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'T');
                break;
            case 'completed_with_errors':
                $queryBuilder
                    ->andWhere('j.status IN(:status)')
                    ->setParameter('status', ['E', 'e']);
                break;
            case 'waiting':
                $queryBuilder
                    ->andWhere('j.status IN(:status)')
                    ->setParameter('status', ['F','S','M','m','s','j','c','d','t','p','C']);
                break;
            case 'failed':
                $queryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'f');
                break;
            case 'canceled':
                $queryBuilder
                    ->andWhere('j.status = :status')
                    ->setParameter('status', 'A');
                break;
        }

        if ($from && $to) {
            $queryBuilder
                ->andWhere('j.endtime BETWEEN :from AND :to')
                ->setParameter('from', $from)
                ->setParameter('to', $to);
        }

        return (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return amount of jobs of specific level (incremental, differential, full, etc.) within
     * a specific period of time.
     *
     * @param string $level
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countJobsByLevel(string $level, ?Carbon $from, ?Carbon $to): int
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $queryBuilder
            ->select('count(j.id)')
            ->where('j.level = :level')
            ->setParameter('level', $level)
            ->andWhere('j.type = :type')
            ->setParameter('type', 'B')
        ;

        if ($from && $to) {
            $queryBuilder
                ->andWhere('j.endtime BETWEEN :from AND :to ')
                ->setParameter('from', $from)
                ->setParameter('to', $to)
            ;
        }

        return (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return an array for each jobs statuses within a specific period of time
     * This method is used to build charts
     *
     * @param Carbon $from
     * @param Carbon $to
     * @param string|null $linkedPage Route name of the linked page
     * @return Chart
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getJobStatusChart(Carbon $from, Carbon $to, string $linkedPage = null): Chart
    {
        $jobsStatuses = [
            'Running' => 'running',
            'Completed' => 'completed',
            'Completed with errors' => 'completed_with_errors',
            'Waiting' => 'waiting',
            'Failed' => 'failed',
            'Canceled' => 'canceled'
        ];

        $chartData = [];

        foreach ($jobsStatuses as $label => $status) {
            $chartData[$label] = $this->countJobsByStatus($status, $from, $to);
        }

        return new Chart([
                'type' => 'pie',
                'name' => 'chart_lastjobs',
                'data' => $chartData,
                'linked_report' => $linkedPage
            ]);
    }

    /**
     * Return a list of the top 10 biggest (job bytes) backup jobs
     *
     * @return array
     */
    public function getBiggestJobs(): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return $queryBuilder
            ->select('j.name,
             j.type,
             COUNT(j.id) AS jobs_count,
             SUM(j.jobfiles) AS jobs_files,
             SUM(j.jobbytes) AS jobs_bytes')
            ->groupBy('j.type')
            ->addGroupBy('j.name')
            ->where('j.type IN(:types)')
            ->setParameter('types', ['B', 'R'])
            ->getQuery()
            ->getResult();
    }

    /**
     * Return backup and restore job statistics
     *
     * @return array
     */
    public function getStatisticsPerType(): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return $queryBuilder
            ->select('COUNT(j.id) AS jobs_count, SUM(j.jobfiles) AS jobs_files, SUM(j.jobbytes) AS jobs_bytes')
            ->where('j.type IN(:types)')
            ->setParameter('types', ['B', 'R'])
            //->groupBy('j.name')
            ->groupBy('j.type')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return an array which contains stored bytes and files of completed backup jobs of each day of the week
     *
     * @return array<int,array<string,string>>|null
     */
    public function getWeeklyJobsStats(): ?array
    {
        $weeklyJobStats = [
            'Sunday' => ['job_bytes' => 0, 'job_files' => 0],
            'Monday' => ['job_bytes' => 0, 'job_files' => 0],
            'Tuesday' => ['job_bytes' => 0, 'job_files' => 0],
            'Wednesday' => ['job_bytes' => 0, 'job_files' => 0],
            'Thursday' => ['job_bytes' => 0, 'job_files' => 0],
            'Friday' => ['job_bytes' => 0, 'job_files' => 0],
            'Saturday' => ['job_bytes' => 0, 'job_files' => 0]
        ];

        $qb = $this->createQueryBuilder('j');
        $result = $qb
            ->select('j.jobfiles, j.jobbytes, j.endtime')
            ->where('j.status = :status')
            ->setParameter('status', 'T')
            ->andWhere('j.type = :type')
            ->setParameter('type', 'B')
            ->getQuery()
            ->getResult();

        foreach ($result as $job) {
            $day = Carbon::create($job['endtime'])->dayName;
            $weeklyJobStats[$day]['job_files'] += $job['jobfiles'];
            $weeklyJobStats[$day]['job_bytes'] += $job['jobbytes'];
        }

        return $weeklyJobStats;
    }

    /**
     * @return Chart
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getLastWeekStoredBytesChart(): Chart
    {
        $chartData = [];

        $current = $this->catalog->getCurrentDateTime()->subDays(7);
        $until = $this->catalog->getCurrentDateTime();

        do {
            $start = Carbon::createFromTimeString(
                sprintf('%s-%s-%s 0:0:0', $current->year, $current->month, $current->day)
            );
            $end = Carbon::createFromTimeString(
                sprintf('%s-%s-%s 23:59:59', $current->year, $current->month, $current->day)
            );
            $chartData[$current->format('Y-m-d')] = $this->getStoredBytesSum($start, $end);
            $current->addDay();
        } while ($current->lte($until));

        return new Chart([
                'type' => 'bar',
                'name' => 'chart_last_week_stored_bytes',
                'data' => $chartData,
                'uniformize_data' => true
            ]);
    }

    /**
     * @return Chart
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getLastWeekStoredFilesChart(): Chart
    {
        $chartData = [];

        $current = $this->catalog->getCurrentDateTime()->subDays(7);
        $until = $this->catalog->getCurrentDateTime();

        do {
            $start = Carbon::createFromTimeString(
                sprintf('%s-%s-%s 0:0:0', $current->year, $current->month, $current->day)
            );
            $end = Carbon::createFromTimeString(
                sprintf('%s-%s-%s 23:59:59', $current->year, $current->month, $current->day)
            );
            $chartData[$current->format('Y-m-d')] = $this->getStoredFilesSum($start, $end);
            $current->addDay();
        } while ($current->lte($until));

        return new Chart([
                'type' => 'bar',
                'name' => 'chart_last_week_stored_files',
                'data' => $chartData,
                'uniformize_data' => true
            ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function findWithFilters(Request $request): array
    {
        $qb = $this->createQueryBuilder('j');

        // Job status filter
        $jobStatus = [
            [''],
            ['R'],
            ['F','S','M','m','s','j','c','d','t','p','C'],
            ['T'],
            ['E'],
            ['f'],
            ['A']
        ];

        $qb
            ->select('j', 's', 'p', 'c')
            //->from(Job::class, 'j')
            ->leftJoin('j.pool', 'p')
            ->leftJoin('j.status', 's')
            ->leftJoin('j.client', 'c')
        ;

        $filterJobStatus = $request->query->get('filter_jobstatus') ?? '0';
        $filterJobLevel = $request->query->get('filter_joblevel') ?? '0';
        $filterJobType = $request->query->get('filter_jobtype') ?? '0';
        $filterClient = $request->query->get('filter_clientid') ?? '0';
        $filterPool = $request->query->get('filter_pool') ?? '0';
        $filterStartTime = $request->query->get('filter_starttime');
        $filterEndTime = $request->query->get('filter_endtime');
        $filterOrderBy = $request->query->get('filter_orderby') ?? 'j.id';
        $filterOrderDirection = $request->query->get('filter_orderby_direction') ?? 'DESC';

        if ($filterJobStatus !== '0') {
            $qb
                ->andWhere('s.status IN(:status)')
                ->setParameter('status', array_values($jobStatus[$filterJobStatus]));
        }

        if ($filterJobLevel !== '0') {
            $qb
                ->andWhere('j.level = :level')
                ->setParameter('level', $filterJobLevel);
        }

        if ($filterJobType !== '0') {
            $qb
                ->andWhere('j.type = :type')
                ->setParameter('type', $filterJobType);
        }

        if ($filterClient !== '0') {
            $qb
                ->andWhere('j.clientid = :clientid')
                ->setParameter('clientid', (int) $filterClient);
        }

        if ($filterPool !== '0') {
            $qb
                ->andWhere('j.poolid = :poolid')
                ->setParameter('poolid', (int) $filterPool);
        }

        if ($filterStartTime) {
            $qb
                ->andWhere('j.starttime >= :starttime')
                ->setParameter('starttime', $filterStartTime);
        }

        if ($filterEndTime) {
            $qb
                ->andWhere('j.starttime <= :endtime')
                ->setParameter('endtime', $filterEndTime);
        }

        return [
            'filter_jobstatus' => $filterJobStatus,
            'filter_joblevel' => $filterJobLevel,
            'filter_jobtype' => $filterJobType,
            'filter_clientid' => $filterClient,
            'filter_pool' => $filterPool,
            'filter_starttime' => $filterStartTime,
            'filter_endtime' => $filterEndTime,
            'filter_orderby' => $filterOrderBy,
            'filter_orderby_direction' => $filterOrderDirection,
            'jobs' => $qb
                ->orderBy($filterOrderBy, $filterOrderDirection)
            ];
    }

    /**
     * Return the sum of bytes  of all Bacula backup jobs
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalStoredBytes(): int
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return (int) $queryBuilder
            ->select('SUM(j.jobbytes)')
            ->where('j.type = :type')
            ->setParameter('type', 'B')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return the sum of bytes of all Bacula backup jobs
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalStoredFiles(): int
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return (int) $queryBuilder
            ->select('SUM(j.jobfiles)')
            ->where('j.type = :type')
            ->setParameter('type', 'B')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
