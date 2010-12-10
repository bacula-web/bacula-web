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
// Last Err: 11
define('CONFIG_DIR', "configs");
define('CONFIG_FILE', "bacula.conf");
define('BACULA_TYPE_BYTES_FILES', 1);
define('BACULA_TYPE_FILES_JOBID', 2);
define('BACULA_TYPE_BYTES_ENDTIME_ALLJOBS', 69);

require_once "paths.php";
require_once "DB.php";                                                                                                                  // Pear DB
require_once "config.inc.php";
require_once "bgraph.inc.php";
require_once($smarty_path."Config_File.class.php");

if (!function_exists('array_fill')) {                                                                                   // For PHP < 4.2.0 users 
    require_once('array_fill.func.php');
}

class Bweb extends DB {

    var $StartDate;
    var $EndDate;
    var $driver;
	var $dbs;
	var $dbs_name;
	
	public $db_link;						// Database link
	private $db_dsn;						// Data Source Name
	
	private $config_file;					// Config filename
	private $config;						// Loaded config from bacula.conf
	private $catalogs = array();			// Catalog array

    function __construct()
	{             
		$this->catalogs = array();
		
		// Loading configuration
		$this->config_file = getcwd() . '/configs/bacula.conf';
		if( !$this->load_config() )
			die( "Unable to load configuration");
			
		//echo "Number of catalog defined " . count($this->catalogs) . "<br />";
		
		/*
		$conf = new Config_File (CONFIG_DIR);
		$this->dbs = array();

		$i = 2;
		$sections = $conf->get(CONFIG_FILE,"DATABASE","host");
		array_push($this->dbs, "DATABASE");

		while ( !empty($sections) ) {                
			$sections = $conf->get(CONFIG_FILE,"DATABASE".$i,"host");
			if ( !empty($sections) )
				array_push($this->dbs,"DATABASE".$i);
			$i++;
		}

		if ( $i < 4)
			$sec = "DATABASE";
		else {
			if ( !empty($_POST['sel_database']) ) {
				$_SESSION['DATABASE'] = $_POST['sel_database'];
				$sec = $_POST['sel_database'];
			} else {
				if (isset($_SESSION['DATABASE']) )
					$sec = $_SESSION['DATABASE'];
				else
					$sec = "DATABASE";
			}
		}

        $this->dsn['hostspec'] = $conf->get(CONFIG_FILE,$sec,"host");
        $this->dsn['username'] = $conf->get(CONFIG_FILE,$sec,"login");
        $this->dsn['password'] = $conf->get(CONFIG_FILE,$sec,"pass");
        $this->dsn['database'] = $conf->get(CONFIG_FILE,$sec,"db_name");
        $this->dsn['phptype']  = $conf->get(CONFIG_FILE,$sec,"db_type");   // mysql, pgsql
        
		if (  $conf->get(CONFIG_FILE,$sec,"db_port") )
			$this->dsn[port] = $conf->get(CONFIG_FILE,$sec,"db_port");
		*/
		
		// Construct a valid dsn
        $this->db_dsn['hostspec'] = $this->catalogs[0]["host"];
        $this->db_dsn['username'] = $this->catalogs[0]["login"];
		$this->db_dsn['password'] = $this->catalogs[0]["pass"];
		$this->db_dsn['database'] = $this->catalogs[0]["db_name"];
		$this->db_dsn['phptype']  = $this->catalogs[0]["db_type"];
		
		                        
        $this->db_link = $this->connect($this->db_dsn);
        
		if (DB::isError($this->db_link)) {
			die($this->db_link->getMessage());
		}else {
			$this->driver = $this->db_dsn['phptype'];                            
            register_shutdown_function(array(&$this,'close'));
			$this->dbs_name = $this->db_dsn['database'];
		}
	}
                
    function load_config()
	{
		$this->config = parse_ini_file( $this->config_file, true );
		
		if( !$this->config == false ) {
			// Loading database connection information
			foreach( $this->config as $parameter => $value )
			{
				//echo "Param $parameter = $value <br />";
				if( is_array($value) ){		// Parsing database section
					array_push( $this->catalogs, $value );
				}
			}
			return true;
		}else
			return false;
	}
	
	public function get_config_param( $param )
	{
		if( isset( $this->config[$param] ) )
			return $this->config[$param];
		else
			return false;
	}
	
	public function Get_Nb_Catalogs() 
	{
		return count( $this->catalogs );
	}
	
	
	function close() 
	{
		$this->db_link->disconnect();
    }      

        
         
        function CalculateBytesPeriod($server,$StartDate,$EndPeriod) {   // Bytes transferred in a period.

                $result =& $this->db_link->query("select SUM(JobBytes) from Job WHERE EndTime < '$EndPeriod' and EndTime > '$StartDate' and Name='$server'")
                        or die("classes.inc: Error query: 1");
                $return =& $result->fetchRow(); 
                return $return[0];
        }//end function

        
         
        function CalculateFilesPeriod($server,$StartDate,$EndPeriod) {    // Number of files transferred in a period.

                $result =& $this->db_link->query("select SUM(JobFiles) from Job WHERE EndTime < '$EndPeriod' and EndTime > '$StartDate' and Name='$server'")
                        or die("classes.inc: Error query: 2");
                $return =& $result->fetchRow();
                return $return[0];
        }//end function 

                 

        function PrepareDate($StartDateMonth,$StartDateDay,$StartDateYear,$EndDateMonth,$EndDateDay,$EndDateYear) {  // Convert date for Smarty. Check if only works with Mysql.
        
                $this->StartDate=$StartDateYear."-".$StartDateMonth."-".$StartDateDay." 00:00:00";
                $this->EndDate=$EndDateYear."-".$EndDateMonth."-".$EndDateDay." 23:59:59";  // last day full
                
        }//end function
 
		function human_file_size( $size, $decimal = 2 )
		{
			$unit_id = 0;
			$lisible = false;
			$units = array('B','KB','MB','GB','TB');
			$hsize = $size;
				
			while( !$lisible ) {
				if ( $hsize >= 1024 ) {
					$hsize    = $hsize / 1024;
					$unit_id += 1;
				} 
				else {
					$lisible = true;
				} 
			} 
			// Format human size
			$hsize = sprintf("%." . $decimal . "f", $hsize);
			return $hsize . ' ' . $units[$unit_id];
		} // end function

		
		function GetDbSize() 
		{
			$database_size = 0;
			if ( $this->driver == "mysql") {
				$dbsize = $this->db_link->query("show table status") or die ("classes.inc: Error query: 3");
				
				if ( $dbsize->numRows() ) {
					while ( $res = $dbsize->fetchRow(DB_FETCHMODE_ASSOC) )
						$database_size += $res["Data_length"];
                } else {
					return 0;
				} // end if else
            } // end if
            else if ( $this->driver == "pgsql") {
				$dbsize = $this->db_link->query("select pg_database_size('$this->dbs_name')") or die ("classes.inc: Error query: 4");
				
				if (PEAR::isError($dbsize))
					die($dbsize->getMessage());
                    
				if ( $dbsize->numRows() ) {
					while ( $res = $dbsize->fetchRow() )
						$database_size += $res[0];
                } else {
					return 0;
				}
            } // end if       
				
			$dbsize->free();
		
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
/*
							$query  = "SELECT Media.VolumeName, Media.VolBytes, Media.VolStatus, Pool.Name, Media.MediaType,Media.LastWritten, FROM_UNIXTIME(UNIX_TIMESTAMP(Media.LastWritten)+Media.VolRetention ) AS expire 
									   FROM Pool LEFT JOIN Media ON Media.PoolId=Pool.PoolId WHERE poolid='$pool[0]' 
									   ORDER BY Media.VolumeName";
*/
							$query  = "SELECT Media.volumename, Media.volbytes, Media.volstatus, Media.mediatype, Media.lastwritten, Media.volretention
									   FROM Media LEFT JOIN Pool ON Media.poolid = Pool.poolid
								       WHERE Media.poolid = '". $pool['poolid'] . "' ORDER BY Media.volumename";
						break;
						case 'pgsql':
							$query  = "SELECT media.volumename, media.volbytes, media.volstatus, media.mediatype, media.lastwritten, media.volretention
									   FROM media LEFT JOIN pool ON media.poolid = pool.poolid
								       WHERE media.poolid = '". $pool['poolid'] . "' ORDER BY media.volumename";
							/*
							$query  = "SELECT Media.VolumeName, Media.VolBytes,Media.VolStatus,Pool.Name,Media.MediaType,Media.LastWritten, Media.LastWritten + Media.VolRetention * interval '1 second' AS expire 
									   FROM Pool LEFT JOIN Media ON media.poolid=pool.poolid WHERE poolid='$pool[0]' 
									   ORDER BY Media.VolumeName";
							*/
						break;
						case 'sqlite':
							$query  = "";		// not yet implemented
						break;
						default:
						break;
					} // end switch
					
					//$this->db_link->setFetchMode(DB_FETCHMODE_ASSOC);
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
										//echo "volume " . $media['volumename'] . 'vol bytes' .$media['volbytes'] . '<br />';
									} else {
										$media['lastwritten'] = "N/A";
										$media['expire']      = "N/A";
										$media['volbytes'] 	  = "0 KB";
									}								
								// Add the media in pool array
								array_push( $volumes[ $pool['name']], $media);
							}
						} // end while
					} // end if else
				} // end while
				return $volumes;
        } // end function GetVolumeList()
		
		public function GetLastJobs( $delay = LAST_DAY )
		{
			$query 		= "";
			$start_date = "";
			$end_date 	= "";
			
			// Interval calculation
			$end_date   = mktime();
			$start_date = $end_date - $delay;
			
			$start_date = date( "Y-m-d H:m:s", $start_date );
			$end_date   = date( "Y-m-d H:m:s", $end_date );
			
			switch( $this->driver )
			{
				case 'mysql':
					$query  = 'SELECT COUNT(JobId) AS completed_jobs ';
					$query .= 'FROM Job ';
					$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date' ";
					$query .= "AND JobStatus = 'T'";
				break;
			}
		
			$jobs = $this->db_link->query( $query );
		
			if (PEAR::isError( $jobs ) ) {
				die( "Unable to get last completed jobs status from catalog<br />" . $status->getMessage() );
			}else {
				return $jobs->fetchRow( DB_FETCHMODE_ASSOC );
			}
		} // end function GetLastJobStatus()
		
		public function GetLastErrorJobs( $delay = LAST_DAY )
		{
			$query 		= "";
			
			// Interval calculation
			$end_date   = mktime();
			$start_date = $end_date - $delay;
			
			$start_date = date( "Y-m-d H:m:s", $start_date );
			$end_date   = date( "Y-m-d H:m:s", $end_date );
			
			//echo "start date: $start_date <br />";
			//echo "end date: $end_date <br />";
			
			switch( $this->driver )
			{
				default:
					$query  = 'SELECT COUNT(JobId) AS failed_jobs ';
					$query .= 'FROM Job ';
					$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date' ";
					$query .= "AND JobStatus = 'f'";
				break;
			}				
			$result = $this->db_link->query( $query );
			
			if (PEAR::isError( $result ) ) {
				die( "Unable to get last failed jobs status from catalog<br />query = $query <br />" . $result->getMessage() );
			}else {
				return $result->fetchRow( DB_FETCHMODE_ASSOC );
			} // end if else
		} // end function GetLastErrorJobs
		
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
		
		public function Get_ElapsedTime_Job( $delay = LAST_DAY )
		{
			$query 			= "";
			$total_elapsed	= 0;
			
			// Interval calculation
			$end_date   = mktime();
			$start_date = $end_date - $delay;
			
			$start_date = date( "Y-m-d H:m:s", $start_date );
			$end_date   = date( "Y-m-d H:m:s", $end_date );
			
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
				if ( $total_elapsed > 86400 ) {
					return gmstrftime("%d days %H:%M:%S", $total_elapsed );
				}else {
					return gmstrftime("%H:%M:%S", $total_elapsed );
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
			
			$start_date = date( "Y-m-d H:m:s", $start_date );
			$end_date   = date( "Y-m-d H:m:s", $end_date );
			
			// Job status
			switch( $type )
			{
				case 'completed':
					$where = "AND JobStatus = 'T' ";
					$label = "Completed";
				break;
				case 'completed_errors':
					$where = "AND JobStatus = 'E' ";
					$label = "Completed with errors";
				break;
				case 'failed':
					$where = "AND JobStatus = 'f' ";
					$label = "Failed";
				break;
				case 'waiting':
					$where = "AND JobStatus IN ('F','S','M','m','s','j','c','d','t') ";
					$label = "Waiting";
				break;
				case 'created':
					$where = "AND JobStatus = 'C' ";
					$label = "Created but not running";
				break;
				case 'running':
					$where = "AND JobStatus = 'R' ";
					$label = "Running";
				break;
				case 'error':
					$where = "AND JobStatus IN ('e','f') ";
					$label = "Errors";
				break;
			}
			
			$query  = 'SELECT COUNT(JobId) AS ' . $type . ' ';
			$query .= 'FROM Job ';
			$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date' ";
			$query .= $where;
		
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
		
		public function GetStoredBytes( $delay = LAST_DAY )
		{
			$query = "SELECT SUM(JobBytes) as stored_bytes FROM Job ";
			
			// Interval calculation
			$end_date   = mktime();
			$start_date = $end_date - $delay;
			
			$start_date = date( "Y-m-d H:m:s", $start_date );
			$end_date   = date( "Y-m-d H:m:s", $end_date );
			
			if( $delay != ALL ) {
				$query .= "WHERE EndTime BETWEEN '$start_date' AND '$end_date'";
			}
			
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
				$tmp = $result->fetchRow( DB_FETCHMODE_ASSOC );
				return array( $tmp['EndTime'], $tmp['stored_bytes'] );
			}
		}
} // end class Bweb
?>
