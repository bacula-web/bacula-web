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

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class VolumeRepository extends EntityRepository
{
    /**
     * Return the total of all volumes bytes
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalBytes(): int
    {
        return (int)$this->createQueryBuilder('v')
            ->select('SUM(v.volbytes)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLastUsedVolumes()
    {
        $queryBuilder = $this->createQueryBuilder('v');

        return $queryBuilder
            ->select('v', 'p')
            ->join('v.pool', 'p')
            ->setMaxResults(10)
            ->where('v.lastwritten IS NOT NULL')
            ->andWhere('v.status != :status')
            ->setParameter('status', 'Disabled')
            ->orderBy('v.lastwritten', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
