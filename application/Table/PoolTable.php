<?php

declare(strict_types=1);

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

namespace App\Table;

use Core\Db\Table;
use Core\Db\CDBQuery;
use Exception;

class PoolTable extends Table
{
    protected ?string $tablename = 'Pool';

    /**
     * @var bool $hideEmptyPools
     * @return mixed
     * @throws Exception
     */
    public function getPools(bool $hideEmptyPools = true)
    {
        $where    = null;
        $orderby  = 'Name';

        if ($hideEmptyPools) {
            $where[] = "$this->tablename.NumVols > 0";
        }

        $fields = [ 'poolid', 'name', 'numvols' ];

        $query = CDBQuery::get_Select(array( 'table' => $this->tablename,
            'fields' => $fields,
            'where' => $where,
            'orderby' => $orderby ));

        return $this->select($query);
    }
}
