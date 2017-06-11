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

class CModel
{
   protected $db_link;
   protected $cdb;
   protected $driver;

   public function __construct( $catalog_id = 0 ) {
      $user = '';
      $pwd  = '';
      $this->cdb  = new CDB();

      // Open config file
      FileConfig::open(CONFIG_FILE);

      // Create PDO connection to database
      $this->driver = FileConfig::get_Value('db_type', $catalog_id);
      $dsn = FileConfig::get_DataSourceName( $catalog_id );
      
      if ($this->driver != 'sqlite') {
         $user = FileConfig::get_Value('login', $catalog_id);
         $pwd  = FileConfig::get_Value('password', $catalog_id);
      }

      switch ($this->driver) {
         case 'mysql':
         case 'pgsql':
            $this->db_link = $this->cdb->connect($dsn, $user, $pwd);
         break;
         case 'sqlite':
            $this->db_link = $this->cdb->connect( $dsn );
      } // end switch
   }
 
    // ==================================================================================
    // Function: 	count()
    // Parameters:	$tablename
    //				$filter (optional)
    // Return:		return row count for one table
    // ==================================================================================

    protected function count($tablename, $filter = null)
    {
        $fields        = array( 'COUNT(*) as row_count' );

     // Prepare and execute query
        $statment   = CDBQuery::get_Select(array( 'table' => $tablename, 'fields' => $fields, $filter));
        $result     = $this->run_query($statment);

        $result     = $result->fetch();
        
        // If SQL count result is null, return 0 instead (much better when plotting data)
        if( is_null($result['row_count']) ) {
        	return 0;
        }else {
        	return $result['row_count'];
        }
    }

    // ==================================================================================
    // Function: 	getServerTimestamp()
    // Parameters:   none	
    // Return:		   return database server timestamp
    // ==================================================================================
    
   public function getServerTimestamp() {
      // Different query for SQlite
      if ($this->get_driver_name() == 'sqlite') {
         $statment = "SELECT datetime('now') as currentdatetime";
      } else {
         $statment = 'SELECT now() as currentdatetime';
      }
           
      $result = $this->run_query($statment);
      $result = $result->fetch();
           
      // Return timestamp
      return strtotime($result['currentdatetime']);
   } // end function getServerTimestamp()

   public function get_driver_name() {
      return $this->cdb->getDriverName();
   }

   public function run_query($query) {
      // Prepare SQL query
      $statment    = $this->db_link->prepare($query);

      if ($statment == false) {
         throw new PDOException("Failed to prepare PDOStatment <br />$query");
      }

      $result     = $statment->execute();

      if (is_null($result)) {
         throw new PDOException("Failed to execute PDOStatment <br />$query");
      }   

      return $statment;
   }

   // ==================================================================================
   // Function:     getConnectionStatus()
   // Parameters:   none 
   // Return:       PDO connection status (string)
   // ==================================================================================
   
   public function getConnectionStatus()
   {
      // If MySQL of postGreSQL
      if ($this->get_driver_name() != 'sqlite') {
         return $this->db_link->getAttribute(PDO::ATTR_CONNECTION_STATUS);
      } else {
         return 'N/A';
      }
   }   

   // ==================================================================================
   // Function:      isConnected()
   // Parameters:    none
   // Return:        true if PDO connection is ok, false otherwise
   // ==================================================================================

   public function isConnected()
   {
      $pdo_connection = true;

      // If MySQL of postGreSQL
      switch ($this->get_driver_name()) {
         case 'mysql':
         case 'pgsql':
         $pdo_connection = $this->getConnectionStatus();
         break;
      default:
         // We assume that the user running Apache has access to the SQLite database file (must be improved)
         $pdo_connection = true;
      }

      // Test connection status
      if ($pdo_connection != false) {
         return true;
      } else {
         return false;
      }
   }
}
