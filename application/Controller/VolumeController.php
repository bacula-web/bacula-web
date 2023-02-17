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
use Core\Exception\AppException;
use App\Tables\VolumeTable;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class VolumeController extends Controller
{
    /**
     * @return Response
     * @throws Exception
     */
    public function prepare(): Response
    {
        $session = new Session();
        $volume = new VolumeTable(DatabaseFactory::getDatabase($session->get('catalog_id', 0)));
        $params = [];

        if ($this->request->get('id') === null) {
            throw new AppException('Missing volume id');
        }

        $volumeid = (int) $this->request->get('id');

        $where[] = 'Media.MediaId = :volume_id';
        $params['volume_id'] = $volumeid;

        $sqlquery = CDBQuery::get_Select(
            [
                'table' => 'Media',
                'fields' => ['*'],
                'where' => $where
            ],
            $volume->get_driver_name()
        );

        $this->setVar(
            'volume',
            $volume->select($sqlquery, $params,'App\Entity\Volume', true)
        );

        $this->setVar('jobs', $volume->getJobs($volumeid));

        return (new Response($this->render('volume.tpl')));
    }
}
