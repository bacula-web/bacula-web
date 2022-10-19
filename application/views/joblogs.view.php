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

use App\Tables\LogTable;
use Core\App\CView;
use Core\Db\CDBQuery;
use Core\Db\DatabaseFactory;
use App\Tables\JobTable;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class JobLogsView extends CView
{
    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        
        $this->templateName = 'joblogs.tpl';
        $this->name = 'Job logs';
        $this->title = 'Bacula job log';
    }

    public function prepare(Request $request)
    {
        $jobid = $request->query->getInt('jobid');

        /*
         * if $_GET['jobid'] is not a valid integer different than 0, then throw an exception
         * TODO: Exceptions will be handled in a better fashion in later development
         */
        if ($jobid === 0) {
            throw new Exception('Invalid job id (invalid or null) provided in Job logs report');
        }

        // Prepare and execute SQL statment
        $jobs = new JobTable(DatabaseFactory::getDatabase());

        $this->assign('job', $jobs->findById($jobid));

        $logTable = new LogTable(DatabaseFactory::getDatabase());

        $sql = CDBQuery::get_Select(
            [
                'table' => 'Log',
                'where' => [ 'JobId = :jobid'],
                'orderby' => 'Time'
            ]
        );

        $this->assign(
            'joblogs',
            $logTable->findAll(
                $sql,
                ['jobid' => $jobid],
                'App\Entity\Log'
            )
        );
    }
}
