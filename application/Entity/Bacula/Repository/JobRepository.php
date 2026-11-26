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
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class JobRepository extends EntityRepository
{
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
     * @return array<string,string>
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
     * @param string $status
     * @param int|null $start
     * @param int|null $end
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countJobsByStatus(string $status, int $start = null, int $end = null): int
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $queryBuilder->select('COUNT(j.id)');

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
            $startDate = Carbon::createFromTimestamp($start);
            $endDate = Carbon::createFromTimestamp($end);

            $queryBuilder
                ->andWhere('j.endtime BETWEEN :start AND :end')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate);
        }

        return (int) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $level
     * @param int|null $start
     * @param int|null $end
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countJobsByLevel(string $level, int $start = null, int $end = null): int
    {
        $queryBuilder = $this->createQueryBuilder('j');
        $queryBuilder->select('COUNT(j.id)');

        return (int) $queryBuilder
            ->andWhere('j.level = :level')
            ->setParameter('level', $level)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
