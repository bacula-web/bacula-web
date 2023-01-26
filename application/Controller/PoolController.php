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
use Core\Utils\CUtils;
use App\Tables\PoolTable;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class PoolController extends Controller
{
    /**
     * @return Response
     * @throws Exception
     */
    public function prepare(): Response
    {
        // Get volumes list (pools.tpl)
        $pools = new PoolTable(DatabaseFactory::getDatabase((new Session())->get('catalog_id', 0)));
        $pools_list = array();
        $plist = $pools->getPools();

        // Add more details to each pool
        foreach ($plist as $pool) {
            // Total bytes for each pool
            $sql = "SELECT SUM(Media.volbytes) as sumbytes FROM Media WHERE Media.PoolId = '" . $pool['poolid'] . "'";
            $result = $pools->run_query($sql);
            $result = $result->fetchAll();
            $pool['totalbytes'] = CUtils::Get_Human_Size($result[0]['sumbytes']);

            $pools_list[] = $pool;
        }

        $this->setVar('pools', $pools_list);

        return (new Response($this->render('pools.tpl')));
    }
}
