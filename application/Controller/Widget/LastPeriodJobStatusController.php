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

namespace App\Controller\Widget;

use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\VersionRepository;
use Carbon\Carbon;
use Core\Exception\AppException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provide various jobs statistics of the chosen period of time
 */

class LastPeriodJobStatusController extends AbstractController
{
    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @var VersionRepository
     */
    private VersionRepository $catalog;

    /**
     * @param JobRepository $jobRepository
     * @param VersionRepository $catalog
     */
    public function __construct(JobRepository $jobRepository, VersionRepository $catalog)
    {
        $this->jobRepository = $jobRepository;
        $this->catalog = $catalog;
    }

    /**
     * @param string $period
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws AppException
     */
    public function index(string $period = 'last_day'): Response
    {
        $periodsList = [
            ['id' => 'last_day', 'label' => 'Last 24 hours'],
            ['id' => 'last_week', 'label' => 'Last week'],
            ['id' => 'last_month', 'label' => 'Last month'],
            ['id' => 'since_bot', 'label' => 'Since BOT']
        ];

        $to = $this->catalog->getCurrentDateTime();
        $from = null;

        switch ($period) {
            case 'last_day':
                $from = $this->catalog->getCurrentDateTime()->subDay();
                break;
            case 'last_week':
                $from = $this->catalog->getCurrentDateTime()->subDays(7);
                break;
            case 'last_month':
                $from = $this->catalog->getCurrentDateTime()->subMonth();
                break;
            case 'since_bot':
                $from = new Carbon('Jan 1st 1970 UTC');
                break;
        }

        $jobStatusesChart = $this->jobRepository->getJobStatusChart($from, $to, $this->generateUrl('jobs'));

        return $this->render('partials/widget/last_period_job_status.html.twig', [
            'literal_period' => $from->toFormattedDayDateString() . ' to ' . $to->toFormattedDayDateString(),
            'period_list' => $periodsList,
            'last_jobs_chart_id' => $jobStatusesChart->getName(),
            'last_jobs_chart' => $jobStatusesChart->render(),
            'running_jobs' => $this->jobRepository->countJobsByStatus('running', $from, $to),
            'completed_jobs' => $this->jobRepository->countJobsByStatus('completed', $from, $to),
            'completed_with_errors_jobs' => $this->jobRepository
                ->countJobsByStatus('completed_with_errors', $from, $to),
            'waiting_jobs' => $this->jobRepository->countJobsByStatus('waiting', $from, $to),
            'failed_jobs' => $this->jobRepository->countJobsByStatus('failed', $from, $to),
            'canceled_jobs' => $this->jobRepository->countJobsByStatus('canceled', $from, $to),
            'incr_jobs' => $this->jobRepository->countJobsByLevel('I', $from, $to),
            'diff_jobs' => $this->jobRepository->countJobsByLevel('D', $from, $to),
            'full_jobs' => $this->jobRepository->countJobsByLevel('F', $from, $to),
            'bytes_last' => $this->jobRepository->getStoredBytesSum($from, $to),
            'files_last' => $this->jobRepository->getStoredFilesSum($from, $to),
            'period' => $period
        ]);
    }
}
