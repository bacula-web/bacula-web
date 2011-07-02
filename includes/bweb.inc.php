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
		
		// Connect to the database
		$options = array( 'portability' => DB_PORTABILITY_ALL );
		$this->db_link = $this->connect( $dsn, $options );
        
		if (DB::isError($this->db_link)) {
			$this->TriggerDBError("Unable to connect to catalog', $this->db_link);
		}else {
			$this->driver = $dsn['phptype'];                            
            register_shutdown_function(array(&$this,'close') );
			$this->db_link->setFetchMode(DB_FETCHMODE_ASSOC);
		}
		
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
		$database_size 	= 0;
		$query 			= "";
		
		switch( $this->driver )
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
		
		$result = $this->db_link->query( $query );
		
		if(! PEAR::isError( $result ) )
		{
			$db = $result->fetchRow();
			$database_size = $db['dbsize'];
		}else
			$this->TriggerDBError( 'Unable to get database size', $result);
		
		return CUtils::Get_Human_Size( $database_size );
	} // end function GetDbSize()
	
	public function Get_Nb_Clients()
	{
		$clients = $this->db_link->query("SELECT COUNT(*) AS nb_client FROM Client");
		if( PEAR::isError($clients) )
			$this->TriggerDBError("Unable to get client number", $clients );
		else
			return $clients->fetchRow( DB_FETCHMODE_ASSOC );
	}
  
	// Return an array of volumes ordered by poolid and volume name
	function GetVolumeList() {

			$volumes   = array();
			$query     = "";
			$debug	   = false;
			
			// Get the list of pools id
			$query = "SELECT Pool.poolid, Pool.name FROM Pool ORDER BY Pool.poolid";
			
			//$this->db_link->setFetchMode(DB_FETCHMODE_ASSOC);
			$pools = $this->db_link->query( $query );
			
			if( PEAR::isError( $pools ) )
				$this->TriggerDBError("Failed to get pool list", $pools );
			
			while( $pool = $pools->fetchRow( DB_FETCHMODE_ASSOC ) ) {
				switch( $this->driver )
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
			return $volumes;
	} // end function GetVolumeList()
	
	public function CountJobsbyLevel( $delay = LAST_DAY, $level = 'F' )
	{
		$end_date    = mktime();
		$start_date  = $end_date - $delay;
		
		$start_date  = date( "Y-m-d H:i:s", $start_date );
		$end_date    = date( "Y-m-d H:i:s", $end_date );
		
		$query 	 = "SELECT COUNT(JobId) as jobs FROM Job ";
		$query 	.= "WHERE (EndTime BETWEEN '$start_date' AND '$end_date') AND ";
		$query 	.= "Level = '$level' ";
		
		$result  = $this->db_link->query( $query );
		
		if (PEAR::isError( $result ) ) {
			$this->TriggerDBError( 'Unable to get number of jobs with ' . $level . ' status from catalog', $result);
		}else {
			$jobs = $result->fetchRow();
			return $jobs['jobs'];
		}
		
	}
	
	public function CountJobs( $delay = LAST_DAY, $status = 'any' )
	{
		$query 			= "SELECT COUNT(JobId) AS job_nb FROM Job ";
		$where_delay 	= "";
		$where_status	= "";
		
		// Interval condition for SQL query
		if( $delay != ALL ) {
			$end_date    = mktime();
			$start_date  = $end_date - $delay;
		
			$start_date  = date( "Y-m-d H:i:s", $start_date );
			$end_date    = date( "Y-m-d H:i:s", $end_date );
		
			$where_delay = "WHERE EndTime BETWEEN '$start_date' AND '$end_date' ";
		}
		
		if( $status != 'any' ) {
			switch( $status )
			{
				case 'completed':
					$where_status = "JobStatus = 'T' ";
				break;
				case 'failed':
					$where_status = "JobStatus IN ('f','E') ";
				break;
				case 'canceled':
					$where_status = "JobStatus = 'A' ";
				break;
				case 'waiting':
					$where_status = "JobStatus IN ('F','S','M','m','s','j','c','d','t') ";
				break;
			} // end switch
		}
		
		if( !empty($where_delay) )
			$query = $query . $where_delay . 'AND ' . $where_status;
		else {
			if( !empty($where_status) )
				$query = $query . 'WHERE ' . $where_status;
		}
			
		$jobs = $this->db_link->query( $query );
	
		if (PEAR::isError( $jobs ) ) {
			$this->TriggerDBError("Unable to get last $status jobs number from catalog", $jobs);
		}else {
			$jobs = $jobs->fetchRow( DB_FETCHMODE_ASSOC ); 
			return $jobs['job_nb'];
		}
	}
	
	// Return the list of Pools in a array
	public function Get_Pools_List()
	{
		$result    	= "";
		$query 		= "SELECT Name, PoolId FROM Pool";
		$result 	= $this->db_link->query ( $query );

		if( PEAR::isError( $result ) ) {
			$this->TriggerDBError( "Unable to get the pool list from catalog", $result );				
		}else {
			return $result;
		}
	}
	
	public function Get_BackupJob_Names()
	{
		$query 	= '';
		
		switch( $this->driver )
		{
			case 'sqlite':
			case 'mysql':
				$query 		= "SELECT name FROM Job GROUP BY name ORDER BY name";
			break;
			case 'pgsql':
				$query 		= "SELECT name FROM Job GROUP BY name ORDER BY name";
			break;
		}
		
		$backupjobs = array();
		
		$result = $this->db_link->query( $query );
		
		if (PEAR::isError( $result ) ) {
			$this->TriggerDBError("Unable to get BackupJobs list from catalog", $result );
		}else{
			while( $backupjob = $result->fetchRow() ) {
				array_push( $backupjobs, $backupjob["name"] );
			}
			return $backupjobs;
		}
	}
	
	// Return Jobs statistics for a specific interval such as
	// - Completed jobs number
	// - Failed jobs number
	// - Waiting jobs number
	// The returned values will be used by a Bgraph classe
	public function GetJobsStatistics( $type = 'completed', $delay = LAST_DAY )
	{
		$query 	= "";
		$where	= "";
		$jobs	= "";
		$label  = "";
		$res    = "";
		
		// Interval calculation
		$end_date   = mktime();
		$start_date = $end_date - $delay;
		
		$start_date = date( "Y-m-d H:i:s", $start_date );
		$end_date   = date( "Y-m-d H:i:s", $end_date );
		
		$interval_where = "(EndTime BETWEEN '$start_date' AND '$end_date') AND ";
		
		// Job status
		switch( $type )
		{
			case 'completed':
				$where = $interval_where . "JobStatus = 'T' ";
				$label = "Completed";
			break;
			case 'terminated_errors':
				$where = $interval_where . "JobStatus = 'E' ";
				$label = "Terminated with errors";
			break;
			case 'failed':
				$where = $interval_where . "JobStatus = 'f' ";
				$label = "Failed";
			break;
			case 'waiting':
				$where = "JobStatus IN ('F','S','M','m','s','j','c','d','t') ";
				$label = "Waiting";
			break;
			case 'created':
				$where = "JobStatus = 'C' ";
				$label = "Created but not running";
			break;
			case 'running':
				$where = "JobStatus = 'R' ";
				$label = "Running";
			break;
			case 'error':
				$where = $interval_where . "JobStatus IN ('e','f') ";
				$label = "Errors";
			break;
		}
		
		$query  = 'SELECT COUNT(JobId) AS count ';
		$query .= 'FROM Job ';
		$query .= "WHERE $where ";
		
		$res = $this->db_link->query( $query );
	
		if (PEAR::isError( $res ) ) {
			$this->TriggerDBError( 'Unable to get last' . $type . 'jobs status from catalog', $res);
		}else {
			$result = $res->fetchRow();
			return array( $label, $result['count'] );
		}
	} // end function GetJobsStatistics()
	
	public function CountVolumesByPool( $pool_id )
	{
		$res 	= null;
		$nb_vol = null;
		$query  = '';

		switch( $this->driver )
		{
			case 'sqlite':
			case 'mysql':
				$query 	= 'SELECT COUNT(*) as vols,Pool.name as pool_name ';
				$query .= 'FROM Media ';
				$query .= 'RIGHT JOIN Pool ON (Media.PoolId = Pool.PoolId) ';
				$query .= 'WHERE Media.poolid = ' . $pool_id;
			break;
			case 'pgsql':
				$query 	= 'SELECT COUNT(*) as vols,Pool.name as pool_name ';
				$query .= 'FROM Media ';
				$query .= 'RIGHT JOIN Pool ON (Media.PoolId = Pool.PoolId) ';
				$query .= 'WHERE Media.poolid = ' . $pool_id;
				$query .= 'GROUP BY pool.name';
			break;
		}
		
		$res = $this->db_link->query( $query );
		if( PEAR::isError( $res ) )
			$this->triggerDBError( 'Unable to get volume number from pool', $res );
		
		$vols = $res->fetchRow( );
		
		return array( $vols['pool_name'], $vols['vols'] );
	}
	
	public function GetStoredFiles( $delay = LAST_DAY )
	{
		$totalfiles = 0;

		$query = "SELECT SUM(JobFiles) AS stored_files FROM Job ";
		
		// Interval calculation
		$end_date   = mktime();
		$start_date = $end_date - $delay;
		
		$start_date = date( "Y-m-d H:i:s", $start_date );
		$end_date   = date( "Y-m-d H:i:s", $end_date );			

		if( $delay != ALL )
			$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date'";
			
		$result = $this->db_link->query( $query );
		
		if( !PEAR::isError($result) ) {
			$nbfiles 	= $result->fetchRow(DB_FETCHMODE_ASSOC);
			$totalfiles = $totalfiles + $nbfiles['stored_files'];
		}else{
			$this->TriggerDBError("Unable to get protected files from catalog", $result);
		}
		
		return $totalfiles;
	}
	
	public function GetStoredBytes( $delay = LAST_DAY )
	{
		$query = "SELECT SUM(JobBytes) as stored_bytes FROM Job ";
		
		// Interval calculation
		$end_date   = mktime();
		$start_date = $end_date - $delay;
		
		$start_date = date( "Y-m-d H:i:s", $start_date );
		$end_date   = date( "Y-m-d H:i:s", $end_date );
		
		if( $delay != ALL )
			$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date'";
		
		$result = $this->db_link->query( $query );
		
		if( PEAR::isError( $result ) ) {
			$this->TriggerDBError("Unable to get Job Bytes from catalog", $result );
		}else{
			return $result->fetchRow( DB_FETCHMODE_ASSOC );
		}
	}
	
	public function GetStoredBytesByInterval( $start_date, $end_date )
	{
		$query = '';
		
		switch($this->driver) {
			case 'sqlite':
			case 'mysql':
				$query = "SELECT SUM(JobBytes) as stored_bytes FROM Job WHERE (EndTime BETWEEN '$start_date' AND '$end_date')";
			break;
			case 'pgsql':
				$query = "SELECT SUM(JobBytes) as stored_bytes FROM Job WHERE (EndTime BETWEEN '$start_date' AND '$end_date')";
			break;
		}
		
		$result = $this->db_link->query( $query );
		
		if( PEAR::isError( $result ) ) {
			$this->TriggerDBError( "Unable to get Job Bytes from catalog", $result );
		}else{
			$stored_bytes = 0;
			$tmp = $result->fetchRow( DB_FETCHMODE_ASSOC );
			
			$day = date( "D d", strtotime($end_date) );
			
			if( isset( $tmp['stored_bytes'] ) ) {
				$hbytes = CUtils::Get_Human_Size( $tmp['stored_bytes'], 3, 'GB' );
				$hbytes = explode( " ", $hbytes );
				$stored_bytes = $hbytes[0];
			}
			
			return array( $day, $stored_bytes );
		}
	}
	
	public function GetStoredBytesByJob( $jobname, $start_date, $end_date )
	{
		$query  = "SELECT SUM(JobBytes) as stored_bytes, EndTime FROM Job ";
		$query .= "WHERE ( EndTime BETWEEN '$start_date' AND '$end_date' ) AND ";
		$query .= "Name = '$jobname'";
		
		$result = $this->db_link->query( $query );
		
		if( PEAR::isError( $result ) ) {
			$this->TriggerDBError("Unable to get Job Bytes from catalog", $result );
		}else{
			$stored_bytes = 0;
			$tmp = $result->fetchRow( DB_FETCHMODE_ASSOC );
			
			$day = date( "D d", strtotime($end_date) );
			
			if( isset( $tmp['stored_bytes'] ) ) {
				$hbytes = CUtils::Get_Human_Size( $tmp['stored_bytes'], 3, 'GB' );
				$hbytes = explode( " ", $hbytes );
				$stored_bytes = $hbytes[0];
			}
			
			return array( $day, $stored_bytes );
		}			
	}
	
	public function GetStoredFilesByJob( $jobname, $start_date, $end_date )
	{
		$query  = "SELECT SUM(JobFiles) as stored_files, EndTime FROM Job ";
		$query .= "WHERE ( EndTime BETWEEN '$start_date' AND '$end_date' ) AND ";
		$query .= "Name = '$jobname'";
		
		$result = $this->db_link->query( $query );
		
		if( PEAR::isError( $result ) ) {
			$this->TriggerDBError("Unable to get Job Files from catalog", $result);
		}else{
			$stored_bytes = 0;
			$tmp = $result->fetchRow( DB_FETCHMODE_ASSOC );
			
			$day 			= date( "D d", strtotime($end_date) );
			$stored_files 	= $tmp['stored_files'];
			
			return array( $day, $stored_files );
		}			
	}
	
	private function TriggerDBError( $message, $db_error)
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
