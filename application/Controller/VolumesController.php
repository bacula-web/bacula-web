<?php

/**
 * Copyright (C) 2010-present Davide Franco.
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
use App\Entity\Bacula\VolumeSearch;
use App\Form\VolumeSearchType;
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
 * Volumes report controller.
 */
class VolumesController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        /*
         * TODO: check if using the repository will be enough
         */
        $this->entityManager = $doctrine->getManager('bacula');
    }

    /**
     * @Route("/volumes", name="volumes")
     *
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
        $volumeSearch = new VolumeSearch($poolRepository);

        $form = $this->createForm(VolumeSearchType::class, $volumeSearch);
        $form->handleRequest($request);

        $volumes = $paginator->paginate(
            $volumeRepository->findPaginated($volumeSearch),
            $request->query->getInt('page', 1),
            $parameters->get('app.rows_per_page')
        );

        return $this->render('pages/volumes.html.twig', [
            'pagination' => $volumes,
            'volumes_total_bytes' => $volumeRepository->getStoredSize(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/volume/{id}", name="volume_detail")
     */
    public function show(int $id): Response
    {
        /**
         * List jobs for a specific volume.
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
            'jobs' => $jobs,
        ]);
    }
}
