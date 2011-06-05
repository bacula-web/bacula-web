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
 class BW_Config {
 
	private $config_params;
	private $config;
	private $catalogs = array();
	
	function __construct()
	{
		global $config;
		$this->config = $config;
	}
	
	public function Check_Config_file()
	{
		// Check if config file exist and is readable
		return is_readable( CONFIG_FILE );
	}
	
	public function Load_Config()
	{
		global $config;
		
		if( is_array($config) && !empty($config) ) {
			// Loading database connection information
			foreach( $config as $parameter => $value )
			{
				if( is_array($value) )		// Parsing database section
					array_push( $this->catalogs, $value );
			}
			return true;
		}else {
			return false;		
		}
	}
	
	public function Get_Config_Param( $param )
	{
		if( isset( $config[$param] ) )
			return $config[$param];
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
	
	public function Get_Dsn( $catalog_id )
	{
		// Construct a valid dsn
		$dsn = array();
        $dsn['hostspec'] = $this->catalogs[$catalog_id]["host"];
        $dsn['username'] = $this->catalogs[$catalog_id]["login"];
		$dsn['password'] = $this->catalogs[$catalog_id]["password"];
		$dsn['database'] = $this->catalogs[$catalog_id]["db_name"];
		$dsn['phptype']  = $this->catalogs[$catalog_id]["db_type"];
		return $dsn;
	}
		
	function __destruct()
	{
		
	}
	
 } // end classe BW_Config
 
?>
