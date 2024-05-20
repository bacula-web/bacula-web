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

use App\Entity\Bacula\Repository\VolumeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Last used volumes widget controller list the last 10 used volumes.
 * Disabled Volumes are not included in the list.
 */
class LastUsedVolumesController extends AbstractController
{
    /**
     * @param VolumeRepository $volumeRepository
     * @return Response
     */
    public function index(VolumeRepository $volumeRepository): Response
    {
        $queryBuilder = $volumeRepository->createQueryBuilder('v');

        $volumes = $queryBuilder
            ->select('v', 'p')
            ->join('v.pool', 'p')
            ->setMaxResults(10)
            ->where('v.lastwritten IS NOT NULL')
            ->andWhere('v.status != :status')
            ->setParameter('status', 'Disabled')
            ->orderBy('v.lastwritten', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render(
            'partials/widget/last_used_volumes.html.twig',
            compact('volumes')
        );
    }
}
