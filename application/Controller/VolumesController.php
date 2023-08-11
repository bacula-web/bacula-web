<?php

declare(strict_types=1);

/**
 * Copyright (C) 2010-2023 Davide Franco
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

namespace App\Controller;

use App\Libs\FileConfig;
use Core\Db\CDBQuery;
use Core\Db\CDBPagination;
use Core\Exception\ConfigFileException;
use Core\Utils\CUtils;
use App\Tables\VolumeTable;
use App\Tables\PoolTable;
use Date_HumanDiff;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use TypeError;
use function Core\Helpers\getRequestParams;

class VolumesController
{
    private VolumeTable $volumeTable;
    private PoolTable $poolTable;

    private Twig $view;

    public function __construct(
        VolumeTable $volumeTable,
        PoolTable $poolTable,
        Twig $view
    ) 
    {
        $this->volumeTable = $volumeTable;
        $this->poolTable = $poolTable;
        $this->view = $view;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ConfigFileException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $tplData = [];
        $params = [];

        $volumeslist = [];
        $volumes_total_bytes = 0;
        $where = null;

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

        // Pools list filter
        $poolslist = [];

        // Create poolTable list
        foreach ($this->poolTable->getPools() as $pool) {
            $poolslist[$pool['poolid']] = $pool['name'];
        }

        $poolslist = [0 => 'Any'] + $poolslist; // Add default pool filter
        $tplData['pools_list'] = $poolslist;

        $postData = getRequestParams($request);

        $poolId = '0';

        if (isset($postData['filter_pool_id'])) {
            if ( $postData['filter_pool_id'] !== '0') {
                $poolId = $postData['filter_pool_id'];
                $where[] = 'Media.PoolId = :pool_id';
                $params['pool_id'] = $poolId;
            }
        }
        $tplData['pool_id'] = $poolId;

        // Order by
        $orderby = [
            'Name' => 'Name',
            'MediaId' => 'Id',
            'VolBytes' => 'Bytes',
            'VolJobs' => 'Jobs'
        ];

        // Set order by
        $tplData['orderby'] = $orderby;

        $volumeOrderBy = 'Name';

        if (isset($postData['filter_orderby'])) {
            $volumeOrderBy = $postData['filter_orderby'];
        }

        $tplData['orderby_selected'] = $volumeOrderBy;

        if (!array_key_exists($volumeOrderBy, $orderby)) {
            throw new TypeError('Critical: Provided orderby parameter is not correct');
        }

        $volumeOrderByDirection = 'Desc';
        if (isset($postData['filter_orderby_asc'])) {
            $volumeOrderByDirection = 'Asc';
        }
        if ($volumeOrderByDirection === 'Asc') {
            $tplData['orderby_asc_checked'] = 'checked';
        } else {
            $tplData['orderby_asc_checked'] = '';
        }

        // Set "inchanger" checkbox to unchecked by default
        if (isset($postData['filter_inchanger'])) {
            $where[] = 'Media.inchanger = :inchanger';
            $params['inchanger'] = 1;
            $tplData['inchanger_checked'] = 'checked';
        } else{
            $tplData['inchanger_checked'] = '';
        }

        $fields = [
            'Media.mediaid',
            'Media.volumename',
            'Media.volbytes',
            'Media.volfiles',
            'Media.voljobs',
            'Media.volstatus',
            'Media.mediatype',
            'Media.lastwritten',
            'Media.volretention',
            'Media.slot',
            'Media.inchanger',
            'Pool.Name AS pool_name'
        ];

        $pagination = new CDBPagination($request);

        $sqlQuery = CDBQuery::get_Select(array('table' => $this->volumeTable->getTableName(),
                                            'fields' => $fields,
                                            'orderby' => "$volumeOrderBy $volumeOrderByDirection",
                                            'join' => array(
                                                array('table' => 'Pool', 'condition' => 'Media.poolid = Pool.poolid')
                                            ),
                                            'where' => $where,
                                            'limit' => [
                                                'count' => $pagination->getLimit(),
                                                'offset' => $pagination->getOffset() ]
                                            ), $this->volumeTable->get_driver_name());

        $countquery = CDBQuery::get_Select([
            'table' => $this->volumeTable->getTableName(),
            'fields' => ['COUNT(*) as row_count'],
            'where' => $where ]);

        foreach ($pagination->paginate($this->volumeTable, $sqlQuery, $countquery, $params) as $volume) {
            // Calculate volume expiration
            // If volume have already been used
            if ($volume['lastwritten'] != "0000-00-00 00:00:00") {
                // Calculate expiration date only if volume status is Full or Used
                if ($volume['volstatus'] == 'Full' || $volume['volstatus'] == 'Used') {
                    $dh = new Date_HumanDiff();
                    $dateTimeFormatShort = explode(' ', FileConfig::get_Value('datetime_format'));
                    $volume['expire'] = date($dateTimeFormatShort[0],strtotime($volume['lastwritten']) + $volume['volretention']);
                    $volume['expire'] = $dh->get(strtotime($volume['lastwritten']) + $volume['volretention'], time()) . ' (' . $volume['expire'] . ')';
                } else {
                    $volume['expire'] = 'n/a';
                }
            } else {
                $volume['expire'] = 'n/a';
            }

            // Set lastwritten for the volume
            if (($volume['lastwritten'] == '0000-00-00 00:00:00') || empty($volume['lastwritten'])) {
                $volume['lastwritten'] = 'n/a';
            } else {
                // Format lastwritten in custom format if defined in config file
                $volume['lastwritten'] = date(
                    FileConfig::get_Value('datetime_format'),
                    strtotime($volume['lastwritten'])
                );
            }

            $volumes_total_bytes += $volume['volbytes'];

            // Get volume used bytes in a human format
            $volume['volbytes'] = CUtils::Get_Human_Size($volume['volbytes']);

            // Update volume inchanger
            if ($volume['inchanger'] == '0') {
                $volume['inchanger'] = '-';
                $volume['slot'] = 'n/a';
            } else {
                $volume['inchanger'] = '<i class="fa fa-check" aria-hidden="true"></i>';
            }

            // Set volume status icon
            $volume['status_icon'] = $volumestatus[ $volume['volstatus'] ];

            // Format voljobs
            $volume['voljobs'] = CUtils::format_Number($volume['voljobs']);

            // add volume in volumeTable list array
            $volumeslist[] = $volume;
        }

        $tplData['pagination'] = $pagination;

        $tplData['volumes'] = $volumeslist;
        $tplData['volumes_count'] = $this->volumeTable->count();
        $tplData['volumes_total_bytes'] = CUtils::Get_Human_Size($volumes_total_bytes);

        return $this->view->render($response, 'pages/volumes.html.twig', $tplData);
    }

    public function show(Request $request, Response $response): Response
    {
        $tplData = [];

        $requestData = $request->getAttributes();
        $params = [];

        $volumeId = (int) $requestData['id'];

        $where[] = 'Media.MediaId = :volume_id';

        $params['volume_id'] = $volumeId;

        $sqlQuery = CDBQuery::get_Select(
            [
                'table' => 'Media',
                'fields' => ['*'],
                'where' => $where
            ],
            $this->volumeTable->get_driver_name()
        );

        $tplData['volume'] = $this->volumeTable->select($sqlQuery, $params, 'App\Entity\Volume', true);
        $tplData['jobs'] = $this->volumeTable->getJobs($volumeId);

        return $this->view->render($response, 'pages/volume.html.twig', $tplData);
    }
}
