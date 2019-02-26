<?php

/**
 * Description of historyfiles
 *
 * @author gorlando
 */
class JobFilesView extends CView {
	
	public function __construct() {
        
        $this->templateName = 'jobfiles.tpl';
		$this->name = 'Job files';
        $this->title = 'Job files';

        parent::init();
    }
	
	public function prepare() {
		$rows_per_page = 10;
		
		$jobFiles = new JobFiles_Model();
		$jobId = $_GET['jobId'];
		$this->assign('jobid', $jobId);
		$jobInfo = $jobFiles->getJobNameAndJobStatusByJobId($jobId);
		$this->assign('job_info', $jobInfo);
		$files_count = $jobFiles->countJobFiles($jobId);
		$this->assign('job_files_count', $files_count);
		
		//pagination
		$pagination_active = FALSE;
		if($files_count > $rows_per_page){
			$pagination_active = TRUE;
		}
		$this->assign('pagination_active', $pagination_active);
		$current_page = 0;
		if(array_key_exists('paginationCurrentPage', $_GET)){
			$current_page = $_GET['paginationCurrentPage'];
		}
		$this->assign('pagination_current_page', $current_page);
		$this->assign('pagination_rows_per_page', $rows_per_page);
		
		$files = $jobFiles->getJobFiles($jobId, $rows_per_page, $current_page);
		$this->assign('job_files', $files);
		$this->assign('job_files_count_paging', count($files));
	}
}
