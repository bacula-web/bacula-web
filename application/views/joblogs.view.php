<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
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

namespace App\Views;

use Core\App\WebApplication;
use Core\App\CView;
use Core\Db\CDBQuery;
use Core\Db\DatabaseFactory;
use App\Tables\JobTable;
use Exception;

class JobLogsView extends CView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'joblogs.tpl';
        $this->name = 'Job logs';
        $this->title = 'Bacula job log';
    }

    public function prepare()
    {
        $joblogs = array();
        $jobid = WebApplication::getRequest()->query->getInt('jobid', 0);

        /*
         * if $_GET['jobid'] is not a valid integer different than 0, then throw an exception
         * Exceptions will be handled in a better fashion in later development
         */
        if ($jobid === 0) {
            throw new Exception('Invalid job id (invalid or null) provided in Job logs report');
        }

        // Prepare and execute SQL statment
        $jobs = new JobTable(DatabaseFactory::getDatabase());
        $statment     = array('table' => 'Log', 'where' => array("JobId = :jobid"), 'orderby' => 'Time');
        $jobs->addParameter('jobid', $jobid);
        $result     = $jobs->run_query(CDBQuery::get_Select($statment));

        // Processing result
        foreach ($result->fetchAll() as $log) {
            $log['logtext'] = nl2br($log['logtext']);
            $log['time'] = date($_SESSION['datetime_format'], strtotime($log['time']));
            $joblogs[] = $log;
        }
        
        $this->assign('jobid', $jobid);
        $this->assign('joblogs', $joblogs);
    } // end of prepare() method
} // end of class
