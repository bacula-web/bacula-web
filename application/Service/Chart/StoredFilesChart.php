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
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Create a Bar chart with last 7 days stored files
 */
class StoredFilesChart extends BarChart
{
    private JobRepository $jobRepository;
    private VersionRepository $catalog;

    /**
     * @param JobRepository $jobRepository
     * @param VersionRepository $catalog
     * @param ChartBuilderInterface $chartBuilder
     */
    public function __construct(
        JobRepository $jobRepository,
        VersionRepository $catalog,
        ChartBuilderInterface $chartBuilder
    ) {
        parent::__construct($chartBuilder);

        $this->jobRepository = $jobRepository;
        $this->catalog = $catalog;
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param int|null $clientId
     * @param string|null $jobName
     * @return Chart
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getChart(Carbon $from, Carbon $to, ?int $clientId = null, ?string $jobName = null): Chart
    {
        $period = CarbonPeriod::create($from->startOfDay(), '1 day', $to->endOfDay());

        $chartLabels = [];
        $chartData = [];

        foreach ($period as $date) {
            $chartLabels[] = $date->format('m-d');
            $chartData[] = $this->jobRepository->getStoredFilesSum(
                $date->copy()->startOfDay()->toDateTime(),
                $date->copy()->endOfDay()->toDateTime(),
                $jobName,
                $clientId
            );
        }

        $this->chart->setData([
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => "Stored files",
                    'data' => $chartData,
                    'backgroundColor' => 'rgb(105,105,105)',
                ]
            ]
        ]);

        return $this->chart;
    }
}
