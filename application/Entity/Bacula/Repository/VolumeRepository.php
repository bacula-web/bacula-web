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

use App\Entity\Bacula\Volume;
use App\Entity\Bacula\VolumeSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Volume|null find($id, $lockMode = null, $lockVersion = null)
 * @method Volume|null findOneBy(array $criteria, array $orderBy = null)
 * @method Volume[] findAll()
 * @method Volume[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolumeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volume::class);
    }

    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getStoredSize(): int
    {
        $queryBuilder = $this->createQueryBuilder('v');

        return (int) $queryBuilder
            ->select('SUM(v.volbytes)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findPaginated(VolumeSearch $search): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('v');

        $orderBy = 'v.name';
        if (!is_null($search->getOrderBy())){
            $orderBy = 'v.' . $search->getOrderBy();
        }

        $queryBuilder
            ->select('v', 'p')
            ->join('v.pool', 'p')
            ->orderBy( $orderBy, $search->getOrderDirection() ?? 'DESC');

        if (!is_null($search->getPool())) {
            $queryBuilder
                ->andWhere('v.pool = :pool')
                ->setParameter('pool', $search->getPool());
        }

        if ($search->isInChanger()) {
            $queryBuilder
                ->andWhere('v.inchanger = 1');
        }

        return $queryBuilder;
    }
}
