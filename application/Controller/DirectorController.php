<?php

declare(strict_types=1);

/**
 * Copyright (C) 2011-present Davide Franco
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

namespace App\Controller;

use App\Libs\Config;
use Core\Db\DatabaseFactory;
use App\Table\ClientTable;
use App\Table\JobTable;
use App\Table\CatalogTable;
use App\Table\VolumeTable;
use App\Table\PoolTable;
use App\Table\FileSetTable;
use Core\Utils\CUtils;
use Odan\Session\SessionInterface;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use GuzzleHttp\Psr7\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DirectorController
{
    private Twig $view;
    private SessionInterface $session;
    private Config $config;

    public function __construct(Twig $view, SessionInterface $session, Config $config)
    {
        $this->view = $view;
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $tplData = [];

        // Save catalog_id from user session
        $prev_catalog_id = $this->session->get('catalog_id') ?? 0;

        $directors = $this->config->getArrays();

        $directors_count = count($directors);

        $tplData['directors_count'] = $directors_count;

        foreach ($directors as $id => $director) {
            $this->session->set('catalog_id', $id);

            $host = $director['host'] ?? null;
            $db_user = $director['login'] ?? null;
            $db_name = $director['db_name'];
            $db_type = $director['db_type'];
            $description = "Bacula catalog on host $host, database: $db_name ($db_type) with user $db_user";

            try {
                $clients = new ClientTable(DatabaseFactory::getDatabase($id));
                $jobs = new JobTable(DatabaseFactory::getDatabase($id));
                $catalog = new CatalogTable(DatabaseFactory::getDatabase($id));
                $volumes = new VolumeTable(DatabaseFactory::getDatabase($id));
                $pools = new PoolTable(DatabaseFactory::getDatabase($id));
                $filesets = new FileSetTable(DatabaseFactory::getDatabase($id));

                $directors[$id] = [
                    'label' => $director['label'],
                    'clients' => $clients->count(),
                    'jobs' => $jobs->count_Job_Names(),
                    'totalbytes' => CUtils::Get_Human_Size($jobs->getStoredBytes()),
                    'totalfiles' => CUtils::format_Number($jobs->getStoredFiles()),
                    'dbsize' => $catalog->get_Size($director['db_name'], $id),
                    'volumes' => $volumes->count(),
                    'volumesize' => CUtils::Get_Human_Size($volumes->getDiskUsage()),
                    'pools' => $pools->count(),
                    'filesets' => $filesets->count(),
                    'description' => $description
                ];
            } catch(PDOException $exception) {
                $directors[$id]['error'] = $exception->getMessage();
                $this->session->set('catalog_id', $prev_catalog_id);
                continue;
            }

            unset($clients);
            unset($jobs);
            unset($catalog);
            unset($volumes);
            unset($pools);
            unset($filesets);
        }

        // Set previous catalog_id in user session
        $this->session->set('catalog_id', $prev_catalog_id);

        $tplData['directors'] = $directors;

        return $this->view->render($response, 'pages/directors.html.twig', $tplData);
    }
}
