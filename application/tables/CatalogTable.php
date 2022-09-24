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

class CatalogTable extends Table
{
    protected $tablename = 'Version';
    private $dbVersionId = '';
 
    // ==================================================================================
    // Function: 	get_Size()
    // Parameters:	$pdo_connection - valid PDO object instance
    // Return:		Database size
    // ==================================================================================

    public function get_Size($catalog_id)
    {
        $db_name    = FileConfig::get_Value('db_name', $catalog_id);
        
        switch ($this->cdb->getDriverName()) {
            case 'mysql':
             // Return N/A for MySQL server prior version 5 (no information_schemas)
                if (version_compare($this->cdb->getServerVersion(), '5.0.0') >= 0) {
                    // Prepare SQL statment
                    $statment = array( 'table'   => 'information_schema.TABLES',
                     'fields'  => array("table_schema AS 'database', (sum( data_length + index_length) / 1024 / 1024 ) AS 'dbsize'"),
                     'where'   => array( "table_schema = '$db_name'" ),
                     'groupby' => 'table_schema' );
                                       
                    $result        = $this->run_query(CDBQuery::get_Select($statment, $this->db_link));
                    $db_size    = $result->fetch();
                    $db_size     = $db_size['dbsize'] * 1024 * 1024;
                    return CUtils::Get_Human_Size($db_size);
                } else {
                    echo 'Not supported ('. $this->cdb->getServerVersion().') <br />';
                }
                break;
            case 'pgsql':
                $statment    = "SELECT pg_database_size('$db_name') AS dbsize";
                $result        = $this->run_query($statment);
                $db_size    = $result->fetch();
                return CUtils::Get_Human_Size($db_size['dbsize']);
            case 'sqlite':
                $db_size     = filesize(FileConfig::get_Value('db_name', $catalog_id));
                return CUtils::Get_Human_Size($db_size);
        }
    }

    /**
     * Return Bacula catalog id
     * @author Tom Hodder <tom@limepepper.co.uk>
     * @return string VersionId value from Bacula catalog
     *
     */
    public function getCatalogVersion()
    {
        $sqlQuery = CDBQuery::get_Select(array('table' => $this->tablename,
            'fields' => array('VersionId'),
            'limit' => array( 'count' => 1, 'offset' => 0)
        ), $this->cdb->getDriverName());
        
        $result = $this->run_query($sqlQuery);
        $this->dbVersionId = intval($result->fetchColumn()); 
        
        return $this->dbVersionId;
    }
}
