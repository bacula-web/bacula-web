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

use Core\App\View;
use Core\Utils\CUtils;
use App\Tables\PoolTable;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use SmartyException;

class PoolController
{
    /**
     * @var PoolTable
     */

    private PoolTable $poolTable;
    /**
     * @var View
     */
    private View $view;

    /**
     * @param PoolTable $poolTable
     * @param View $view
     */
    public function __construct(PoolTable $poolTable, View $view)
    {
        $this->poolTable = $poolTable;
        $this->view = $view;
    }

    /**
     * @return Response
     * @throws SmartyException
     */
    public function prepare(Request $request, Response $response): Response
    {
        $pools_list = [];

        // Add more details to each pool
        foreach ($this->poolTable->getPools() as $pool) {
            // Total bytes for each pool
            $sql = "SELECT SUM(Media.volbytes) as sumbytes FROM Media WHERE Media.PoolId = '" . $pool['poolid'] . "'";
            $result = $this->poolTable->run_query($sql);
            $result = $result->fetchAll();
            $pool['totalbytes'] = CUtils::Get_Human_Size($result[0]['sumbytes']);

            $pools_list[] = $pool;
        }

        $this->view->set('pools', $pools_list);

        $response->getBody()->write($this->view->render('pools.tpl'));
        return $response;
    }
}
