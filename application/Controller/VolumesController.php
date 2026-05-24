<?php

/**
 * Copyright (C) 2017-present Davide Franco
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

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Bacula\Job;
use App\Entity\Bacula\JobMedia;
use App\Entity\Bacula\Volume;
use App\Form\VolumeSearchType;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use VolumeSearch;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VolumesController extends AbstractController
{
    private ObjectManager $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager('bacula');
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route("/volumes", name: "volumes")]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $volumeSearch = new VolumeSearch();
        $form = $this->createForm(VolumeSearchType::class, $volumeSearch);
        $form->handleRequest($request);

        $volumeQueryBuilder = $this->entityManager->createQueryBuilder();
        $volumeQueryBuilder->select('v')->from(Volume::class, 'v')->orderBy('v.name', 'DESC');

        if($volumeSearch->getPool()) {
            $volumeQueryBuilder
                ->andWhere('v.pool = :pool')
                ->setParameter('pool', $volumeSearch->getPool());
        }

        $orderBy = $volumeSearch->getOrderby() ? 'v.' . $volumeSearch->getOrderby() : 'v.name';
        $volumeQueryBuilder->orderBy($orderBy, $volumeSearch->getOrderDirection() ? 'ASC' : 'DESC');

        if ($volumeSearch->getInChanger()) {
            $volumeQueryBuilder
                ->andWhere('v.inchanger = 1');
        }

        $pagination = $paginator->paginate(
            $volumeQueryBuilder,
            $request->query->getInt('page', 1),
            $this->getParameter('app.rows_per_page')
        );

        return $this->render('pages/volumes.html.twig', [
            'volumes' => $pagination->getItems(),
            'pagination' => $pagination,
            'form' => $form,
            'volumes_total_count' => $this->entityManager
                ->getRepository(Volume::class)
                ->count(),
            'volumes_total_bytes' => $this->entityManager
                ->createQueryBuilder()
                ->select('SUM(v.volbytes)')
                ->from(Volume::class, 'v')
                ->getQuery()
                ->getSingleScalarResult(),
        ]);
    }

    /**
     * @param Volume|null $volume
     * @return Response
     */
    #[Route("/volume/{id}", name: "volume_detail")]
    public function show(?Volume $volume): Response
    {
        if( !$volume) {
            throw $this->createNotFoundException('Volume with provided id not found');
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $jobs = $queryBuilder
            ->select('v', 'j.id', 'j.name', 'j.type')
            ->distinct()
            ->from(Volume::class, 'v')
            ->innerJoin(JobMedia::class, 'jm', Join::WITH, 'v.id = jm.mediaid')
            ->innerJoin(Job::class, 'j', Join::WITH, 'jm.jobid = j.id')
            ->where('v.id = :id')
            ->setParameter('id', $volume->getId())
            ->getQuery()
            ->getArrayResult();

        return $this->render('pages/volume.html.twig', [
            'volume' => $volume,
            'jobs' => $jobs
        ]);
    }
}
