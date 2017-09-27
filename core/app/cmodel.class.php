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
   protected $parameters;

   public function __construct() {
      $user = '';
      $pwd  = '';
      $this->cdb  = new CDB();

      // Open config file
      FileConfig::open(CONFIG_FILE);

      // Create PDO connection to database
      $catalog_id = $_SESSION['catalog_id'];
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

   /*
     Function: run_query	
     Parameters:  $query
     Return:	   PDO_Statment
   */

   public function run_query($query) {

      // Prepare PDO statment
      $statment    = $this->db_link->prepare($query);

      if ($statment == false) {
         throw new PDOException("Failed to prepare PDOStatment <br />$query");
      }

      // Bind PHP variables with named placeholders
      if( isset($this->parameters) ) {
         foreach( $this->parameters as $name => $value) {
            if( $statment->bindParam(":$name", $value) != TRUE ) {
               throw new PDOException("Something went wrong with PDO_Statment::bindParam()");
            }
         }
      }

      $result = $statment->execute();

      if ($result == FALSE) {
         throw new PDOException("Failed to execute PDOStatment <br />$query");
      }else{
         // Erase parameters to bind to avoid problem on next CModel::run_query() call
         $this->parameters = null;
         return $statment;
      }
   }

   /*
     Function: addParameter
     Parameters:  $name
                  $value
     Return:	   nothing
   */

   public function addParameter($name, $value) {
      $this->parameters[$name] = $value;
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
