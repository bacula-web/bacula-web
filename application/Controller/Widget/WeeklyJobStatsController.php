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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provide backup job statistics for each day of the week
 */
class WeeklyJobStatsController extends AbstractController
{
    /**
     * Return an array which contains stored bytes and files of completed backup jobs of each day of the week
     *
     * @param JobRepository $jobRepository
     * @return Response
     */
    public function index(JobRepository $jobRepository): Response
    {
        $jobStats = $jobRepository->getWeeklyJobsStats();

        return $this->render('partials/widget/weekly_job_stats.html.twig', [
            'weekly_jobs_stats' => $jobStats,
        ]);
    }
}
