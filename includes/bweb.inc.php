<?php
/* 
+-------------------------------------------------------------------------+
| Copyright (C) 2004 Juan Luis Francés Jiménez							  |
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
class Bweb extends DB 
{
    var 	$driver;
	
	public  $tpl;
	public  $db_link;						// Database link
	
	private $config_file;					// Config filename
	private $config;						// Loaded config from bacula.conf
	private $catalogs = array();			// Catalog array
	public  $catalog_nb;
	private	$catalog_current_id;
	private $bwcfg;

    function __construct()
	{             
		$this->bwcfg = new Config();
		$dsn = array();
		
		// Loading configuration from config file
		$this->bwcfg->Load_Config();
		$this->catalog_nb = $this->bwcfg->Count_Catalogs();
		
		// Initialize smarty template classe
		$this->init_tpl();
		// Initialize smarty gettext function
		$this->init_gettext();
		
		// Check catalog id
		if( isset($_POST['catalog_id']) ) {
			$this->catalog_current_id = $_POST['catalog_id'];
			$_SESSION['catalog_id'] = $this->catalog_current_id;
		}
		elseif( isset( $_SESSION['catalog_id'] ) )
			$this->catalog_current_id = $_SESSION['catalog_id'];
		else {
			$this->catalog_current_id = 0;
			$_SESSION['catalog_id'] = $this->catalog_current_id;
		}

		$this->tpl->assign( 'catalog_current_id', $this->catalog_current_id );
		
		// Get DSN
		$dsn = $this->bwcfg->Get_Dsn( $this->catalog_current_id );
/*		
		// Connect to the database
		$options 		= array( 'portability' => DB_PORTABILITY_ALL );
		$this->db_link 	= $this->connect( $dsn, $options );
*/
		// Database connection
		try {
			$this->db_link = new CDB( 	$this->bwcfg->getDSN($this->catalog_current_id), 
										$this->bwcfg->getUser($this->catalog_current_id), 
										$this->bwcfg->getPwd($this->catalog_current_id)  );
			$this->db_link->makeConnection();
			
        }catch( PDOException $e ) {
			CDBError::raiseError( $e );
		}		
/*		
		if (DB::isError($this->db_link)) {
			$this->TriggerDBError('Unable to connect to catalog', $this->db_link);
		}else {
			$this->driver = $dsn['phptype'];                            
            		register_shutdown_function(array(&$this,'close') );
			$this->db_link->setFetchMode(DB_FETCHMODE_ASSOC);
		}
*/
		// Catalog selection		
		if( $this->catalog_nb > 1 ) {
			// Catalogs list
			$this->tpl->assign( 'catalogs', $this->bwcfg->Get_Catalogs() );			
			// Catalogs count
			$this->tpl->assign( 'catalog_nb', $this->catalog_nb );
		}
	}
                
    // Initialize Smarty template classe
	function init_tpl()
	{
		$this->tpl = new Smarty();
		
		$this->tpl->compile_check 	= true;
		$this->tpl->debugging 		= false;
		$this->tpl->force_compile 	= true;

		$this->tpl->template_dir 	= "./templates";
		$this->tpl->compile_dir 	= "./templates_c";
	}
	
	function init_gettext()
	{
		global $smarty_gettext_path;
		
		if ( function_exists("gettext") ) {
			require_once( BW_SMARTY_GETTEXT . "smarty_gettext.php" );     
			$this->tpl->register_block('t','smarty_translate');
        
			$language = $this->bwcfg->Get_Config_Param("lang");
			$domain = "messages";   
			putenv("LANG=$language"); 
			setlocale(LC_ALL, $language);
			bindtextdomain($domain,"./locale");
			textdomain($domain);
		}
		else {
			function smarty_translate($params, $text, &$smarty) {
                return $text;
			}
			$smarty->register_block('t','smarty_translate');
		}
	}
	
	function close() 
	{
		$this->db_link->disconnect();
    }      
       
	function GetDbSize() 
	{
		$db_size = 0;
		$query 	 = '';
		$result	 = '';
		
		switch( $this->db_link->getDriver() )
		{
			case 'mysql':
				$query  = "SELECT table_schema AS 'database', sum( data_length + index_length) AS 'dbsize' ";
				$query .= "FROM information_schema.TABLES ";
				$query .= "WHERE table_schema = 'bacula' ";
				$query .= "GROUP BY table_schema";
			break;
			case 'pgsql':
				$query  = "SELECT pg_database_size('bacula') AS dbsize";
			break;
			case 'sqlite':
				// Not yet implemented
				return "0 MB";
			break;
		}
		
		// Execute SQL statment
		try {
			$result  = $this->db_link->runQuery( $query );
			$db_size = $result->fetch();
			$db_size = CUtils::Get_Human_Size( $db_size['dbsize'] );
		}catch( PDOException $e) {
			CDBError::raiseError($e);
		}
		return $db_size;
		/*
		$result = $this->db_link->query( $query );
		
		if(! PEAR::isError( $result ) )
		{
			$db = $result->fetchRow();
			$database_size = $db['dbsize'];
		}else
			$this->TriggerDBError( 'Unable to get database size', $result);
		
		return CUtils::Get_Human_Size( $database_size );
		*/
	} // end function GetDbSize()
	
	public function Get_Nb_Clients()
	{
		$result    = '';
		$clients_nb = 0;
		$query     = "SELECT COUNT(*) AS nb_client FROM Client";
		
		try {
			$clients    = $this->db_link->runQuery( $query );
			$clients_nb = $clients->fetch();
		}catch( PDOException $e ) {
			CDBError::raiseError( $e );
		}
		
		return $clients_nb;
		/*
		$clients = $this->db_link->query("SELECT COUNT(*) AS nb_client FROM Client");
		if( PEAR::isError($clients) )
			$this->TriggerDBError("Unable to get client number", $clients );
		else
			return $clients->fetchRow( DB_FETCHMODE_ASSOC );
		*/
	}
  
	// Return an array of volumes ordered by poolid and volume name
	function GetVolumeList() 
	{
			$pools        = '';
			$volumes      = '';
			$volumes_list = array();
			$query        = "";
			$debug	      = false;
			
			// Get the list of pools id
			$query = "SELECT Pool.poolid, Pool.name FROM Pool ORDER BY Pool.poolid";
			
			try {
				foreach( $this->getPools() as $pool ) {
					switch( $this->db_link->getDriver() )
					{
						case 'sqlite':
						case 'mysql':
							$query  = "SELECT Media.volumename, Media.volbytes, Media.volstatus, Media.mediatype, Media.lastwritten, Media.volretention
									FROM Media LEFT JOIN Pool ON Media.poolid = Pool.poolid
									WHERE Media.poolid = '". $pool['poolid'] . "' ORDER BY Media.volumename";
						break;
						case 'pgsql':
							$query  = "SELECT media.volumename, media.volbytes, media.volstatus, media.mediatype, media.lastwritten, media.volretention
									FROM media LEFT JOIN pool ON media.poolid = pool.poolid
								    WHERE media.poolid = '". $pool['poolid'] . "' ORDER BY media.volumename";
						break;
					} // end switch
					
					$volumes = $this->db_link->runQuery($query);
				
					if( !array_key_exists( $pool['name'], $volumes_list) )
						$volumes_list[ $pool['name'] ] = array();
					
					foreach( $volumes->fetchAll() as $volume ) {
						if( $volume['lastwritten'] != "0000-00-00 00:00:00" ) {
							
							// Calculate expiration date if the volume is Full
							if( $volume['volstatus'] == 'Full' ) {
								$expire_date     = strtotime($volume['lastwritten']) + $volume['volretention'];
								$volume['expire'] = strftime("%Y-%m-%d", $expire_date);
							}else {
								$volume['expire'] = 'N/A';
							}
							
							// Media used bytes in a human format
							$volume['volbytes'] = CUtils::Get_Human_Size( $volume['volbytes'] );
						} else {
							$volume['lastwritten'] = "N/A";
							$volume['expire']      = "N/A";
							$volume['volbytes'] 	  = "0 KB";
						}
						
						// Odd or even row
						if( count(  $volumes_list[ $pool['name'] ] ) % 2)
							$volume['class'] = 'odd';

						// Add the media in pool array
						array_push( $volumes_list[ $pool['name']], $volume);
					} // end foreach volumes
				} // end foreach pools
				
			}catch(PDOException $e) {
				CDBError::raiseError($e);
			}
			
			return $volumes_list;
			/*
			$pools = $this->db_link->query( $query );
			
			if( PEAR::isError( $pools ) )
				$this->TriggerDBError("Failed to get pool list", $pools );

			
			while( $pool = $pools->fetchRow( DB_FETCHMODE_ASSOC ) ) {
				switch( $this->db_link->getDriver() )
				{
					case 'mysql':
						$query  = "SELECT Media.volumename, Media.volbytes, Media.volstatus, Media.mediatype, Media.lastwritten, Media.volretention
								   FROM Media LEFT JOIN Pool ON Media.poolid = Pool.poolid
								   WHERE Media.poolid = '". $pool['poolid'] . "' ORDER BY Media.volumename";
					break;
					case 'pgsql':
						$query  = "SELECT media.volumename, media.volbytes, media.volstatus, media.mediatype, media.lastwritten, media.volretention
								   FROM media LEFT JOIN pool ON media.poolid = pool.poolid
								   WHERE media.poolid = '". $pool['poolid'] . "' ORDER BY media.volumename";
					break;
					case 'sqlite':
						$query  = "";		// not yet implemented
					break;
					default:
					break;
				} // end switch
				
				$medias = $this->db_link->query( $query );

				if( PEAR::isError( $medias ) ) {
					$this->TriggerDBError("Failed to get media list for pool", $medias);
				}else {
					if( $debug ) echo "Found " . $medias->numRows() . " medias for pool " . $pool['name'] . " <br />";
				
					// Create array key for each pool
					if( !array_key_exists( $pool['name'], $volumes) )
					{
						$volumes[ $pool['name'] ] = array();
					}
					while( $media = $medias->fetchRow( DB_FETCHMODE_ASSOC ) ) {
						if( $debug ) {
							var_dump( $media );
						}
						// If the pool is empty (no volumes in this pool)
						if( $medias->numRows() == 0 ) {
							if( $debug ) echo "No media in pool " . $pool['name'] . "<br />";
						} else {
								if( $media['lastwritten'] != "0000-00-00 00:00:00" ) {
									// Calculate expiration date if the volume is Full
									if( $media['volstatus'] == 'Full' ) {
										$expire_date     = strtotime($media['lastwritten']) + $media['volretention'];
										$media['expire'] = strftime("%Y-%m-%d", $expire_date);
									}else {
										$media['expire'] = 'N/A';
									}
									// Media used bytes in a human format
									$media['volbytes'] = CUtils::Get_Human_Size( $media['volbytes'] );
								} else {
									$media['lastwritten'] = "N/A";
									$media['expire']      = "N/A";
									$media['volbytes'] 	  = "0 KB";
								}								
							
							// Odd or even row
							if( count(  $volumes[ $pool['name'] ] ) % 2)
									$media['class'] = 'odd';

							// Add the media in pool array
							array_push( $volumes[ $pool['name']], $media);
						}
					} // end while
				} // end if else
			} // end while
			
			*/

	} // end function GetVolumeList()
	
	public function countJobs( $start_timestamp, $end_timestamp, $status = 'ALL', $level = 'ALL', $jobname = 'ALL', $client = 'ALL' )
	{
		$query 			  = "";
		$where_interval	  = "";
		$where_conditions = array();
		$result			  = '';
		
		// Calculate sql query interval
		$start_date		= date( "Y-m-d H:i:s", $start_timestamp);	
		$end_date		= date( "Y-m-d H:i:s", $end_timestamp);
		
		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
				$query 		       .= "SELECT COUNT(*) AS job_nb FROM Job ";
				$where_conditions[] = "(EndTime BETWEEN '$start_date' AND '$end_date')";
			break;
			case 'pgsql':
				$query             .= "SELECT COUNT(*) AS job_nb FROM job ";
				$where_conditions[] = "(EndTime BETWEEN timestamp '$start_date' AND timestamp '$end_date')";
			break;
		}
		
		if( $status != 'ALL' ) {
			switch( strtolower($status) )
			{
				case 'running':
					array_pop( $where_conditions );
					$where_conditions[] = "JobStatus = 'R' ";
				break;
				case 'completed':
					$where_conditions[] = "JobStatus = 'T' ";
				break;
				case 'failed':
					$where_conditions[] = "JobStatus IN ('f','E') ";
				break;
				case 'canceled':
					$where_conditions[] = "JobStatus = 'A' ";
				break;
				case 'waiting':
					array_pop( $where_conditions );
					$where_conditions[] = "Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
				break;
			} // end switch
		}
		
		// Filter by status
		if( $level != 'ALL' )
			$where_conditions[] = "Level = '$level' ";
		
		// Construct SQL query
		foreach( $where_conditions as $k => $condition ) {
			if( $k == 0) {
				$query .= "WHERE $condition ";
			}else
				$query .= "AND $condition ";
		}
		
		// Execute the query
		try{
			$jobs   = $this->db_link->runQuery($query);
			$result = $jobs->fetch(); 
		}catch(PDOException $e) {
			CDBError::raiseError($e);
		}
		
		return $result['job_nb'];
		/*
		$jobs = $this->db_link->query( $query );
	
		if (!PEAR::isError( $jobs ) ) {
			$jobs = $jobs->fetchRow(); 
			return $jobs['job_nb'];
		}
		
		$this->TriggerDBError("Unable to get last $status jobs number from catalog", $jobs);
		*/
	}
	
	// Return the list of Pools in a array
	public function getPools()
	{
		$pools  = array();
		$result = '';
		
		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
				$query 		= "SELECT name, poolid FROM Pool";
			break;
			case 'pgsql':
				$query 		= "SELECT name, poolid FROM pool";
			break;
		}
		try{
			$result = $this->db_link->runQuery($query);
			foreach( $result->fetchAll() as $pool )
				$pools[] = $pool;
		}catch(PDOException $e) {
			CDBError::raiseError($e);
		}

		return $pools;
		/*
		$result 	= $this->db_link->query ( $query );

		if( !PEAR::isError( $result ) ) {
			while( $pool = $result->fetchRow() )
				$pools[] = $pool;
			return $pools;
		}
		$this->TriggerDBError( "Unable to get the pool list from catalog", $result );		
		*/
	}
	
	public function Get_BackupJob_Names()
	{
		$query 		= '';
		$result 	= '';
		$backupjobs = array();
		
		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
				$query 		= "SELECT name FROM Job GROUP BY name ORDER BY name";
			break;
			case 'pgsql':
				$query 		= "SELECT name FROM Job GROUP BY name ORDER BY name";
			break;
		}
		try {
			$result = $this->db_link->runQuery($query);
			foreach( $result->fetchAll() as $jobname )
				$backupjobs[] = $jobname['name'];
		}catch(PDOException $e) {
			CDBError::raiseError($e);
		}

		return $backupjobs;
		/*
		$result = $this->db_link->query( $query );
		
		if (PEAR::isError( $result ) ) {
			$this->TriggerDBError("Unable to get BackupJobs list from catalog", $result );
		}else{
			while( $backupjob = $result->fetchRow() ) {
				array_push( $backupjobs, $backupjob["name"] );
			}
			return $backupjobs;
		}
		*/
	}
	
	public function countVolumes( $pool_id = 'ALL' )
	{
		$result = null;
		$nb_vol = null;
		$query  = '';

		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
				$query 	= 'SELECT COUNT(*) as vols_count ';
				$query .= 'FROM Media ';
				if( $pool_id != 'ALL' )
					$query .= ' WHERE Media.poolid = ' . $pool_id;
			break;
			case 'pgsql':
				$query 	= 'SELECT COUNT(*) as vols_count ';
				$query .= 'FROM Media ';
				if( $pool_id != 'ALL' )
					$query .= ' WHERE media.poolid = ' . $pool_id;
			break;
		}
		
		// Execute sql query
		try {
			$result = $this->db_link->runQuery($query);
			$vols = $result->fetch();
		}catch( PDOException $e) {
			CDBError::raiseError($e);
		}
		
		return $vols['vols_count'];
		/*
		$res = $this->db_link->query( $query );
		
		if( !PEAR::isError( $res ) ) {
			$vols = $res->fetchRow( );
			return $vols['vols_count'];
		}	
		$this->triggerDBError( 'Unable to get volume number from pool', $res );
		*/
	}
	
	public function getStoredFiles( $start_timestamp, $end_timestamp, $job_name = 'ALL' )
	{
		$query = "";
		$start_date = date( "Y-m-d H:i:s", $start_timestamp);	
		$end_date   = date( "Y-m-d H:i:s", $end_timestamp);	
		
		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
					$query = "SELECT SUM(JobFiles) AS stored_files FROM Job ";
					$query .= "WHERE ( EndTime BETWEEN '$start_date' AND '$end_date' )";
			break;
			case 'pgsql':
					$query = "SELECT SUM(JobFiles) AS stored_files FROM job ";
					$query .= "WHERE ( endtime BETWEEN timestamp '$start_date' AND timestamp '$end_date' )";
			break;
		}
		
		if( $job_name != 'ALL' ) 
			$query .= " AND name = '$job_name'";
		
		// Execute query
		try {
			$result = $this->db_link->runQuery( $query );
			$result = $result->fetch();
		}catch( PDOException $e) {
			CDBError::raiseError($e);
		}
		
		if( isset($result['stored_files']) and !empty($result['stored_files']) )
			return $result['stored_files'];
		else
			return 0;
		/*
		$result = $this->db_link->query( $query );
		
		if( !PEAR::isError($result) ) {
			$result = $result->fetchRow();
			
			if( isset($result['stored_files']) and !empty($result['stored_files']) )
				return $result['stored_files'];
			else
				return 0;
		}else{
			$this->TriggerDBError("Unable to get protected files from catalog", $result);
		}
		*/
	}
	
	// Function: getStoredBytes
	// Parameters:
	// 		$start_timestamp: 	start date in unix timestamp format
	// 		$end_timestamp: 	end date in unix timestamp format
	//		$job_name:			optional job name
	
	public function getStoredBytes( $start_timestamp, $end_timestamp, $job_name = 'ALL' )
	{
		$query    		= '';
		$result			= '';
		$start_date		= date( "Y-m-d H:i:s", $start_timestamp);	
		$end_date		= date( "Y-m-d H:i:s", $end_timestamp);	
		
		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
				$query  = "SELECT SUM(JobBytes) as stored_bytes FROM Job ";
				$query .= "WHERE ( EndTime BETWEEN '$start_date' AND '$end_date' )";
			break;
			case 'pgsql':
				$query  = "SELECT SUM(jobbytes) as stored_bytes FROM job ";
				$query .= "WHERE ( endtime BETWEEN timestamp '$start_date' AND timestamp '$end_date' )";
			break;
		}

		if( $job_name != 'ALL' ) 
			$query .= " AND name = '$job_name'";
		
		// Execute SQL statment
		try {
			$result = $this->db_link->runQuery( $query );
			$result = $result->fetch();
		}catch(PDOException $e) {
			CDBError::raiseError( $e );
		}
		
		if( isset($result['stored_bytes']) and !empty($result['stored_bytes']) )
			return $result['stored_bytes'];
		else
			return 0;
		/*
		$result = $this->db_link->query( $query );
		
		// Testing query result
		if( PEAR::isError( $result ) ) {
			$this->TriggerDBError("Unable to get Job Bytes from catalog", $result );
		}else{
			$result = $result->fetchRow();
			
			if( !PEAR::isError($result) ) {
				if( isset($result['stored_bytes']) and !empty($result['stored_bytes']) )
					return $result['stored_bytes'];
				else
					return 0;
			}else
				$this->TriggerDBError( "Error fetching query result", $result);
		}
		*/
	}
	
	public function TriggerDBError( $message, $db_error)
	{
		echo 'Error: ' . $message . '<br />';
		echo 'Standard Message: ' . $db_error->getMessage() . '<br />';
		echo 'Standard Code: ' . $db_error->getCode() . '<br />';
		echo 'DBMS/User Message: ' . $db_error->getUserInfo() . '<br />';
		echo 'DBMS/Debug Message: ' . $db_error->getDebugInfo() . '<br />';
		exit;
	}
} // end class Bweb
?>
