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

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class FileBeforeV11Repository extends EntityRepository
{
    /**
     * @param int $jobId
     * @param string|null $filename
     * @return QueryBuilder
     * @author Davide Franco
     *
     * @author Gabriele Orlando
     */
    public function getFilesFromJobId(int $jobId, string $filename = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('f');

        $queryBuilder
            ->select('f, p, fn')
            ->join('f.path', 'p')
            ->join('f.filename', 'fn') // <- update annotations on Filename table
            ->where('f.jobid = :jobId')
            ->setParameter('jobId', $jobId);

        if (!empty($filename)) {
            $queryBuilder
                ->andWhere('fn.name LIKE :filename')
                ->setParameter('filename', '%' . $filename . '%');
        }

        return $queryBuilder;
    }

    /**
     * @param int $jobId
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countFilesPerJob(int $jobId): int
    {
        $queryBuilder = $this->createQueryBuilder('f');

        $query = $queryBuilder
            ->select('count(f.id)')
            ->where('f.jobid = :jobid')
            ->setParameter('jobid', $jobId)
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }
}
