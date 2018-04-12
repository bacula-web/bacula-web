<?php

/**
 * Description of historyfiles
 *
 * @author gorlando
 */
class HistoryFilesView extends CView {
	
	public function __construct() {
        
        $this->templateName = 'historyfiles.tpl';
		$this->name = 'History Files';
        $this->title = 'History Files';

        parent::init();
    }
	
	public function prepare() {
		$rows_per_page = 10;
		
		$historyFiles = new HistoryFiles_Model();
		$jobId = $_GET['jobId'];
		$this->assign('jobid', $jobId);
		$jobInfo = $historyFiles->getJobNameAndJobStatusByJobId($jobId);
		$this->assign('job_info', $jobInfo);
		$files_count = $historyFiles->getCountHistoryFiles($jobId);
		$this->assign('history_files_count', $files_count);
		
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
		
		$files = $historyFiles->getHistoryFiles($jobId, $rows_per_page, $current_page);
		$this->assign('history_files', $files);
		$this->assign('history_files_count_paging', count($files));
	}
}
