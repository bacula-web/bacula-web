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

class CDB
{
    private static $connection;
    private $options;
    private $result;

    // ==================================================================================
    // Function: 	__construct()
    // Parameters: 	none
    // Return:		none
    // ==================================================================================

    private function __construct()
    {
    }

    // ==================================================================================
    // Function: 	connect()
    // Parameters: 	none
    // Return:		valid PDO connection
    // ==================================================================================

    public static function connect($dsn, $user = null, $password = null)
    {
        try {
            if (is_null(self::$connection)) {
                self::$connection = new PDO($dsn, $user, $password);
            }
        } catch (PDOException $e) {
            CErrorHandler::displayError($e);
        }
        
        return self::$connection;
    }

    // ==================================================================================
    // Function: 	getDriverName()
    // Parameters: 	none
    // Return:		driver name (eg: mysql, pgsql, sqlite, etc.)
    // ==================================================================================

    public static function getDriverName()
    {
        return self::$connection->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    // ==================================================================================
    // Function: 	getServerVersion()
    // Parameters: 	none
    // Return:		database server version
    // ==================================================================================

    public static function getServerVersion()
    {
        $server_version = self::$connection->getAttribute(PDO::ATTR_SERVER_VERSION);
        $server_version = explode(':', $server_version);
        return $server_version[0];
    }

    // ==================================================================================
    // Function: 	getServerTimestamp()
    // Parameters: 	none
    // Return:		database server current timestamp
    // ==================================================================================

    public static function getServerTimestamp()
    {
        if (!is_null(self::$connection)) {
            // Different query for SQlite
            if (self::getDriverName() == 'sqlite') {
                $statment = "SELECT datetime('now') as CurrentDateTime";
            } else {
                $statment = 'SELECT now() as CurrentDateTime';
            }

            $result = CDBUtils::runQuery($statment, self::$connection);
            $result = $result->fetch();
            
            // Return timestamp
            return strtotime($result['currentdatetime']);
        } else {
            throw new Exception("No connection to database");
        }
    }
}
