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
use Core\Exception\AppException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Last 7 days backed-up files chart controller
 */
class LastWeekStoredFilesController extends AbstractController
{
    /**
     * @param JobRepository $jobRepository
     * @return Response
     * @throws AppException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(JobRepository $jobRepository): Response
    {
        /**
         * give chart_last_week_stored_files nane to chart to avoid issues
         */
        $storedFilesChart = $jobRepository->getLastWeekStoredFilesChart();

        return $this->render('partials/widget/last_week_stored_files.html.twig', [
            'stored_files_chart' => $storedFilesChart->render(),
            'stored_files_chart_id' => $storedFilesChart->getName()
        ]);
    }
}
