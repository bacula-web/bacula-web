<?php

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

/**
 * CDBPagination helps creating pagination from database queries results
 *
 * @author  Davide Franco <bacula-dev@dflc.ch>
 *
 */

namespace Core\Db;

use App\Libs\FileConfig;
use Core\App\View;
use Core\Helpers\Sanitizer;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class CDBPagination
{
    private $totalRow = 0;
    private $filteredRow = 0;
    private $currentView;
    private $offset;
    private $limit;

    /**
     * Maximum number of pagination page
     * @var int
     */
    private $paginationMax;
    private $paginationSteps = 1;
    private $paginationLink;
    private $paginationCurrent = 1;
    private $request;

    /**
     * @param View $view
     * @throws Exception
     */
    public function __construct(View $view)
    {
        $this->request = Request::createFromGlobals();
        $this->currentView = $view;

        $this->limit = (FileConfig::get_Value('rows_per_page') !== null) ? FileConfig::get_Value('rows_per_page') : 25;

        // get pagination page from GET
        $current_page = (int) $this->request->query->get('pagination_page', 1);
        if ($current_page === 1) {
            $this->offset = 0;
        } else {
            $this->offset = ($current_page - 1) * $this->limit;
        }

        $this->paginationLink = 'index.php?page=' . Sanitizer::sanitize($this->request->query->getAlpha('page'));

        // Append filter and options from submited form values
        // from POST
        foreach ($this->request->request->all() as $key => $value) {
            if (strpos($key, 'filter_') !== false) {
                $this->paginationLink .= "&$key=$value";
            }
        }

        // from GET
        foreach ($this->request->query->all() as $key => $value) {
            if (strpos($key, 'filter_') !== false) {
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
    public function getLimit(): int
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
        $this->currentView->set('rowcount', $this->totalRow);

        $this->filteredRow = $table->select($queryCount, $params)[0]['row_count'];
        $this->paginationMax = ceil($this->filteredRow / $this->limit);

        $this->currentView->set('count', $this->filteredRow);

        $this->currentView->set('pagination_link', $this->paginationLink);

        if ($this->request->query->has('pagination_page')) {
                $this->paginationCurrent = $this->request->query->getInt('pagination_page');
                $this->currentView->set('pagination_current', $this->paginationCurrent);

                // if requested pagination page is the first one
            if ($this->request->query->get('pagination_page') == "1") {
                $this->currentView->set('first', 'disabled');
            } else {
                $this->currentView->set('first', '');
            }

                // if requested pagination page is the last one
            if ($this->request->query->getInt('pagination_page') == $this->paginationMax) {
                $this->currentView->set('last', 'disabled');
            } else {
                $this->currentView->set('last', '');
            }

                // if requested pagination page is in first 4 pages, disable previous button
            if ($this->request->query->getInt('pagination_page') < $this->paginationSteps) {
                $this->currentView->set('previous_enabled', 'disabled');
            } else {
                $this->currentView->set('previous_enabled', '');
            }

                // if requested pagination page is within $this->paginationSteps, disable next link
            if ($this->request->query->getInt('pagination_page') > ($this->paginationMax - $this->paginationSteps)) {
                $this->currentView->set('next_enabled', 'disabled');
            } else {
                $this->currentView->set('next_enabled', '');
            }

                $this->currentView->set('previous', $this->request->query->getInt('pagination_page') - $this->paginationSteps);
                $this->currentView->set('next', $this->request->query->getInt('pagination_page') + $this->paginationSteps);
        } else {
            $this->currentView->set('pagination_current', $this->paginationCurrent);
            $this->currentView->set('previous_enabled', 'disabled');
            $this->currentView->set('previous', '1');
            $this->currentView->set('next', $this->paginationSteps + 1);

            if ($this->paginationMax == 1) {
                $this->currentView->set('next_enabled', 'disabled');
                $this->currentView->set('last', 'disabled');
            } else {
                $this->currentView->set('next_enabled', '');
                $this->currentView->set('last', '');
            }

            $this->currentView->set('first', 'disabled');
        }

        // these lines below are buggy :(
        if ($this->paginationMax == $this->paginationCurrent) {
            $this->currentView->set('pagination_range', ($this->offset) . ' to ' . $this->filteredRow);
        } else {
            $this->currentView->set('pagination_range', ($this->offset) . ' to ' . ($this->offset + $this->limit));
        }

        $this->currentView->set('pagination_max', $this->paginationMax);
        $this->currentView->set('pagination_steps', $this->paginationSteps);

        return $table->select($query, $params);
    }
}
