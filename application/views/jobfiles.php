<?php

/**
 * Description of JobFilesView class
 *
 * @author Gabriele Orlando
 * @author Davide Franco
 * @copyright 2018 Gavriele Orlando
 */

class JobFilesView extends CView
{
    public function __construct()
    {
        parent::__construct();
        
        $this->templateName = 'jobfiles.tpl';
        $this->name = 'Job files';
        $this->title = 'Bacula Job Files';
    }
    
    public function prepare()
    {
        $rows_per_page = 10;
        $jobFiles = new JobFiles_Model();
        $filename = '';

        if (CHttpRequest::get_Value('jobId') != null) {

            // Ensure pool_id value is numeric
            $jobId = CHttpRequest::get_Value('jobId');

            if (!is_numeric($jobId) && !is_null($jobId)) {
                throw new Exception('Invalid Job Id (not numeric) provided in ' . $this->title);
            }

            $this->assign('jobid', $jobId);
        } else {
            throw new Exception('Missing Job Id parameter in' . $this->title);
        }

        if (CHttpRequest::get_Value('InputFilename') != null) {
            $filename = CHttpRequest::get_Value('InputFilename');
            $this->assign('filename', $filename);
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
        $this->assign('pagination_active', $pagination_active);
        $current_page = 0;
        if (array_key_exists('paginationCurrentPage', $_GET)) {
            $current_page = $_GET['paginationCurrentPage'];
        }
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
    }
}
