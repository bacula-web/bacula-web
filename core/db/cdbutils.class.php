<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2017, Davide Franco			                               |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */

class CDBUtils
{
    private static $result_count;

    private function __construct()
    {
    }

    public static function isConnected($PDO_connection)
    {
        $pdo_connection    = null;
        
     // If MySQL of postGreSQL
        switch (CDB::getDriverName()) {
            case 'mysql':
            case 'pgsql':
                $pdo_connection = self::getConnectionStatus($PDO_connection);
                break;
            default:
             // We assume that the user running Apache has access to the SQLite database file (to be improved)
                $pdo_connection = true;
                break;
        }
        
     // Test connection status
        if ($pdo_connection != false) {
            return true;
        } else {
            return false;
        }
    }

    // ==================================================================================
    // Function: 	getConnectionStatus()
    // Parameters:	$PDO_connection (valid pdo connection)
    // Return:	true if the PDO connection is valid or false
    // ==================================================================================

    public static function getConnectionStatus($PDO_connection)
    {
        // If MySQL of postGreSQL
        if (CDB::getDriverName() != 'sqlite') {
            return $PDO_connection->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        } else {
            return 'N/A';
        }
    }
    
    public function countResult()
    {
        return self::$result_count;
    }

    public static function runQuery($query, $db_link)
    {
        $statment    = $db_link->prepare($query);

        if ($statment == false) {
            throw new PDOException("Failed to prepare PDOStatment <br />$query");
        }
            
        $result     = $statment->execute();

        if (is_null($result)) {
            throw new PDOException("Failed to execute PDOStatment <br />$query");
        }
    
        return $statment ;
    }
}
