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

namespace App\Service\Chart;

use App\Entity\Bacula\Job;
use App\Entity\Bacula\Pool;
use App\Entity\Bacula\Repository\JobRepository;
use Core\Graph\Chart;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StatsChart {

    private ObjectManager $manager;
    /**
     * @var JobRepository
     */
    private $jobRepository;
    /**
     * @var \Doctrine\Persistence\ObjectRepository
     */
    private $poolRepository;
    private UrlGeneratorInterface $urlGenerator;


    public function __construct(
        ObjectManager $entityManager,
    )
    {
        $this->jobRepository = $entityManager->getRepository(Job::class);
        $this->poolRepository = $entityManager->getRepository(Pool::class);
    }

    public function getLastJobsChart(DateTimeInterface $from, DateTimeInterface $to, string $link): Chart
    {
        $jobsStatus = ['Running', 'Completed', 'Completed_with_errors', 'Waiting', 'Failed', 'Canceled'];
        $jobsStatusData = [];

        foreach ($jobsStatus as $status) {
            $jobsCount = $this->jobRepository->countJobsByStatus(strtolower($status), $from, $to);
            $jobsStatusData[] = [$status, $jobsCount];
        }

        return new Chart([
            'type' => 'pie',
            'name' => 'chart_lastjobs',
            'data' => $jobsStatusData,
            'linked_report' => $link,
        ]);
    }

    public function getPoolsUsageChart(string $link): Chart
    {
        $queryBuilder = $this->poolRepository->createQueryBuilder('p');

        $queryBuilder
            ->select('p.id, p.name, SUM(p.numvols) AS numvols')
            ->orderBy('p.numvols', 'DESC')
            ->setMaxResults(9)
            ->groupBy('p.id');

        $pools = $queryBuilder
            ->getQuery()
            ->getResult();

        foreach ($pools as $pool) {
            $chartData[] = [
                $pool['name'], $pool['numvols'] ?? null
            ];
        }

        return new Chart([
            'type' => 'pie',
            'name' => 'chart_pools_usage',
            'data' => $chartData,
            'linked_report' => $link
        ]);
    }

    /**
     * @return Chart
     * @throws \DateInvalidOperationException
     * @throws \DateMalformedPeriodStringException
     */
    public function getStoredBytesChart(): Chart
    {
        $daysStoredBytes = [];

        $to = new DateTimeImmutable('now');
        $interval = new DateInterval("P7D");
        $from = $to->sub($interval);

        $interval = new DateInterval('P1D');
        $period = new DatePeriod($from, $interval, $to);

        foreach ($period as $day) {
            $daysStoredBytes[] = [
                $day->format('m-d'),
                $this->jobRepository->getTotalStoredBytes(
                    $day->setTime(0, 0, 0),
                    $day->setTime(23, 59, 59))
            ];
        }

        return new Chart([
                'type' => 'bar',
                'name' => 'chart_storedbytes',
                'data' => $daysStoredBytes,
                'ylabel' => 'Stored Bytes',
                'uniformize_data' => true
            ]
        );
    }

    /**
     * @return Chart
     * @throws \DateInvalidOperationException
     * @throws \DateMalformedPeriodStringException
     */
    public function getStoredFilesChart(): Chart
    {
        $daysStoredBytes = [];

        $to = new DateTimeImmutable('now');
        $interval = new DateInterval("P7D");
        $from = $to->sub($interval);

        $interval = new DateInterval('P1D');
        $period = new DatePeriod($from, $interval, $to);

        foreach ($period as $day) {
            $daysStoredFiles[] = [
                $day->format('m-d'),
                $this->jobRepository->getTotalStoredFiles(
                    $day->setTime(0, 0, 0),
                    $day->setTime(23, 59, 59))
            ];
        }

        return new Chart([
                'type' => 'bar',
                'name' => 'chart_storedfiles',
                'data' => $daysStoredFiles,
                'ylabel' => 'Stored Bytes',
                'uniformize_data' => true
            ]
        );
    }
}
