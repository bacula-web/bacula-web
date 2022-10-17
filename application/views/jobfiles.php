<?php

/**
 * Copyright (C) 2018 Gabriele Orlando
 * Copyright (C) 2019-2022 Davide Franco
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

namespace App\Views;

use Core\App\CView;
use Core\Db\DatabaseFactory;
use Core\Utils\CUtils;
use Core\Helpers\Sanitizer;
use App\Tables\JobFileTable;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class JobFilesView extends CView
{
    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        
        $this->templateName = 'jobfiles.tpl';
        $this->name = 'Job files';
        $this->title = 'Bacula Job Files';
    }
    
    public function prepare(Request $request)
    {
        $rows_per_page = 10;
        $current_page = null;
        $jobFiles = new JobFileTable(DatabaseFactory::getDatabase());
        $filename = '';

        $jobId = $request->query->getInt('jobId', 0);

        if($jobId !== 0) {
            $this->assign('jobid', $jobId);
        } else {
            throw new Exception('Invalid or missing Job Id (not numeric) provided in ' . $this->title);
        }

        if ($request->request->has('InputFilename')) {
            $filename = $request->request->get('InputFilename');
            $filename = Sanitizer::sanitize($filename);
        }

        $jobInfo = $jobFiles->getJobNameAndJobStatusByJobId($jobId);
        $this->assign('job_info', $jobInfo);
        $files_count = $jobFiles->countJobFiles($jobId, $filename);
        $this->assign('job_files_count', CUtils::format_Number($files_count));
        
        //pagination
        $pagination_active = false;
        if ($files_count > $rows_per_page) {
            $pagination_active = true;
        }

        if ($request->query->has('paginationCurrentPage')) {
            $current_page = $request->query->getInt('paginationCurrentPage');
        }

        $this->assign('pagination_active', $pagination_active);
        $this->assign('pagination_current_page', $current_page);
        $this->assign('pagination_rows_per_page', $rows_per_page);

        if (!empty($filename)) {
            // Filter with provided filename if provided
            $files = $jobFiles->getJobFiles($jobId, $rows_per_page, $current_page, $filename);
        } else {
            // otherwise, get files based on JobId only
            $files = $jobFiles->getJobFiles($jobId, $rows_per_page, $current_page);
        }

        $this->assign('job_files', $files);
        $this->assign('job_files_count_paging', count($files));

        $this->assign('filename', $filename);
    }
}
