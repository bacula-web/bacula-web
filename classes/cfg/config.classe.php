<?php

 class BW_Config {
 
	private $config;
	private $catalogs = array();
	
	function __construct()
	{
			if( !$this->Check_Config_file() )
				return true;
	}
	
	public function Check_Config_file()
	{
		// Check if config file exist and is readable
		return is_readable( CONFIG_FILE );
	}
	
	public function Get_Config()
	{
		$this->config = parse_ini_file( $this->config_file, true );
		
		if( !$this->config == false ) {
			// Loading database connection information
			foreach( $this->config as $parameter => $value )
			{
				if( is_array($value) )		// Parsing database section
					array_push( $this->catalogs, $value );
			}
			return true;
		}else
			return false;		
	}
	
	public function Get_Config_Params( $params = array() )
	{
		if( isset( $this->config[$param] ) )
			return $this->config[$param];
		else
			return false;
	}
	
	public function Count_Catalogs()
	{
		return count( $this->catalogs );
	}
	
	public function Get_Dsn( $catalog_id )
	{
		
	}
		
	function __destruct()
	{
		
	}
	
 } // end classe BW_Config
 
?>
