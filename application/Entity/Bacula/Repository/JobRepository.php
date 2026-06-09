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

namespace App\Entity\Bacula\Repository;

use App\Entity\Bacula\Job;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @param int $jobId
     * @return Job|null
     * @throws NonUniqueResultException
     */
    public function getJobWithLogs(int $jobId): ?Job
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return $queryBuilder
            ->select('j', 's', 'l')
            ->leftJoin('j.status', 's')
            ->leftJoin('j.logs', 'l')
            ->orderBy('l.time')
            ->where('j.id = :id')
            ->setParameter('id', $jobId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
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
            'A' => 'Data',
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
     * @return array<string, string>
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
            'g' => 'Migration',
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
     * @param DateTime $from
     * @param DateTime $to
     * @param string|null $jobName
     * @param int|null $clientId
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalStoredBytes(DateTimeInterface $from, DateTimeInterface $to, ?string $jobName = null, ?int $clientId = null): int
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

    public function getTotalStoredFiles(DateTimeInterface $from, DateTimeInterface $to, ?string $jobName = null, ?int $clientId = null): int
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
     * @param int $clientId
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @return mixed
     */
    public function getClientJobs(int $clientId, DateTimeInterface $from, DateTimeInterface $to)
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
     * @param string $status
     * @param DateTimeInterface|null $start
     * @param DateTimeInterface|null $end
     * @return int
     */
    public function countJobsByStatus(string $status, DateTimeInterface $start = null, DateTimeInterface $end  = null)
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
                    ->setParameter('status', ['F', 'S', 'M', 'm', 's', 'j', 'c', 'd', 't', 'p', 'C']);
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

        if ($start && $end) {
            $queryBuilder
                ->andWhere('j.endtime BETWEEN :from AND :to')
                ->setParameter('from', $start)
                ->setParameter('to', $end);
        }

        return (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param string $level
     * @return void
     */
    public function countJobsByLevel(DateTimeInterface $from, DateTimeInterface $to, string $level): int
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
     * Return backup and restore job statistics.
     */
    public function getStatisticsPerType(): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return $queryBuilder
            ->select('COUNT(j.id) AS jobs_count, SUM(j.jobfiles) AS jobs_files, SUM(j.jobbytes) AS jobs_bytes')
            ->where('j.type IN(:types)')
            ->setParameter('types', ['B', 'R'])
            // ->groupBy('j.name')
            ->groupBy('j.type')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return a list of the top 10 biggest (job bytes) backup jobs.
     */
    public function getBiggestJobs(): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        return $queryBuilder
            ->select('j.name,
             j.type,
             j.jobfiles AS jobs_files,
             j.jobbytes AS jobs_bytes')
            ->where('j.type IN(:types)')
            ->setParameter('types', ['B', 'R'])
            ->setMaxResults(10)
            ->orderBy('j.jobbytes', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return an array which contains stored bytes and files of completed backup jobs of each day of the week.
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
            'Saturday' => ['job_bytes' => 0, 'job_files' => 0],
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
     * @return mixed
     */
    public function getJobNameStats()
    {
        $dql = "SELECT count(j.id) AS jobscount, sum(j.jobfiles) AS jobfiles, j.type, sum(j.jobbytes) AS jobbytes, j.name AS jobname FROM App\Entity\Bacula\Job AS j WHERE j.type IN ('B','R') GROUP BY j.name, j.type";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
