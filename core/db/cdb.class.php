<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2019, Davide Franco			                            |
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
    private $connection;

    // ==================================================================================
    // Function: 	__construct()
    // Parameters: 	none
    // Return:		none
    // ==================================================================================

    public function __construct()
    {
    }

    // ==================================================================================
    // Function: 	connect()
    // Parameters: 	none
    // Return:		valid PDO connection
    // ==================================================================================

    public function connect($dsn, $user = null, $password = null)
    {
        $this->connection = new PDO($dsn, $user, $password);

        // Set PDO connection options
        $this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('CDBResult', array($this)));
        
        // MySQL connection specific parameter
        if ($this->getDriverName() == 'mysql') {
            $this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }
        
        return $this->connection;
    }

    // ==================================================================================
    // Function: 	getDriverName()
    // Parameters: 	none
    // Return:		driver name (eg: mysql, pgsql, sqlite, etc.)
    // ==================================================================================

    public function getDriverName()
    {
        return $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    // ==================================================================================
    // Function: 	getServerVersion()
    // Parameters: 	none
    // Return:		database server version
    // ==================================================================================

    public function getServerVersion()
    {
        $server_version = $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
        $server_version = explode(':', $server_version);
        return $server_version[0];
    }

}
