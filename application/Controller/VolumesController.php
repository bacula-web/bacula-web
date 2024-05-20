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

use App\Entity\Bacula\Job;
use App\Entity\Bacula\JobMedia;
use App\Entity\Bacula\Repository\PoolRepository;
use App\Entity\Bacula\Repository\VolumeRepository;
use App\Entity\Bacula\Volume;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Volumes report controller
 */
class VolumesController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        /**
         * TODO: check if using the repository will be enough
         */
        $this->entityManager = $doctrine->getManager('bacula');
    }

    /**
     * @Route("/volumes", name="volumes")
     *
     * @param Request $request
     * @param PoolRepository $poolRepository
     * @param VolumeRepository $volumeRepository
     * @param PaginatorInterface $paginator
     * @param ParameterBagInterface $parameters
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(
        Request $request,
        PoolRepository $poolRepository,
        VolumeRepository $volumeRepository,
        PaginatorInterface $paginator,
        ParameterBagInterface $parameters
    ): Response {
        $poolId = 0;

        // Order by
        $orderByOptions = [
            'name' => 'Name',
            'id' => 'Id',
            'volbytes' => 'Bytes',
            'voljobs' => 'Jobs'
        ];

        $inChangerChecked = '';
        $orderByField = 'name';
        $orderByDirection = 'ASC';
        $orderByChecked = '';

        $pools = $poolRepository->getPools();

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('v', 'p')
            ->from(Volume::class, 'v')
            ->leftJoin('v.pool', 'p')
        ;

        $postData = $request->request->all();

        /**
         * TODO: refactor using Symfony Form component
         */
        if ($request->getMethod() === 'POST') {
            /**
             * TODO: add missing validation
             */
            $poolId = $postData['filter_pool_id'] ?? 0;

            if ($poolId !== '0') {
                $queryBuilder
                    ->where('v.poolId = :poolid')
                    ->setParameter('poolid', $poolId);
            }

            $orderByField = isset($postData['filter_orderby']) ? $postData['filter_orderby'] : 'name';

            if (isset($postData['filter_orderby_asc'])) {
                $orderByChecked = 'checked';
            }

            $orderByDirection = $postData['filter_orderby_asc'] ?? 'DESC';

            if (isset($postData['filter_inchanger'])) {
                $queryBuilder
                    ->andWhere('v.inchanger = :inchanger')
                    ->setParameter('inchanger', 1);
            }
            $inChangerChecked = isset($postData['filter_inchanger']) ? 'checked' : '';
        }

        $queryBuilder->orderBy('v.' . $orderByField, $orderByDirection);

        $volumes = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $parameters->get('app.rows_per_page')
        );

        return $this->render('pages/volumes.html.twig', [
                'pagination' => $volumes,
                'datetime_format' => $this->getParameter('app.datetime_format'),
                'datetime_format_short' => $this->getParameter('app.datetime_format_short'),
                'volumes_total_bytes' => $volumeRepository->getStoredSize(),
                'pools' => $pools,
                'pool_id' => $poolId,
                'orderby_options' => $orderByOptions,
                'orderby_selected' => $orderByField,
                'orderby_asc_checked' => $orderByChecked,
                'inchanger_checked' => $inChangerChecked
            ]);
    }

    /**
     * @Route("/volume/{id}", name="volume_detail")
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        /**
         * List jobs for a specific volume
         */
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $query = $queryBuilder
            ->select('v', 'j.id', 'j.name', 'j.type')
            ->distinct()
            ->from(Volume::class, 'v')
            ->innerJoin(JobMedia::class, 'jm', Join::WITH, 'v.id = jm.mediaid')
            ->innerJoin(Job::class, 'j', Join::WITH, 'jm.jobid = j.id')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
        ;
        $jobs = $query->getArrayResult();

        $volume = $this->entityManager->getRepository(Volume::class)->find($id);

        return $this->render('pages/volume.html.twig', [
                'volume' => $volume,
                'jobs' => $jobs
            ]);
    }
}
