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
     * @return string Database size in human format
     */
    public function get_Size(string $dbName): string
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

                    $result  = $this->run_query(CDBQuery::get_Select($statement, $this->db->getDriverName()));
                    $dbSize = $result->fetchColumn(1);
                    $dbSize = $dbSize * 1024 * 1024;
                    $dbSize = CUtils::GetHumanSize($dbSize);
                } else {
                    $dbSize = 'Not supported (' . $this->db->getServerVersion() . ')';
                }
                break;
            case 'pgsql':
                $statement = "SELECT pg_database_size('$dbName') AS dbsize";
                $result = $this->run_query($statement);
                $dbSize = CUtils::GetHumanSize($result->fetchColumn());
                break;
            case 'sqlite':
                $dbSize = CUtils::GetHumanSize((string) filesize(BW_ROOT . '/application/assets/protected/application.db'));
                break;
            default:
                $dbSize = 'Catalog database size not supported with driver ' . $this->db->getDriverName();
        }
        return $dbSize;
    }

    /**
     * Return Bacula catalog id
     * @author Tom Hodder <tom@limepepper.co.uk>
     * @return int VersionId value from Bacula catalog
     * @throws Exception
     */
    public function getCatalogVersion(): int
    {
        $sqlQuery = CDBQuery::get_Select(array('table' => $this->tablename,
            'fields' => array('VersionId'),
            'limit' => array('count' => 1, 'offset' => 0)
        ), $this->db->getDriverName());

        $result = $this->run_query($sqlQuery);

        return $this->dbVersionId = (int) $result->fetchColumn();
    }
}
