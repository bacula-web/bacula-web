<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2012, Davide Franco			                          |
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

class CDBUtils {
	private static $result_count;

    private function __construct() {
    }

    public static function getDriverName( $PDO_connection ) {
		return $PDO_connection->getAttribute(PDO::ATTR_DRIVER_NAME);
	}
	
	public static function isConnected( $PDO_connection ) {
		// if MySQL of postGreSQL
		if( self::getDriverName( $PDO_connection ) != 'sqlite' ) {
			$pdo_connection = self::getConnectionStatus($PDO_connection);
			$str = 'Connection OK';
		}else {
			// Assume that the SQLite database file is readable by Apache - will be improved
			return true;
		}

		if ( stripos( $pdo_connection, $str ) === false )
			return false;
		else
			return true;	
	}
	
	public static function getConnectionStatus( $PDO_connection ) {
		// if MySQL of postGreSQL
		if( self::getDriverName( $PDO_connection ) != 'sqlite' ) {
			return $PDO_connection->getAttribute( PDO::ATTR_CONNECTION_STATUS );
		}else {
			return '';
		}
	}
	
	public function countResult() {
		return self::$result_count;
	}

    public static function runQuery( $query, $db_link ) {
        $result 	  = null;
		$result_count = 0;
		$statment	  = null;
				
		try {
			$statment	= $db_link->prepare($query); 
			
			if( $statment == FALSE )
				throw new PDOException("Failed to prepare PDOStatment <br />$query");
			
			$result 	= $statment->execute();			
            if ( is_null($result) )
                throw new PDOException("Failed to execute PDOStatment <br />$query");
				
        } catch (PDOException $e) {
            CErrorHandler::displayError($e);
        }
		
		return $statment ;
    }

}

?>
