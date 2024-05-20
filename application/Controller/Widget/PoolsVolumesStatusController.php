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

use App\Entity\Bacula\Repository\PoolRepository;
use Core\Exception\AppException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provide 9 biggest pools with volumes usage in a pie chart
 */
class PoolsVolumesStatusController extends AbstractController
{
    /**
     * @var PoolRepository
     */
    private PoolRepository $poolRepository;

    /**
     * @param PoolRepository $poolRepository
     */
    public function __construct(PoolRepository $poolRepository)
    {
        $this->poolRepository = $poolRepository;
    }

    /**
     * @return Response
     * @throws AppException
     */
    public function index(): Response
    {
        $poolsChart = $this->poolRepository->getPoolsStatistics($this->generateUrl('pools'));

        return $this->render('partials/widget/pools_volumes_status.html.twig', [
            'pools_usage_chart_id' => $poolsChart->getName(),
            'pools_usage_chart' => $poolsChart->render()
        ]);
    }
}
