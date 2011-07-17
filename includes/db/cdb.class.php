<?php
/* 
+-------------------------------------------------------------------------+
| Copyright 2010-2011, Davide Franco			                          |
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
	private $username;
	private $password;
	private $dsn;
	private $connection;
	private $options;
	
	private $result;
	private $result_nb;
	
	public function __construct( $dsn, $user, $password )
	{
		$this->dsn      = $dsn;
		$this->user     = $user;
		$this->password = $password;
		
		$this->options  = array( 	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
									PDO::ATTR_CASE => PDO::CASE_LOWER,
									PDO::ATTR_STATEMENT_CLASS => array('CDBResult', array($this) ) );
	}
	
	public function makeConnection()
	{
		if( !isset( $this->connection ) )
			$this->connection = new PDO( $this->dsn, $this->user, $this->password );
		
		if( !is_a( $this->connection, 'PDO' ) )
			throw new CDBError("Failed to make database connection");
		
		$this->setOptions(); 		
	}
	
	private function setOptions()
	{
		foreach( $this->options as $option => $value )
			$this->connection->setAttribute( $option, $value);
	}
	
	public function getDriver()
	{
		return $this->connection->getAttribute( PDO::ATTR_DRIVER_NAME );
	}
	
	public function runQuery( $query) 
	{
		$this->result = $this->connection->prepare( $query );
				
		if( !is_a( $this->result, 'CDBResult') )
			throw new PDOException("Failed to execute query <br />$query");
		
		if( !$this->result->execute() )
			throw new PDOException("Failed to execute query <br />$query");
		
		return $this->result;
	}
	
	public function countResult()
	{
		return $this->result_nb;
	}
}
?>
