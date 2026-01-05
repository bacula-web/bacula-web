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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

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
}
