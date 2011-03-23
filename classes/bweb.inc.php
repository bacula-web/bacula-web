<?php
/* 
+-------------------------------------------------------------------------+
| Copyright (C) 2004-2005 Juan Luis Frances Jiminez                       |
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
require_once "config.inc.php";

class Bweb extends DB {

    var $StartDate;
    var $EndDate;

    var $driver;
	
	public  $tpl;
	public  $db_link;						// Database link
	
	private $config_file;					// Config filename
	private $config;						// Loaded config from bacula.conf
	private $catalogs = array();			// Catalog array
	public  $catalog_nb;
	private $bwcfg;

    function __construct()
	{             
		$this->bwcfg = new BW_Config();
		$dsn = array();
		
		// Checking if config file exist and is readable
		if( !$this->bwcfg->Check_Config_File() )
			die( "Unable to load configuration file" );
		else {
			$this->bwcfg->Load_Config();
			$this->catalog_nb = $this->bwcfg->Count_Catalogs();
		}
		
		// Select which catalog to connect to
		if( isset( $_POST['catalog_id'] ) )
			$dsn = $this->bwcfg->Get_Dsn( $_POST['catalog_id'] );
		else
			$dsn = $this->bwcfg->Get_Dsn( 0 );
		
		// Connect to the database
		$this->db_link = $this->connect( $dsn );
        
		if (DB::isError($this->db_link)) {
			die( 'Unable to connect to catalog <br />' . $this->db_link->getMessage());
		}else {
			$this->driver = $dsn['phptype'];                            
            register_shutdown_function(array(&$this,'close') );
		}
		
		// Initialize smarty template classe
		$this->init_tpl();
		// Initialize smarty gettext function
		$this->init_gettext();
		
		// Catalog selection		
		if( $this->catalog_nb > 1 ) {
			// Set current catalog in header template
			if(isset( $_POST['catalog_id'] ) )
				$this->tpl->assign( 'catalog_current', $_POST['catalog_id'] );
			
			$this->tpl->assign( 'catalogs', $this->bwcfg->Get_Catalogs() );			
		}else
		{
		
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
		$this->tpl->config_dir     	= "./configs";
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
         
	// Return humanized size with default unit of GB
	// if auto provide for unit argument, automaticaly decide which unit
	function human_file_size( $size, $decimal = 2, $unit = 'auto' )
	{
		$unit_id = 0;
		$lisible = false;
		$units = array('B','KB','MB','GB','TB');
		$hsize = $size;

		switch( $unit )
		{
			case 'auto';
				while( !$lisible ) {
					if ( $hsize >= 1024 ) {
						$hsize    = $hsize / 1024;
						$unit_id += 1;
					}	 
					else
						$lisible = true;
				} // end while
			break;
			
			default:
				$p = array_search( $unit, $units);
				$hsize = $hsize / pow(1024,$p);
			break;
		} // end switch
		
		$hsize = sprintf("%." . $decimal . "f", $hsize);
		$hsize = $hsize . ' ' . $units[$unit_id];
		return $hsize;
	} // end function

	
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
			$db = $result->fetchRow( DB_FETCHMODE_ASSOC );
			$database_size =+ $db['dbsize'];
		}else
			die( "Unable to get database size<br />" . $result->getMessage() );
		
		return $this->human_file_size( $database_size );  
	} // end function GetDbSize()
	
	public function Get_Nb_Clients()
	{
		$clients = $this->db_link->query("SELECT COUNT(*) AS nb_client FROM Client");
		if( PEAR::isError($clients) )
			die( "Unable to get client number" );
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
				die("Error: Failed to get pool list <br />SQL Query: $query<br />" . $pools->getMessage() );
			
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
					die( "Failed to get media list for pool $volume[0] <br /> " . $medias->getMessage() );
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
									$media['volbytes'] = $this->human_file_size( $media['volbytes'] );
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
			die( "Unable to get number of jobs with $level status from catalog <br />" . $result->getMessage() );
		}else {
			$jobs = $result->fetchRow( DB_FETCHMODE_ASSOC ); 
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
			die( "Unable to get last $status jobs number from catalog <br />" . $jobs->getMessage() );
		}else {
			$jobs = $jobs->fetchRow( DB_FETCHMODE_ASSOC ); 
			return $jobs['job_nb'];
		}
	}
	
	// Return the list of Pools in a array
	public function Get_Pools_List()
	{
		$pool_list = array();
		$result    = "";
		
		$query = "SELECT Name, PoolId FROM Pool";
		
		$result = $this->db_link->query ( $query );

		if( PEAR::isError( $result ) ) {
			die( "Unable to get the pool list from catalog" );				
		}else {
			while( $pool = $result->fetchRow(DB_FETCHMODE_ASSOC) ) {
				array_push( $pool_list, array( $pool['Name'] => $pool['PoolId'] ) );
			}
			return $pool_list;
		}
	}
	
	public function Get_BackupJob_Names()
	{
		$query 		= "SELECT Name FROM Job GROUP BY Name";
		$backupjobs = array();
		
		$result = $this->db_link->query( $query );
		
		if (PEAR::isError( $result ) ) {
			die("Unable to get BackupJobs list from catalog" );
		}else{
			while( $backupjob = $result->fetchRow( DB_FETCHMODE_ASSOC ) ) {
				array_push( $backupjobs, $backupjob["Name"] );
			}
			return $backupjobs;
		}
	}
	
	// Return elasped time string for a job
	function Get_ElapsedTime( $start_time, $end_time ) 
	{ 
		$diff = $end_time - $start_time;
		
		$daysDiff = sprintf("%02d", floor($diff/60/60/24) );
		$diff -= $daysDiff*60*60*24;
		
		$hrsDiff = sprintf("%02d", floor($diff/60/60) );
		$diff -= $hrsDiff*60*60;
		
		$minsDiff = sprintf("%02d", floor($diff/60) );
		$diff -= $minsDiff*60;
		$secsDiff = sprintf("%02d", $diff );
		
		if( $daysDiff > 0 )
			return $daysDiff . 'day(s) ' . $hrsDiff.':' . $minsDiff . ':' . $secsDiff;
		else
			return $hrsDiff . ':' . $minsDiff . ':' . $secsDiff;
	}
	
	public function Get_ElapsedTime_Job( $delay = LAST_DAY )
	{
		$query 			= "";
		$total_elapsed	= 0;
		
		// Interval calculation
		$end_date   = mktime();
		$start_date = $end_date - $delay;
		
		$start_date = date( "Y-m-d H:i:s", $start_date );
		$end_date   = date( "Y-m-d H:i:s", $end_date );
		
		switch( $this->driver )
		{
			case 'mysql':
				$query  = "SELECT UNIX_TIMESTAMP(EndTime) - UNIX_TIMESTAMP(StartTime) AS elapsed from Job ";
				$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date'";
			break;
		}
		$result = $this->db_link->query( $query );
		
		if( PEAR::isError($result) ){
			die( "Unable to get elapsed time for jobs from catalog<br />query = $query <br />" . $result->getMessage() );
		}else {
			while( $time = $result->fetchRow( DB_FETCHMODE_ASSOC ) ) {
				//echo 'elapsed = ' . $time['elapsed'] . '<br />';
				$total_elapsed += $time['elapsed'];
			}
			// Verify if elapsed time is more than 1 day
			if ( $total_elapsed > LAST_DAY ) {
				return date("%d days H:i:s", $total_elapsed );
			}else {
				return date("H:i:s", $total_elapsed );
			}
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
		
		$query  = 'SELECT COUNT(JobId) AS ' . $type . ' ';
		$query .= 'FROM Job ';
		$query .= "WHERE $where ";
	
		//echo 'query = ' . $query . '<br />';
		
		$jobs = $this->db_link->query( $query );
	
		if (PEAR::isError( $jobs ) ) {
			die( "Unable to get last $type jobs status from catalog<br />" . $status->getMessage() );
		}else {
			$res = $jobs->fetchRow();
			return array( $label , current($res) );
		}
	} // end function GetJobsStatistics()
	
	public function GetPoolsStatistics( $pools )
	{
		foreach( $pools as $pool_name => $pool ) {
			//var_dump( $pool );
			$query = "SELECT COUNT(*) AS nb_vol FROM Media WHERE PoolId = '$pool'";
			//echo $query . '<br />';
			//echo 'Pool name ' . $pool_name . '<br />';
			$result = $this->db_link->query( $query );
			
			if( PEAR::isError( $result ) ) {
				die("Unable to get volume number from catalog");
			}else{
				$nb_vol = $result->fetchRow(DB_FETCHMODE_ASSOC);
				return array( $pool_name, $nb_vol['nb_vol'] );
			}
		}
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
			die("Unable to get protected files from catalog <br />" . $result->getMessage() );
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
			die( "Unable to get Job Bytes from catalog" );
		}else{
			return $result->fetchRow( DB_FETCHMODE_ASSOC );
		}
	}
	
	public function GetStoredBytesByInterval( $start_date, $end_date )
	{
		$query = "SELECT SUM(JobBytes) as stored_bytes, EndTime FROM Job WHERE EndTime BETWEEN '$start_date' AND '$end_date'";
		
		$result = $this->db_link->query( $query );
		
		if( PEAR::isError( $result ) ) {
			die( "Unable to get Job Bytes from catalog" );
		}else{
			$stored_bytes = 0;
			$tmp = $result->fetchRow( DB_FETCHMODE_ASSOC );
			
			$day = date( "D d", strtotime($end_date) );
			
			if( isset( $tmp['stored_bytes'] ) ) {
				$hbytes = $this->human_file_size( $tmp['stored_bytes'], 3, 'GB');
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
			die( "Unable to get Job Bytes from catalog" );
		}else{
			$stored_bytes = 0;
			$tmp = $result->fetchRow( DB_FETCHMODE_ASSOC );
			
			$day = date( "D d", strtotime($end_date) );
			
			if( isset( $tmp['stored_bytes'] ) ) {
				$hbytes = $this->human_file_size( $tmp['stored_bytes'], 3, 'GB');
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
			die( "Unable to get Job Files from catalog" );
		}else{
			$stored_bytes = 0;
			$tmp = $result->fetchRow( DB_FETCHMODE_ASSOC );
			
			$day 			= date( "D d", strtotime($end_date) );
			$stored_files 	= $tmp['stored_files'];
			
			return array( $day, $stored_files );
		}			
	}
} // end class Bweb
?>
