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
 class Config {
 
	private $config_params;
	private $config;
	private $catalogs = array();
	
	function __construct()
	{
		global $config;
		$this->config = $config;
	}
	
	public function loadConfig()
	{
		global $config;
		
		if( is_readable( CONFIG_FILE ) )
			include_once( CONFIG_FILE );
		else {
			throw new CErrorHandler( "Config file not found or bad file permissions" );
			return;
		}
		
		// Checking options and database parameters
		if( !array_key_exists('0', $config ) ) {
			throw new CErrorHandler( "At least one catalog should be defined in the configuration" );
			return;
		}
			
		// Loading catalog(s) parameter(s)
		if( is_array($config) && !empty($config) ) {
			foreach( $config as $parameter => $value )
			{
				if( is_array($value) )		// Parsing database section
					array_push( $this->catalogs, $value );
			}
		}else {
			throw new CErrorHandler( "Missing parameters in the config file" );
			return;
		}
	}
	
	public function get_Config_param( $param )
	{
		global $config;
				
		if( isset( $config[$param] ) )
			return $config[$param];
		else
			return false;
	}
	
	public function Get_Catalogs()
	{
		$result = array();
		foreach( $this->catalogs as $db )
			array_push( $result, $db['label']);
		
		return $result;
	}
	
	public function Count_Catalogs()
	{
		return count( $this->catalogs );
	}
	
	public function getDSN( $catalog_id ) 
	{
		$dsn = '';
		
		switch( $this->catalogs[$catalog_id]['db_type'] )
		{
			case 'mysql':
			case 'pgsql':
				$dsn  = $this->catalogs[$catalog_id]['db_type'] . ':';
				$dsn .= 'dbname=' . $this->catalogs[$catalog_id]['db_name'] . ';';
				$dsn .= 'host=' . $this->catalogs[$catalog_id]['host'];
			break;
			case 'sqlite':
				$dsn  = $this->catalogs[$catalog_id]['db_type'] . ':' . $this->catalogs[$catalog_id]['db_name'];
			break;
		}
		
		return $dsn;		
	}
	
	public function getUser( $catalog_id )
	{
		return $this->catalogs[$catalog_id]['login'];
	}
	
	public function getPwd( $catalog_id )
	{
		return $this->catalogs[$catalog_id]['password'];
	}	
 } // end class Config
 
?>
