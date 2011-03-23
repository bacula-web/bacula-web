<?php

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
