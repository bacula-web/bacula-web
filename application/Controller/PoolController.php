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

use Core\Utils\CUtils;
use App\Table\PoolTable;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Response;
use Slim\Views\Twig;

class PoolController
{
    /**
     * @var PoolTable
     */

    private PoolTable $poolTable;

    /**
     * @param PoolTable $poolTable
     */
    public function __construct(PoolTable $poolTable)
    {
        $this->poolTable = $poolTable;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function prepare(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $tplData = [];

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

        $tplData['pools'] = $pools_list;

        return $view->render($response, 'pages/pools.html.twig', $tplData);
    }
}
