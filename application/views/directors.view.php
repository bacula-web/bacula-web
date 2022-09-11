<?php

/**
 * Copyright (C) 2011-2022 Davide Franco
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

class DirectorsView extends CView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'directors.tpl';
        $this->name = 'Directors';
        $this->title = 'Bacula director(s) overview';
    }

    public function prepare()
    {
        require_once BW_ROOT . '/core/const.inc.php';
        
        $no_period = array(FIRST_DAY, NOW);
        $directors = array();

        // Save catalog_id from user session
        $prev_catalog_id = $_SESSION['catalog_id'];

        FileConfig::open(CONFIG_FILE);
        $directors_count = FileConfig::count_Catalogs();
        
        $this->assign('directors_count', $directors_count);

        for ($d=0; $d < $directors_count; $d++) {
            $_SESSION['catalog_id'] = $d;

            $clients = new ClientTable(DatabaseFactory::getDatabase());
            $jobs = new JobTable(DatabaseFactory::getDatabase());
            $catalog = new CatalogTable(DatabaseFactory::getDatabase());
            $volumes = new VolumeTable(DatabaseFactory::getDatabase());
            $pools = new PoolTable(DatabaseFactory::getDatabase());
            $filesets = new FileSetTable(DatabaseFactory::getDatabase());

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
        $_SESSION['catalog_id'] = $prev_catalog_id;

        $this->assign('directors', $directors);
    } // end of prepare() method
} // end of class
