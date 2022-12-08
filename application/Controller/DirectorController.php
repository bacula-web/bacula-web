<?php

declare(strict_types=1);

/**
 * Copyright (C) 2011-2023 Davide Franco
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
use App\Libs\FileConfig;
use App\Tables\ClientTable;
use App\Tables\JobTable;
use App\Tables\CatalogTable;
use App\Tables\VolumeTable;
use App\Tables\PoolTable;
use App\Tables\FileSetTable;
use Core\Utils\ConfigFileException;
use Core\Utils\CUtils;
use SmartyException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class DirectorController extends Controller
{
    /**
     * @return Response
     * @throws ConfigFileException
     * @throws SmartyException
     */
    public function prepare(): Response
    {
        require_once BW_ROOT . '/core/const.inc.php';

        $session = new Session();

        $no_period = [
            FIRST_DAY,
            NOW
        ];

        $directors = [];

        // Save catalog_id from user session
        $prev_catalog_id = $session->get('catalog_id');

        FileConfig::open(CONFIG_FILE);
        $directors_count = FileConfig::count_Catalogs();

        $this->setVar('directors_count', $directors_count);

        for ($d = 0; $d < $directors_count; $d++) {
            $session->set('catalog_id', $d);

            $clients = new ClientTable(DatabaseFactory::getDatabase($d));
            $jobs = new JobTable(DatabaseFactory::getDatabase($d));
            $catalog = new CatalogTable(DatabaseFactory::getDatabase($d));
            $volumes = new VolumeTable(DatabaseFactory::getDatabase($d));
            $pools = new PoolTable(DatabaseFactory::getDatabase($d));
            $filesets = new FileSetTable(DatabaseFactory::getDatabase($d));

            $host = FileConfig::get_Value('host', $d);
            $db_user = FileConfig::get_Value('login', $d);
            $db_name = FileConfig::get_Value('db_name', $d);
            $db_type = FileConfig::get_Value('db_type', $d);

            $description = "Connected on $host/$db_name ($db_type) with user $db_user";

            $directors[] = array( 'label' => FileConfig::get_Value('label', $d),
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
        $session->set('catalog_id', $prev_catalog_id);

        $this->setVar('directors', $directors);

        return (new Response($this->render('directors.tpl')));
    }
}
