<?php

use PHP_CodeSniffer\Tokenizers\PHP;

/**
 * CDBPagination helps creating pagination from database queries results
 *
 * @author  Davide Franco <bacula-dev@dflc.ch>
 *
 */

class CDBPagination
{
    private $currentView;
    private $offset;
    private $limit;
    private $paginationMax;
    private $paginationSteps = 4;
    private $paginationLink;
    private $paginationCurrent = 1;
    
    public function __construct($view)
    {
        $this->currentView = $view;
        $this->limit = (FileConfig::get_Value('row_per_page') !== null) ? FileConfig::get_Value('row_per_page') : 25;
        $this->offset = (CHttpRequest::get_Value('pagination_page') !==null) ? ((CHttpRequest::get_Value('pagination_page')*$this->limit)-$this->limit) : 0;
        $this->paginationLink = 'index.php?page='.CHttpRequest::get_Value('page');

        // Append filter and options from submited form values
        foreach(CHttpRequest::getAll() as $key => $value) {
            if(strpos($key, 'filter_') !== false) {
                $this->paginationLink .= "&$key=$value";
            }
        }
    }
    
    /**
     * getOffset
     *
     * @return int offset
     */
    public function getOffset()
    {
        return $this->offset;
    }
         
    /**
     * getLimit
     *
     * @return int limit
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * paginate
     *
     * @param CDBResult $dbResult
     * @param int $rowsTotal total row(s) in the table
     * @param int $rowsFiltered total filtered rows in the table
     * @return array
     */
    public function paginate($dbResult, $rowsTotal, $rowsFiltered)
    {        
        $this->currentView->assign('rowcount', $rowsTotal);
        $this->currentView->assign('count', $rowsFiltered);
        $this->paginationMax = intval($rowsFiltered / $this->limit)+1;
        $this->currentView->assign('pagination_link', $this->paginationLink);

        if (!is_null(filter_input(INPUT_GET, 'pagination_page'))) {
            $this->paginationCurrent = CHttpRequest::get_Value('pagination_page');
            $this->currentView->assign('pagination_current', $this->paginationCurrent);

            // if requested pagination page is the first one
            if (CHttpRequest::get_Value('pagination_page') == "1") {
                $this->currentView->assign('first', 'disabled');
            } else {
                $this->currentView->assign('first', '');
            }

            // if requested pagination page is the last one
            if (CHttpRequest::get_Value('pagination_page') == $this->paginationMax) {
                $this->currentView->assign('last', 'disabled');
            } else {
                $this->currentView->assign('last', '');
            }

            // if requested pagination page is in first 4 pages, disable previous button
            if (CHttpRequest::get_Value('pagination_page') < $this->paginationSteps) {
                $this->currentView->assign('previous_enabled', 'disabled');
            } else {
                $this->currentView->assign('previous_enabled', '');
            }

            // if requested pagination page is within $this->paginationSteps, disable next link
            if (CHttpRequest::get_Value('pagination_page') > ($this->paginationMax - $this->paginationSteps)) {
                $this->currentView->assign('next_enabled', 'disabled');
            }else {
                $this->currentView->assign('next_enabled', '');
            }

            $this->currentView->assign('previous', CHttpRequest::get_Value('pagination_page')-$this->paginationSteps);
            $this->currentView->assign('next', CHttpRequest::get_Value('pagination_page')+$this->paginationSteps);
        } else {
            $this->currentView->assign('pagination_current', $this->paginationCurrent);
            $this->currentView->assign('previous_enabled', 'disabled');
            $this->currentView->assign('previous', '1');
            $this->currentView->assign('next', $this->paginationSteps+1);

            if($this->paginationMax == 1) {
                $this->currentView->assign('next_enabled', 'disabled');
                $this->currentView->assign('last', 'disabled');
            }else {
                $this->currentView->assign('next_enabled', '');
                $this->currentView->assign('last', '');
            }
            
            $this->currentView->assign('first', 'disabled');
            
        }

        // these lines below are buggy :(
        if($this->paginationMax == $this->paginationCurrent ) {
            $this->currentView->assign('pagination_range', ($this->offset) . ' to '. $rowsFiltered);
        }else {
            $this->currentView->assign('pagination_range', ($this->offset) . ' to '. ($this->offset+$this->limit));
        }
    
        $this->currentView->assign('pagination_max', $this->paginationMax);
        $this->currentView->assign('pagination_steps', $this->paginationSteps);

        return $dbResult->fetchAll();
    }
}
