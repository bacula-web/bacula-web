<?php

declare(strict_types=1);

/**
 * Copyright (C) 2018 Gabriele Orlando
 * Copyright (C) 2019-2023 Davide Franco
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

/**
 * Description of JobFilesView class
 *
 * @author Gabriele Orlando
 * @author Davide Franco
 */

namespace App\Controller;

use Core\App\Controller;
use Core\Db\DatabaseFactory;
use Core\Utils\CUtils;
use Core\Helpers\Sanitizer;
use App\Tables\JobFileTable;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use TypeError;

class JobFilesController extends Controller
{
    /**
     * @return Response
     * @throws Exception
     */
    public function prepare(): Response
    {
        $rows_per_page = 10;
        $current_page = null;

        $jobFiles = new JobFileTable(
            DatabaseFactory::getDatabase(
                (new Session())->get('catalog_id', 0)
            )
        );

        $filename = '';

        $jobId = $this->request->query->getInt('jobId');

        if ($jobId !== 0) {
            $this->setVar('jobid', $jobId);
        } else {
            throw new TypeError('Invalid or missing Job Id');
        }

        if ($this->request->request->has('InputFilename')) {
            $filename = $this->request->request->get('InputFilename');
            $filename = Sanitizer::sanitize($filename);
        }

        $jobInfo = $jobFiles->getJobNameAndJobStatusByJobId($jobId);
        $this->setVar('job_info', $jobInfo);
        $files_count = $jobFiles->countJobFiles($jobId, $filename);
        $this->setVar('job_files_count', CUtils::format_Number($files_count));

        //pagination
        $pagination_active = false;
        if ($files_count > $rows_per_page) {
            $pagination_active = true;
        }

        if ($this->request->query->has('paginationCurrentPage')) {
            $current_page = $this->request->query->getInt('paginationCurrentPage');
        }

        $this->setVar('pagination_active', $pagination_active);
        $this->setVar('pagination_current_page', $current_page);
        $this->setVar('pagination_rows_per_page', $rows_per_page);

        if (!empty($filename)) {
            // Filter with provided filename if provided
            $files = $jobFiles->getJobFiles($jobId, $rows_per_page, $current_page, $filename);
        } else {
            // otherwise, get files based on JobId only
            $files = $jobFiles->getJobFiles($jobId, $rows_per_page, $current_page);
        }

        $this->setVar('job_files', $files);
        $this->setVar('job_files_count_paging', count($files));

        $this->setVar('filename', $filename);

        return (new Response($this->render('jobfiles.tpl')));
    }
}
