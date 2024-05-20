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

namespace App\Controller;

use App\Entity\Bacula\Repository\ClientRepository;
use App\Entity\Bacula\Repository\FileSetRepository;
use App\Entity\Bacula\Repository\JobRepository;
use App\Entity\Bacula\Repository\PoolRepository;
use App\Entity\Bacula\Repository\VersionRepository;
use App\Entity\Bacula\Repository\VolumeRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for Director report page
 */
class DirectorController extends AbstractController
{
    /**
     * @var PoolRepository
     */
    private PoolRepository $poolRepository;

    /**
     * @var FileSetRepository
     */
    private FileSetRepository $fileSetRepository;

    /**
     * @var VolumeRepository
     */
    private VolumeRepository $volumeRepository;

    /**
     * @var JobRepository
     */
    private JobRepository $jobRepository;

    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * @param PoolRepository $poolRepository
     * @param FileSetRepository $fileSetRepository
     * @param VolumeRepository $volumeRepository
     * @param JobRepository $jobRepository
     * @param ClientRepository $clientRepository
     */
    public function __construct(
        PoolRepository $poolRepository,
        FileSetRepository $fileSetRepository,
        VolumeRepository $volumeRepository,
        JobRepository $jobRepository,
        ClientRepository $clientRepository
    ) {
        $this->poolRepository = $poolRepository;
        $this->fileSetRepository = $fileSetRepository;
        $this->volumeRepository = $volumeRepository;
        $this->jobRepository = $jobRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/directors", name="directors")
     *
     * @param VersionRepository $catalog
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function index(
        VersionRepository $catalog
    ): Response {
        /**
         * Get Bacula directors list once Bacula directors are stored in user settings
         * and multi-tenancy is completed
         * Currently, there's only one Bacula director defined in .env in BACULA_DATABASE_URL
         */
        $directors[] = [
            /**
             * TODO: Use label from user settings once implemented
             */
            'label' => 'Backup server',
            'clients' => $this->clientRepository->count([]),
            'jobs' => $this->jobRepository->count([]),
            'totalbytes' => $this->jobRepository->getTotalStoredBytes(),
            'totalfiles' => $this->jobRepository->getTotalStoredFiles(),
            'dbsize' => $catalog->getDatabaseSize(),
            'volumes' => $this->volumeRepository->count([]),
            'volumesize' => $this->volumeRepository->getStoredSize(),
            'pools' => $this->poolRepository->count([]),
            'filesets' => $this->fileSetRepository->count([]),
            /**
             * TODO: Use description from user settings once implemented
             *
             * Description should look like
             * Bacula catalog on host $host, database: $db_name ($db_type) with user $db_user
             */
            'description' => 'Bacula backup server'
        ];

        return $this->render('pages/directors.html.twig', [
            'directors' => $directors,
            'directors_count' => count($directors)
        ]);
    }
}
