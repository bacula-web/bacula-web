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

use App\Entity\Bacula\FilePriorV11;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FilePriorV11|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilePriorV11|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilePriorV11[] findAll()
 * @method FilePriorV11[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilePriorV11Repository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilePriorV11::class);
    }

    /**
     * @param int $jobId
     * @param string|null $filename
     * @return array
     */
    public function findFilesByJobid(int $jobId, string $filename = null): array
    {
        $queryBuilder = $this->createQueryBuilder('f');

        $queryBuilder
            ->select('f')
            ->join('f.job', 'j')
            ->join('f.path', 'p')
            ->join('f.filename', 'fn') // <- update annotations on Filename table
            ->where('f.jobid = :jobId')
            ->setParameter('jobId', $jobId);

        if ($filename) {
            $queryBuilder
                ->andWhere('fn.name LIKE :filename')
                ->setParameter('filename', '%' . $filename . '%');
        }

        return [
            'files' => $queryBuilder
        ];
    }
}
