<?php

/**
 * Copyright (C) 2010-present Davide Franco
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

use App\Entity\Bacula\Pool;
use Core\Controller\AbstractController;
use Core\Utils\CUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PoolController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function prepare(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->managerRegistry->getManager('bacula');
        $queryBuilder = $em->createQueryBuilder();

        $pools = $queryBuilder
            ->select('p', 'v')
            ->from(Pool::class, 'p')
            ->leftJoin('p.volumes', 'v')
            ->orderBy('p.name')
            ->getQuery()
            ->getArrayResult();

        $dql = 'SELECT SUM(m.volbytes) as sumbytes FROM App\Entity\Bacula\Volume m WHERE m.poolId = :poolid';

        foreach ($pools as $id => $pool) {
            $query = $em->createQuery($dql);
            $query->setParameter('poolid', $pool['id']);
            $totalBytes = $query->getSingleScalarResult();
            $pools[$id]['total_bytes'] = CUtils::Get_Human_Size($totalBytes);
        }

        return $this->view->render($response, 'pages/pools.html.twig', [
            'pools' => $pools
        ]);
    }
}
