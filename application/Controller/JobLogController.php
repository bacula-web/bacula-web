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

use App\Tables\LogTable;
use Core\App\Controller;
use Core\Db\CDBQuery;
use App\Tables\JobTable;
use Core\Exception\ConfigFileException;
use SmartyException;
use Symfony\Component\HttpFoundation\Response;
use TypeError;

class JobLogController extends Controller
{
    /**
     * @param LogTable $logTable
     * @param JobTable $jobTable
     * @return Response
     * @throws SmartyException
     * @throws ConfigFileException
     */
    public function prepare(LogTable $logTable, JobTable $jobTable): Response
    {
        $jobid = $this->request->query->getInt('jobid');

        if ($jobid === 0) {
            throw new TypeError('Invalid job id (invalid or null) provided in Job logs report');
        }

        $this->setVar('job', $jobTable->findById($jobid));

        $sql = CDBQuery::get_Select(
            [
                'table' => 'Log',
                'where' => [ 'JobId = :jobid'],
                'orderby' => 'Time'
            ]
        );

        $this->setVar(
            'joblogs',
            $logTable->findAll($sql, ['jobid' => $jobid], 'App\Entity\Log')
        );

        return new Response($this->render('joblogs.tpl'));
    }
}
