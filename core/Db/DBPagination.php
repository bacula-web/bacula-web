<?php

/**
 * Copyright (C) 2010-present Davide Franco
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
use Core\Exception\ConfigFileException;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use function Core\Helpers\getRequestParams;

class DBPagination
{
    /**
     * @var int
     */
    private int $totalRow = 0;

    /**
     * @var int
     */
    private int $filteredRow = 0;

    /**
     * @var int
     */
    private int $offset;

    /**
     * @var int
     */
    private int $limit;

    /**
     * Maximum number of pagination page
     * @var int
     */
    private int $paginationMax;

    /**
     * @var int
     */
    private int $paginationCurrent;

    private ServerRequestInterface $request;

    /**
     * @param ServerRequestInterface $request
     * @throws ConfigFileException
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $parameters = getRequestParams($request);

        $this->limit = (FileConfig::get_Value('rows_per_page') !== null) ? FileConfig::get_Value('rows_per_page') : 25;

        $this->paginationCurrent = $parameters['page'] ?? 1;

        if ($this->paginationCurrent === 1) {
            $this->offset = 0;
        } else {
            $this->offset = ($this->paginationCurrent - 1) * $this->limit;
        }
    }

    public function getParams(): string
    {
        $params = '';

        // Append filter and options from submitted form values
        // from POST
        foreach ($this->request->getParsedBody() as $key => $value) {
            if (strpos($key, 'filter_') !== false) {
                $params .= "&$key=$value";
            }
        }

        // get pagination page from GET
        $parameters = $this->request->getQueryParams();

        foreach ($parameters as $key => $value) {
            if (strpos($key, 'filter_') !== false) {
                $params .= "&$key=$value";
            }
        }

        return $params;
    }

    /**
     * getOffset
     *
     * @return int offset
     */
    public function getOffset(): int
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

    public function getTotalRow(): int
    {
        return $this->totalRow;
    }

    public function getRows(): int
    {
        return $this->filteredRow;
    }

    /**
     * @param Table $table
     * @param string $query
     * @param string $queryCount
     * @param null $params
     * @return array|false
     * @throws Exception
     */
    public function paginate(Table $table, string $query, string $queryCount, $params = null)
    {
        $this->totalRow = $table->count();

        $this->filteredRow = $table->select($queryCount, $params)[0]['row_count'];
        $this->paginationMax = ceil($this->filteredRow / $this->limit);

        return $table->select($query, $params);
    }

    /**
     * @return string
     */
    public function getPaginationRange(): string
    {
        if ($this->paginationMax == $this->paginationCurrent) {
            return ($this->offset) . ' to ' . $this->filteredRow;
        } else {
            return ($this->offset) . ' to ' . ($this->offset + $this->limit);
        }
    }

    /**
     * @return int
     */
    public function getMaxPage(): int
    {
        return $this->paginationMax;
    }

    /**
     * @return int
     */
    public function getPreviousPage(): int
    {
        if ($this->paginationCurrent == 1)
        {
            return 1;
        }

        return $this->paginationCurrent -1;
    }

    /**
     * @return int
     */
    public function getNextPage(): int
    {
        if ($this->paginationCurrent !== $this->getMaxPage())
        {
            return $this->paginationCurrent + 1;
        }

        return $this->getMaxPage();
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return (int) $this->paginationCurrent;
    }

    /**
     * @return int
     */
    public function getPaginationStart(): int
    {
        if ($this->getMaxPage() < 5) {
            return 1;
        }

        if ($this->paginationCurrent >= ($this->getMaxPage()-5)) {
            return ($this->getMaxPage() -5);
        }
        return $this->paginationCurrent;
    }

    /**
     * @return int
     */
    public function getPaginationEnd(): int
    {
        if ($this->paginationCurrent >= ($this->getMaxPage()-5)) {
            return $this->getMaxPage();
        }

        if ($this->getMaxPage() <= 5) {
            return $this->getMaxPage();
        }
        return $this->paginationCurrent + 5;
    }
}
