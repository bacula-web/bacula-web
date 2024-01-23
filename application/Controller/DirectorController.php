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

use Core\Db\DatabaseFactory;
use App\Libs\FileConfig;
use App\Table\ClientTable;
use App\Table\JobTable;
use App\Table\CatalogTable;
use App\Table\VolumeTable;
use App\Table\PoolTable;
use App\Table\FileSetTable;
use Core\Exception\ConfigFileException;
use Core\Utils\CUtils;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
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

    public function __construct(Twig $view, SessionInterface $session)
    {
        $this->view = $view;
        $this->session = $session;
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

        $no_period = [
            FIRST_DAY,
            NOW
        ];

        $directors = [];

        // Save catalog_id from user session
        $prev_catalog_id = $this->session->get('catalog_id') ?? 0;

        FileConfig::open(CONFIG_FILE);
        $directors_count = FileConfig::count_Catalogs();

        $tplData['directors_count'] = $directors_count;

        for ($d = 0; $d < $directors_count; $d++) {
            $this->session->set('catalog_id', $d);

            $host = FileConfig::get_Value('host', $d);
            $db_user = FileConfig::get_Value('login', $d);
            $db_name = FileConfig::get_Value('db_name', $d);
            $db_type = FileConfig::get_Value('db_type', $d);
            $description = "Bacula catalog on host $host, database: $db_name ($db_type) with user $db_user";

            try {
                $clients = new ClientTable(DatabaseFactory::getDatabase($d));
                $jobs = new JobTable(DatabaseFactory::getDatabase($d));
                $catalog = new CatalogTable(DatabaseFactory::getDatabase($d));
                $volumes = new VolumeTable(DatabaseFactory::getDatabase($d));
                $pools = new PoolTable(DatabaseFactory::getDatabase($d));
                $filesets = new FileSetTable(DatabaseFactory::getDatabase($d));
            } catch(\PDOException $exception) {
                $directors[$d]['label'] = FileConfig::get_Value('label', $d);
                $directors[$d]['description'] = $description;
                $directors[$d]['error'] = $exception->getMessage();

                $this->session->set('catalog_id', $prev_catalog_id);

                continue;
            }

            $directors[] = array('label' => FileConfig::get_Value('label', $d),
                'description' => $description,
                'clients' => $clients->count(),
                'jobs' => $jobs->count_Job_Names(),
                'totalbytes' => CUtils::Get_Human_Size($jobs->getStoredBytes($no_period)),
                'totalfiles' => CUtils::format_Number($jobs->getStoredFiles($no_period)),
                'dbsize' => $catalog->get_Size($d),
                'volumes' => $volumes->count(),
                'volumesize' => CUtils::Get_Human_Size($volumes->getDiskUsage()),
                'pools' => $pools->count(),
                'filesets' => $filesets->count()
            );

            // Destroy CatalogTable object
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
