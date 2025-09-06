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
use Doctrine\ORM\QueryBuilder;

class FileRepository extends EntityRepository
{
    public function getFilesFromJobId(int $jobId, string $filename = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('f');

        $queryBuilder
            ->select('f')
            ->join('f.job', 'j')
            ->join('f.path', 'p')
            ->where('f.jobid = :jobId')
            ->setParameter('jobId', $jobId);

        /*$queryBuilder
            ->select('f', 'p', 'fn')
            ->from(File::class, 'f')
            ->join('f.path', 'p')
            ->join('f.filename', 'fn') // <- update annotations on Filename table
            ->where('f.jobid = :jobId')
            ->setParameter('jobId', $jobId);
        */

        if (!empty($filename)) {
            $queryBuilder
                ->andWhere('fn.name LIKE :filename')
                ->setParameter('filename', '%' . $filename . '%');
        }

        return $queryBuilder;
    }
}
