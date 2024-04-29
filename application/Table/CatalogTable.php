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

declare(strict_types=1);

namespace App\Table;

use Core\Db\Table;
use Core\Db\CDBQuery;
use Core\Exception\ConfigFileException;
use Core\Exception\DatabaseException;
use Core\Utils\CUtils;
use Exception;

class CatalogTable extends Table
{
    /**
     * @var string|null
     */
    protected ?string $tablename = 'Version';

    /**
     * @var int
     */
    private int $dbVersionId;

    /**
     * @param string $dbName
     * @param int $catalogId
     * @return string Database size in human format
     */
    public function get_Size(string $dbName, int $catalogId): string
    {
        switch ($this->db->getDriverName()) {
            case 'mysql':
                /**
                 * Return N/A for MySQL server prior version 5 (no information_schemas)
                 */
                if (version_compare($this->db->getServerVersion(), '5.0.0') >= 0) {
                    $statement = [
                        'table'   => 'information_schema.TABLES',
                        'fields'  => [
                            "table_schema AS 'database', (sum( data_length + index_length) / 1024 / 1024 ) AS 'dbsize'"
                        ],
                        'where'   => ["table_schema = '$dbName'"],
                        'groupby' => 'table_schema'
                    ];

                    $result = $this->run_query(CDBQuery::get_Select($statement, $this->db->getDriverName()));
                    $dbSize = $result->fetch();
                    $dbSize = $dbSize['dbsize'] * 1024 * 1024;
                } else {
                    throw new DatabaseException('Not supported (' . $this->db->getServerVersion() . ')');
                }
                break;
            case 'pgsql':
                $statement = "SELECT pg_database_size('$dbName') AS dbsize";
                $result = $this->run_query($statement);
                $dbSize = $result->fetch()['dbsize'];
                break;
            case 'sqlite':
                $dbSize = filesize(BW_ROOT . '/application/assets/protected/application.db');
                return CUtils::Get_Human_Size($dbSize);
            default:
                throw new DatabaseException(
                    'Catalog db size error: Unsupported PDO driver' . $this->db->getDriverName()
                );
        }

        return CUtils::Get_Human_Size((int)$dbSize);
    }
}
