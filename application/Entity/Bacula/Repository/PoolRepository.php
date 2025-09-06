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

use App\Entity\Bacula\Pool;
use Doctrine\ORM\EntityRepository;

class PoolRepository extends EntityRepository
{
    /**
     * @param bool $hideEmpty
     * @return Pool[]
     */
    public function getPools(bool $hideEmpty = true): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.numvols')
            ->orderBy('p.name', 'ASC');

        if ($hideEmpty) {
            $queryBuilder->andWhere('p.numvols > 0');
        }

        return $queryBuilder
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param bool $hideEmpty
     * @return array
     */
    public function getPoolsList(bool $hideEmpty = true): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p.id, p.name');

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
