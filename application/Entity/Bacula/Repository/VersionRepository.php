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

use App\Entity\Bacula\Version;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Version|null find($id, $lockMode = null, $lockVersion = null)
 * @method Version|null findOneBy(array $criteria, array $orderBy = null)
 * @method Version[] findAll()
 * @method Version[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Version::class);
    }

    /**
     * Return Bacula catalog version
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCatalogVersion(): int
    {
        $queryBuilder = $this->createQueryBuilder('v');
        return $queryBuilder
            ->select('v')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
