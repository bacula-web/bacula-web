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

    public static function getDriverName( $db_link ) {
		return $db_link->getAttribute(PDO::ATTR_DRIVER_NAME);
	}
	
	public static function isConnected() {
	
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
		
		self::$result_count = $statment->rowCount();
		
		if( self::$result_count > 1) {
			return $statment->fetchAll();
		}else {
			return $statment->fetch();
		}
    }

}

?>
