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

/**
 * CDBPagination helps creating pagination from database queries results
 *
 * @author  Davide Franco <bacula-dev@dflc.ch>
 *
 */

namespace Core\Db;

use App\Libs\FileConfig;
use Core\App\WebApplication;
use Core\App\CView;
use Core\Helpers\Sanitizer;

class CDBPagination
{
    private $totalRow = 0;
    private $filteredRow = 0;
    private $currentView;
    private $offset;
    private $limit;
    /**
     * Number max of pages
     * @var
     */
    private $paginationMax;
    private $paginationSteps = 4;
    private $paginationLink;
    private $paginationCurrent = 1;

    /**
     * @param CView $view
     * @throws Exception
     */
    public function __construct(CView $view)
    {
        $this->currentView = $view;
        $this->limit = (FileConfig::get_Value('rows_per_page') !== null) ? FileConfig::get_Value('rows_per_page') : 25;
        $this->offset = (WebApplication::getRequest()->query->has('pagination_page')) ? WebApplication::getRequest()->query->getInt('pagination_page') : 0;
        $this->paginationLink = 'index.php?page='. Sanitizer::sanitize( WebApplication::getRequest()->query->getAlpha('page'));

        // Append filter and options from submited form values
        foreach(WebApplication::getRequest()->query->all() as $key => $value) {
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
     * @param Table $table
     * @param $query
     * @param $queryCount
     * @param $params
     * @return array|false
     */
    public function paginate(Table $table, $query, $queryCount, $params = null)
    {
        $this->totalRow = $table->count();
        $this->currentView->assign('rowcount', $this->totalRow);

        $this->filteredRow = $table->select($queryCount, $params)[0]['row_count'];
        $this->paginationMax = ceil($this->filteredRow / $this->limit);

        $this->currentView->assign('count', $this->filteredRow);

        $this->currentView->assign('pagination_link', $this->paginationLink);

        if (WebApplication::getRequest()->query->has('pagination_page')) {
                $this->paginationCurrent = WebApplication::getRequest()->query->getInt('pagination_page');
                $this->currentView->assign('pagination_current', $this->paginationCurrent);

                // if requested pagination page is the first one
                if (WebApplication::getRequest()->query->get('pagination_page') == "1") {
                    $this->currentView->assign('first', 'disabled');
                } else {
                    $this->currentView->assign('first', '');
                }

                // if requested pagination page is the last one
                if (WebApplication::getRequest()->query->getInt('pagination_page') == $this->paginationMax) {
                    $this->currentView->assign('last', 'disabled');
                } else {
                    $this->currentView->assign('last', '');
                }

                // if requested pagination page is in first 4 pages, disable previous button
                if (WebApplication::getRequest()->query->getInt('pagination_page') < $this->paginationSteps) {
                    $this->currentView->assign('previous_enabled', 'disabled');
                } else {
                    $this->currentView->assign('previous_enabled', '');
                }

                // if requested pagination page is within $this->paginationSteps, disable next link
                if (WebApplication::getRequest()->query->getInt('pagination_page') > ($this->paginationMax - $this->paginationSteps)) {
                    $this->currentView->assign('next_enabled', 'disabled');
                }else {
                    $this->currentView->assign('next_enabled', '');
                }

                $this->currentView->assign('previous', WebApplication::getRequest()->query->getInt('pagination_page') - $this->paginationSteps);
                $this->currentView->assign('next', WebApplication::getRequest()->query->getInt('pagination_page')+$this->paginationSteps);
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
        if ($this->paginationMax == $this->paginationCurrent) {
            $this->currentView->assign('pagination_range', ($this->offset) . ' to '. $this->filteredRow);
        } else {
            $this->currentView->assign('pagination_range', ($this->offset) . ' to '. ($this->offset+$this->limit));
        }
    
        $this->currentView->assign('pagination_max', $this->paginationMax);
        $this->currentView->assign('pagination_steps', $this->paginationSteps);

        return $table->select($query, $params);
    }
}
