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
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[] findAll()
 * @method Job[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
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
     * Return list of used Bacula jobs level
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
            ->setParameter('end', $to);

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
            ->setParameter('end', $to);

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
}
