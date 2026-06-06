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
}
