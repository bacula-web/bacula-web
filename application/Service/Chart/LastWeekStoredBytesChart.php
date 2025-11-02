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
use App\Entity\Bacula\Repository\VersionRepository;
use Carbon\CarbonPeriod;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Create a Bar chart with last 7 days stored bytes
 */
class LastWeekStoredBytesChart extends BarChart
{
    private JobRepository $jobRepository;
    private VersionRepository $catalog;

    /**
     * @param ChartBuilderInterface $chartBuilder
     * @param JobRepository $jobRepository
     * @param VersionRepository $catalog
     */
    public function __construct(
        ChartBuilderInterface $chartBuilder,
        JobRepository $jobRepository,
        VersionRepository $catalog
    ) {
        parent::__construct($chartBuilder);

        $this->jobRepository = $jobRepository;
        $this->catalog = $catalog;
    }

    /**
     * @return Chart
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChart(): Chart
    {
        $current = $this->catalog->getCurrentDateTime()->subDays(7);
        $until = $this->catalog->getCurrentDateTime();

        $period = CarbonPeriod::create($current->startOfDay(), '1 day', $until->endOfDay());

        $chartLabels = [];
        $chartData = [];

        foreach ($period as $date) {
            $chartLabels[] = $date->format('m-d');
            $chartData[] = $this->jobRepository->getStoredBytesSum(
                $date->copy()->startOfDay()->toDateTime(),
                $date->copy()->endOfDay()->toDateTime()
            );
        }

        $this->chart->setData([
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => 'Last 7 days stored bytes',
                    'data' => $chartData,
                    'backgroundColor' => 'rgb(105,105,105)',
                ]
            ]
        ]);

        return $this->chart;
    }
}
