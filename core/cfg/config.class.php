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
			throw new Exception( "Config file not found or bad file permissions" );
			return;
		}
		
		// Checking options and database parameters
		if( !array_key_exists('0', $config ) ) {
			throw new Exception( "At least one catalog should be defined in the configuration" );
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
			throw new Exception( "Missing parameters in the config file" );
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
	
	public function get_DSN( $catalog_id ) 
	{
		$dsn = '';
		$current_catalog = $this->catalogs[$catalog_id];
		
		switch( $current_catalog['db_type'] )
		{
			case 'mysql':
			case 'pgsql':
				$dsn  = $current_catalog['db_type'] . ':';
				$dsn .= 'dbname=' . $current_catalog['db_name'] . ';';
				$dsn .= 'host=' . $current_catalog['host'] . ';';
				if(isset($current_catalog['db_port']) and !empty($current_catalog['db_port']))
					$dsn .= 'port=' . $current_catalog['db_port'] . ';';
			break;
			case 'sqlite':
				$dsn  = $current_catalog['db_type'] . ':' . $current_catalog['db_name'];
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
