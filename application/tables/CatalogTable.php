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

namespace App\Tables;

use Core\Db\Table;
use App\Libs\FileConfig;
use Core\Db\CDBQuery;
use Core\Utils\CUtils;
use Exception;

class CatalogTable extends Table
{
    protected $tablename = 'Version';
    private $dbVersionId = '';

    /**
     * @param int $catalogId
     * @return string Database size in human format
     * @throws Exception
     */

    public function get_Size(int $catalogId): string
    {
        $dbName = FileConfig::get_Value('db_name', $catalogId);

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

                    $result  = $this->run_query(CDBQuery::get_Select($statement, $this->pdo));
                    $dbSize = $result->fetch();
                    $dbSize = $dbSize['dbsize'] * 1024 * 1024;
                    return CUtils::Get_Human_Size($dbSize);
                } else {
                    return 'Not supported (' . $this->db->getServerVersion() . ')';
                }
                break;
            case 'pgsql':
                $statement = "SELECT pg_database_size('$dbName') AS dbsize";
                $result = $this->run_query($statement);
                $dbSize = $result->fetch();
                return CUtils::Get_Human_Size($dbSize['dbsize']);
            case 'sqlite':
                $dbSize = filesize(FileConfig::get_Value('dbName', $catalogId));
                return CUtils::Get_Human_Size($dbSize);
            default:
                throw new Exception('Catalog db size error: Unsupported PDO driver' . $this->db->getDriverName());
        }
    }

    /**
     * Return Bacula catalog id
     * @a
     * author Tom Hodder <tom@limepepper.co.uk>
     * @return string VersionId value from Bacula catalog
     * @throws Exception
     */
    public function getCatalogVersion(): string
    {
        $sqlQuery = CDBQuery::get_Select(array('table' => $this->tablename,
            'fields' => array('VersionId'),
            'limit' => array('count' => 1, 'offset' => 0)
        ), $this->db->getDriverName());

        $result = $this->run_query($sqlQuery);
        $this->dbVersionId = intval($result->fetchColumn());

        return $this->dbVersionId;
    }
}
