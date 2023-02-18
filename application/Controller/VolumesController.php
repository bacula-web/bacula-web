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

use Core\App\Controller;
use Core\Db\DatabaseFactory;
use Core\Db\CDBQuery;
use Core\Db\CDBPagination;
use Core\Utils\CUtils;
use App\Tables\VolumeTable;
use App\Tables\PoolTable;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class VolumesController extends Controller
{
    /**
     * @return Response
     * @throws Exception
     */
    public function prepare(): Response
    {
        $volumes = new VolumeTable(DatabaseFactory::getDatabase($this->session->get('catalog_id', 0)));
        $params = [];

        $volumes_list = array();
        $volumes_total_bytes = 0;
        $where = null;

        // Paginate database query result
        $pagination = new CDBPagination($this->view);

        // Volumes status icon
        $volume_status = array( 'Full' => 'fa-battery-full',
            'Archive' => 'fa-file-archive-o',
            'Append' => 'fa-battery-quarter',
            'Recycle' => 'fa-recycle',
            'Read-Only' => 'fa-lock',
            'Disabled' => 'fa-ban',
            'Error' => 'fa-times-circle',
            'Busy' => 'fa-clock-o',
            'Used' => 'fa-battery-quarter',
            'Purged' => 'fa-battery-empty' );

        // Pools list filter
        $pools = new PoolTable(DatabaseFactory::getDatabase($this->session->get('catalog_id', 0)));
        $pools_list = array();

        // Create pools list
        foreach ($pools->getPools() as $pool) {
            $pools_list[$pool['poolid']] = $pool['name'];
        }

        $pools_list = array( 0 => 'Any') + $pools_list; // Add default pool filter
        $this->setVar('pools_list', $pools_list);

        /*
         * If filter_pool_id parameter is not provided in GET or POST request, use 0 as default
         */
        $pool_id = (int) $this->getParameter('filter_pool_id', 0);

        if ($pool_id !== 0) {
            $where[] = 'Media.PoolId = :pool_id';
            $params['pool_id'] = $pool_id;
        }

        // Order by
        $orderby = array('Name' => 'Name', 'MediaId' => 'Id', 'VolBytes' => 'Bytes', 'VolJobs' => 'Jobs');

        // Set order by
        $this->setVar('orderby', $orderby);

        $volume_orderby_filter = $this->getParameter('filter_orderby', 'Name');
        $this->setVar('orderby_selected', $volume_orderby_filter);

        if (!array_key_exists($volume_orderby_filter, $orderby)) {
            throw new \TypeError("Critical: Provided orderby parameter is not correct");
        }

        // Set order by filter and checkbox status
        $volume_orderby_asc = $this->getParameter('filter_orderby_asc', 'DESC');

        if ($volume_orderby_asc === 'Asc') {
            $this->setVar('orderby_asc_checked', 'checked');
        } else {
            $this->setVar('orderby_asc_checked', '');
        }

        // Set inchanger checkbox to unchecked by default
        if ($this->request->request->has('filter_inchanger')) {
            $where[] = 'Media.inchanger = :inchanger';
            $params['inchanger'] = 1;
            $this->setVar('inchanger_checked', 'checked');
        } else {
            $this->setVar('inchanger_checked', '');
        }

        $fields = [
            'Media.mediaid',
            'Media.volumename',
            'Media.volbytes',
            'Media.voljobs',
            'Media.volstatus',
            'Media.mediatype',
            'Media.lastwritten',
            'Media.volretention',
            'Media.slot',
            'Media.inchanger',
            'Pool.Name AS pool_name'
        ];

        $sqlQuery = CDBQuery::get_Select(array('table' => $volumes->getTableName(),
                                            'fields' => $fields,
                                            'orderby' => "$volume_orderby_filter $volume_orderby_asc",
                                            'join' => array(
                                                array('table' => 'Pool', 'condition' => 'Media.poolid = Pool.poolid')
                                            ),
                                            'where' => $where,
                                            'limit' => [
                                                'count' => $pagination->getLimit(),
                                                'offset' => $pagination->getOffset() ]
                                            ), $volumes->get_driver_name());

        $countQuery = CDBQuery::get_Select([
            'table' => $volumes->getTableName(),
            'fields' => ['COUNT(*) as row_count'],
            'where' => $where ]);

        foreach ($pagination->paginate($volumes, $sqlQuery, $countQuery, $params) as $volume) {
            // Calculate volume expiration
            // If volume have already been used
            if ($volume['lastwritten'] != "0000-00-00 00:00:00") {
                // Calculate expiration date only if volume status is Full or Used
                if ($volume['volstatus'] == 'Full' || $volume['volstatus'] == 'Used') {
                    $expire_date = strtotime($volume['lastwritten']) + $volume['volretention'];
                    $volume['expire'] = date($this->session->get('datetime_format_short'), $expire_date);
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
                $volume['lastwritten'] = date($this->session->get('datetime_format'), strtotime($volume['lastwritten']));
            }

            // Sum volumes bytes
            $volumes_total_bytes += $volume['volbytes'];

            // Get volume used bytes in a human format
            $volume['volbytes'] = CUtils::Get_Human_Size($volume['volbytes']);

            // Update volume inchanger
            if ($volume['inchanger'] == '0') {
                $volume['inchanger'] = '-';
                $volume['slot'] = 'n/a';
            } else {
                $volume['inchanger'] = '<span class="glyphicon glyphicon-ok"></span>';
            }

            // Set volume status icon
            $volume['status_icon'] = $volume_status[ $volume['volstatus'] ];

            // Format voljobs
            $volume['voljobs'] = CUtils::format_Number($volume['voljobs']);

            // add volume in volumes list array
            $volumes_list[] = $volume;
        } // end foreach

        $this->setVar('pool_id', $pool_id);
        $this->setVar('volumes', $volumes_list);

        $this->setVar('volumes_count', $volumes->count());
        $this->setVar('volumes_total_bytes', CUtils::Get_Human_Size($volumes_total_bytes));

        return (new Response($this->render('volumes.tpl')));
    }
}
