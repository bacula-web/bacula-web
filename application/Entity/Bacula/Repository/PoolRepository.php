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

use App\Entity\Bacula\Pool;
use App\Service\Chart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @method Pool|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pool|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pool[] findAll()
 * @method Pool[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PoolRepository extends ServiceEntityRepository
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameters;

    /**
     * @param ManagerRegistry $registry
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ManagerRegistry $registry, ParameterBagInterface $parameters)
    {
        parent::__construct($registry, Pool::class);

        $this->parameters = $parameters;
    }

    /**
     * Return the list of Bacula pools, optionally omitting empty pools
     * if "hide_empty_pools is set to true in user parameters
     *
     * @return array
     */
    public function getPools(): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('p.id, p.name, p.numvols');

        if ($this->parameters->get('app.hide_empty_pools')) {
            $queryBuilder->where('p.numvols > 0');
        }

        return $queryBuilder
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Return 9 biggest pools based on volumes usage.
     * Empty pools are not displayed in the pie chart.
     *
     * @param string $pageRoute
     * @return Chart
     */
    public function getPoolsStatistics(string $pageRoute): Chart
    {
        $chartData = [];

        $queryBuilder = $this->createQueryBuilder('p');

        $queryBuilder
            ->select('p.id, p.name, SUM(p.numvols) AS numvols')
            ->orderBy('p.numvols', 'DESC')
            ->setMaxResults(9)
            ->groupBy('p.id');

        if ($this->parameters->get('app.hide_empty_pools')) {
            $queryBuilder->where('p.numvols > 0');
        }

        $pools = $queryBuilder
            ->getQuery()
            ->getResult();

        foreach ($pools as $pool) {
            $chartData[$pool['name']] = $pool['numvols'] ?? 0;
        }

        return new Chart([
            'type' => 'pie',
            'data' => $chartData,
            'name' => 'chart_pools_volume_usage',
            'linked_report' => $pageRoute
        ]);
    }
}
