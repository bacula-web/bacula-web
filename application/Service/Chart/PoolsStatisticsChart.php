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

namespace App\Service\Chart;

use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\PoolRepository;
use Carbon\Carbon;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Create a Pie chart with amount of volumes per pool (limited 9 biggest pools)
 */
class PoolsStatisticsChart extends PieChart
{
    /**
     * @var PoolRepository
     */
    private PoolRepository $poolRepository;

    /**
     * @param ChartBuilderInterface $chartBuilder
     * @param PoolRepository $poolRepository
     */
    public function __construct(ChartBuilderInterface $chartBuilder, PoolRepository $poolRepository)
    {
        parent::__construct($chartBuilder);

        $this->poolRepository = $poolRepository;
    }

    /**
     * @return Chart
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChart(): Chart
    {
        $poolsStats = $this->poolRepository->getPoolsStatistics('there');

        $chartLabels = array_keys($poolsStats);
        $chartData = array_values($poolsStats);

        $this->chart->setData([
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'data' => $chartData,
                    'backgroundColor' => [
                        'rgb(105,105,105)',
                        'rgb(50,205,50)',
                        'rgb(255,215,0)',
                        'rgb(65,105,225)',
                        'rgb(255,0,0)',
                        'rgb(255,140,0)'
                    ],
                    'hoverOffset' => '4'
                ]
            ]
        ]);

        $this->chart->setOptions([
            'layout' => [
                'padding' => [
                    'top' => '0',
                    'left' => '100',
                    'right' => '100',
                    'bottom' => '0',
                ]
            ],
            'plugins' => [
                'legend' => ['position' => 'right'],
                'title' => ['display' => true, 'text' => 'Volumes per pool statistics'],
            ],
            'aspectRatio' => 1.5,
        ]);

        return $this->chart;
    }
}
