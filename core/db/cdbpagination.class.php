<?php
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
    
    public function __construct($view)
    {
        $this->currentView = $view;
        $this->limit = (FileConfig::get_Value('row_per_page') !== null) ? FileConfig::get_Value('row_per_page') : 25;
        $this->offset = (CHttpRequest::get_Value('pagination_page') !==null) ? ((CHttpRequest::get_Value('pagination_page')*$this->limit)-$this->limit) : 0;
        $this->paginationLink = 'index.php?page='.CHttpRequest::get_Value('page');
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
     * @param  mixed $dbResult
     * @return void
     */
    public function paginate($dbResult)
    {
        $this->currentView->assign('count', $dbResult->count());
        $this->paginationMax = (int)($dbResult->count() / $this->limit);
        $this->currentView->assign('pagination_link', $this->paginationLink);

        if (!is_null(filter_input(INPUT_GET, 'pagination_page'))) {
            $this->currentView->assign('pagination_current', CHttpRequest::get_Value('pagination_page'));

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
            if (CHttpRequest::get_Value('pagination_page') > ($this->paginationMax-$this->paginationSteps)) {
                $this->currentView->assign('next_enabled', 'disabled');
            } else {
                $this->currentView->assign('next_enabled', '');
            }

            $this->currentView->assign('previous', CHttpRequest::get_Value('pagination_page')-$this->paginationSteps);
            $this->currentView->assign('next', CHttpRequest::get_Value('pagination_page')+$this->paginationSteps);
        } else {
            $this->currentView->assign('pagination_current', 1);
            $this->currentView->assign('previous_enabled', 'disabled');
            $this->currentView->assign('previous', '1');
            $this->currentView->assign('next', $this->paginationSteps+1);
            $this->currentView->assign('next_enabled', '');
            $this->currentView->assign('first', 'disabled');
            $this->currentView->assign('last', '');
        }

        $this->currentView->assign('pagination_range', ($this->offset).' to '. ($this->offset+$this->limit));
        $this->currentView->assign('pagination_max', $this->paginationMax);
        $this->currentView->assign('pagination_steps', $this->paginationSteps);

        return ['limit' => $this->limit, 'offset' => $this->offset];
    }
}
