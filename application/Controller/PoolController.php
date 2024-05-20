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

use App\Entity\Bacula\Pool;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Pools report controller
 */
class PoolController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager('bacula');
    }

    /**
     * @Route("/pools", name="pools")
     *
     * @param ParameterBagInterface $parameters
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function prepare(ParameterBagInterface $parameters): Response
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('p', 'v')
            ->from(Pool::class, 'p')
            ->leftJoin('p.volumes', 'v')
            ->orderBy('p.name')
        ;

        if ($parameters->get('app.hide_empty_pools') === true) {
            $queryBuilder->andWhere('p.numvols > 0');
        }

        /**
         * TODO: refactor code below with a single query
         */
        $pools = $queryBuilder->getQuery()->getArrayResult();

        $dql = 'SELECT SUM(m.volbytes) as sumbytes FROM App\Entity\Bacula\Volume m WHERE m.poolId = :poolid';

        foreach ($pools as $id => $pool) {
            $query = $this->entityManager->createQuery($dql);
            $query->setParameter('poolid', $pool['id']);
            $totalBytes = $query->getSingleScalarResult();
            $pools[$id]['total_bytes'] = (int) $totalBytes;
        }

        return $this->render('pages/pools.html.twig', [
            'pools' => $pools
        ]);
    }
}
