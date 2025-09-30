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
use App\Entity\Bacula\Pool;
use App\Entity\Bacula\Volume;
use Core\Controller\AbstractController;
use Core\Db\DBPagination;
use Core\Exception\ValidationException;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Query\Expr\Join;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Valitron\Validator;

use function Core\Helpers\getRequestParams;

class VolumesController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function index(Request $request, Response $response): Response
    {
        $em = $this->managerRegistry->getManager('bacula');

        $volumeslist = [];
        $volumes_total_bytes = 0;
        $volumeOrderBy = 'v.name';
        $volumeOrderByDirection = 'DESC';
        $orderDirectionChecked = '';
        $inChangerChecked = '';
        $poolId = 0;

        // Order by
        $orderby = [
            'v.name' => 'Name',
            'v.id' => 'Id',
            'v.volbytes' => 'Bytes',
            'v.voljobs' => 'Jobs'
        ];

        // Volumes status icon
        $volumestatus = [
            'Full' => 'fa-battery-full',
            'Archive' => 'fa-file-archive-o',
            'Append' => 'fa-battery-quarter',
            'Recycle' => 'fa-recycle',
            'Read-Only' => 'fa-lock',
            'Disabled' => 'fa-ban',
            'Error' => 'fa-times-circle',
            'Busy' => 'fa-clock-o',
            'Used' => 'fa-battery-quarter',
            'Purged' => 'fa-battery-empty'
        ];

        $poolList = $em->getRepository(Pool::class)->getPools(false);

        $postData = getRequestParams($request);

        $queryBuilder = $em->createQueryBuilder();

        $volumesRequestValidator = new Validator($postData, [
            'page',
            'filter_pool_id',
            'filter_orderby',
            'filter_orderby_asc',
            'filter_inchanger'
        ]);

        $volumesRequestValidator->rule('integer', ['page']);
        $volumesRequestValidator->rule('integer', ['filter_pool_id']);
        $volumesRequestValidator->rule('in', 'filter_pool_id', array_keys($poolList))->message('Invalid pool');
        $volumesRequestValidator->rule('in', 'filter_orderby', $orderby)->message('Invalid order by');
        $volumesRequestValidator->rule('in', 'filter_orderby_asc', ['ASC', 'DESC'])->message('Invalid order direction');

        if (!empty($postData)) {
            if (!$volumesRequestValidator->validate()) {
                throw new ValidationException($volumesRequestValidator->errors());
            }

            $poolId = $postData['filter_pool_id'] ?? '0';
            if ($poolId !== '0') {
                $queryBuilder
                    ->andWhere('p.id = :pool_id')
                    ->setParameter('pool_id', $poolId);
            }

            $volumeOrderBy = $postData['filter_orderby'] ?? 'v.name';

            $volumeOrderByDirection = $postData['filter_orderby_asc'] ?? 'DESC';
            $orderDirectionChecked = $volumeOrderByDirection === 'ASC' ? 'checked' : '';

            if (isset($postData['filter_inchanger'])) {
                $queryBuilder
                    ->andWhere('v.inchanger = 1');
            }
            $inChangerChecked = isset($postData['filter_inchanger']) ? 'checked' : '';
        }

        $queryBuilder
            ->select('v,p')
            ->from(Volume::class, 'v')
            ->leftJoin('v.pool', 'p')
            ->orderBy($volumeOrderBy, $volumeOrderByDirection);

        $totalVolumesCount = $em->getRepository(Volume::class)->count([]);

        $pagination = new DBPagination($request, $this->config);

        // TODO: $volumes_total_bytes += $volume['volbytes'];

        return $this->view->render($response, 'pages/volumes.html.twig', [
            'pools_list' => $poolList,
            'volumes' => $pagination->paginate($queryBuilder, $totalVolumesCount),
            'volumes_total_bytes' => $volumes_total_bytes,
            'volumes_count' => $totalVolumesCount,
            'pagination' => $pagination,
            'pool_id' => $poolId,
            'orderby' => $orderby,
            'orderby_selected' => $volumeOrderBy,
            'orderby_asc_checked' => $orderDirectionChecked,
            'inchanger_checked' => $inChangerChecked
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws NotSupported
     */
    public function show(Request $request, Response $response): Response
    {
        $requestData = $request->getAttributes();
        $volumeId = (int) $requestData['id'];

        $em = $this->managerRegistry->getManager('bacula');
        $queryBuilder = $em->createQueryBuilder();

        $query = $queryBuilder
            ->select('v', 'j.id', 'j.name', 'j.type')
            ->distinct()
            ->from(Volume::class, 'v')
            ->innerJoin(JobMedia::class, 'jm', Join::WITH, 'v.id = jm.mediaid')
            ->innerJoin(Job::class, 'j', Join::WITH, 'jm.jobid = j.id')
            ->where('v.id = :id')
            ->setParameter('id', $volumeId)
            ->getQuery()
        ;
        $jobs = $query->getArrayResult();

        $volume = $em->getRepository(Volume::class)->find($volumeId);

        /**
         * TODO: if $volume is null, return 404 instead of returning the view
         */

        return $this->view->render($response, 'pages/volume.html.twig', [
            'volume' => $volume,
            'jobs' => $jobs
        ]);
    }
}
