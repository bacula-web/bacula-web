<?php
/* 
+-------------------------------------------------------------------------+
| Copyright (C) 2004 Juan Luis Francés Jiménez							  |
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
class Bweb
{
	public	$translate;						// Translation class instance
	private $bwcfg;							// Config class
	private $catalogs = array();			// Catalog array
	
	private $view;							// Template class

	public  $db_link;						// Database connection
	private $db_driver;						// Database connection driver
	
	public  $catalog_nb;					// Catalog count
	private	$catalog_current_id;			// Current catalog

    function __construct( &$view )
	{             
		// Loading configuration file parameters
		try {
			$this->bwcfg = new Config();
			$this->bwcfg->load();
		}catch( Exception $e ) {
			CErrorHandler::displayError($e);
		}
			
		$this->catalog_nb = $this->bwcfg->Count_Catalogs();
		
		// Template engine initalization
		$this->view = $view;
		
		// Checking template cache permissions
		if( !is_writable( VIEW_CACHE_DIR ) )
			throw new Exception("The template cache folder <b>" . VIEW_CACHE_DIR . "</b> must be writable by Apache user");
			
		// Initialize smarty gettext function
		$language  = $this->bwcfg->get_Param( 'language' );
		if( !$language )
			throw new Exception("Language translation problem");
			
		$this->translate = new CTranslation( $language );
		$this->translate->set_Language( $this->view );
		
		// Check catalog id
		$http_post = CHttpRequest::getRequestVars($_POST);
		if( isset( $http_post['catalog_id'] ) ) {
			$this->catalog_current_id = $http_post['catalog_id'];
			$_SESSION['catalog_id'] = $this->catalog_current_id;
		}
		elseif( isset( $_SESSION['catalog_id'] ) )
			$this->catalog_current_id = $_SESSION['catalog_id'];
		else {
			$this->catalog_current_id = 0;
			$_SESSION['catalog_id'] = $this->catalog_current_id;
		}

		$this->view->assign( 'catalog_current_id', $this->catalog_current_id );
		
		// DB connection parameters
		$dsn 	= $this->bwcfg->get_DSN($this->catalog_current_id);
		$driver = $this->bwcfg->get_Catalog_Param( $this->catalog_current_id, 'db_type');
		
		if( $driver != 'sqlite' ) {
			$user 	= $this->bwcfg->get_Catalog_Param( $this->catalog_current_id, 'login');
			$pwd 	= $this->bwcfg->get_Catalog_Param( $this->catalog_current_id, 'password');
		}

		switch( $driver ) {
			case 'mysql':
			case 'pgsql':
				$this->db_link = CDB::connect( $dsn, $user, $pwd );
			break;
			case 'sqlite':
				$this->db_link = CDB::connect( $dsn );
			break;
		}
		
		// Getting driver name from PDO connection
		$this->db_driver = CDBUtils::getDriverName( $this->db_link );

		// Set PDO connection options
		$this->db_link->setAttribute( PDO::ATTR_CASE, PDO::CASE_LOWER);
		$this->db_link->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db_link->setAttribute( PDO::ATTR_STATEMENT_CLASS, array('CDBResult', array($this)) );
		
		// MySQL connection specific parameter
		if ( $this->db_driver == 'mysql' )
			$this->db_link->setAttribute( PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

		// Bacula catalog selection		
		if( $this->catalog_nb > 1 ) {
			// Catalogs list
			$this->view->assign( 'catalogs', $this->bwcfg->get_Catalogs() );			
			// Catalogs count
			$this->view->assign( 'catalog_nb', $this->catalog_nb );
		}
	}
                
	public function getDatabaseSize() 
	{
		$db_size = 0;
		$query 	 = '';
		$result	 = '';
		
		switch( $this->db_driver )
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
				$db_size = filesize($this->bwcfg->get_Catalog_Param($this->catalog_current_id, 'db_name') );
				return CUtils::Get_Human_Size($db_size);
			break;
		}
		
		// Execute SQL statment
		$result  = CDBUtils::runQuery( $query, $this->db_link );
		$db_size = $result->fetch();
		
		return CUtils::Get_Human_Size( $db_size['dbsize'] );
	} // end function GetDbSize()
	
	// ==================================================================================
	// Function: 	Get_Nb_Clients()
	// Parameters: 	none
	// Return:		Total of clients
	// ==================================================================================
	public function Get_Nb_Clients()
	{
		$clients_nb = 0;
		$query     = "SELECT COUNT(*) AS nb_client FROM Client";
		
		$client_nb = CDBUtils::runQuery( $query, $this->db_link ); 
		return $client_nb->fetch();
	}
  
	// Return an array of volumes ordered by poolid and volume name
	public function GetVolumeList() 
	{
			$pools        = '';
			$volumes      = '';
			$volumes_list = array();
			$query        = "";
			$debug	      = false;
			
			// Get the list of pools id
			$query = "SELECT Pool.poolid, Pool.name FROM Pool ORDER BY Pool.poolid";
			
			foreach( $this->getPools() as $pool ) {
				switch( $this->db_driver )
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
				
				//$volumes = $this->db_link->runQuery($query);
				$volumes  = CDBUtils::runQuery( $query, $this->db_link );
			
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
						$volume['odd_even'] = 'even';

					// Add the media in pool array
					array_push( $volumes_list[ $pool['name']], $volume);
				} // end foreach volumes
			} // end foreach pools
			
			return $volumes_list;
	} // end function GetVolumeList()
	
	public function countJobs( $start_timestamp, $end_timestamp, $status = 'ALL', $level = 'ALL', $jobname = 'ALL', $client = 'ALL' )
	{
		$query 			  = "";
		$where_interval	  = "";
		$where_conditions = array();
		
		// Calculate sql query interval
		$start_date		= date( "Y-m-d H:i:s", $start_timestamp);	
		$end_date		= date( "Y-m-d H:i:s", $end_timestamp);
		
		switch( $this->db_driver )
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
		$jobs = CDBUtils::runQuery( $query, $this->db_link );
		$jobs = $jobs->fetch();
		return $jobs['job_nb'];
	}
	
	// ==================================================================================
	// Function: 	countPools()
	// Parameters: 	none
	// Return:		number of pools
	// ==================================================================================
	public function countPools() {
		$pools_count = '';
		$table 		 = 'Pool';
		
		// Check db driver for pool table name
		if ( $this->db_driver == 'pgsql' ) {
			$table = strtolower($table);
		} 
		
		$query = array('table' => $table, 'fields' => array('count(*) as pools_count'));
		$result = $this->db_link->query(CDBQuery::get_Select($query));
		
		// Return result
		$pools_count = $result->fetch();
		return $pools_count['pools_count'];
	}
	
	// ==================================================================================
	// Function: 	countVolumes()
	// Parameters: 	none
	// Return:		number of volumes
	// ==================================================================================
	public function countVolumes() {
		$vols_count  = '';
		$table 		 = 'Media';
		
		// Check db driver for pool table name
		if ( $this->db_driver == 'pgsql') {
			$table = strtolower($table);
		} 
		
		$query = array('table' => $table, 'fields' => array('count(*) as vols_count'));
		$result = $this->db_link->query(CDBQuery::get_Select($query));
		
		// Return result
		$vols_count = $result->fetch();
		return $vols_count['vols_count'];
	}

	// ==================================================================================
	// Function: 	countFilesets()
	// Parameters: 	none
	// Return:		number of volumes
	// ==================================================================================
	public function countFilesets() {
		$filesets_count  = '';
		$table 		     = 'FileSet';
		
		// Check db driver for pool table name
		if ( $this->db_driver == 'pgsql') {
			$table = strtolower($table);
		} 
		
		$query = array('table' => $table, 'fields' => array('count(*) as filesets_count'));
		$result = $this->db_link->query(CDBQuery::get_Select($query));
		
		// Return result
		$filesets_count = $result->fetch();
		return $filesets_count['filesets_count'];
	}

	// ==================================================================================
	// Function: 	getPools()
	// Parameters: 	none
	// Return:		list of Pools in a array
	// ==================================================================================
	public function getPools()
	{
		$query  = '';
		$pools  = array();
		$result = '';
		
		switch( $this->db_driver )
		{
			case 'sqlite':
			case 'mysql':
				$query 		= "SELECT name, numvols, poolid FROM Pool ";
			break;
			case 'pgsql':
				$query 		= "SELECT name, numvols, poolid FROM pool ";
			break;
		}
		
		if( $this->bwcfg->get_Param( 'hide_empty_pools' ) ) {
			$query .= 'WHERE Pool.NumVols > 0';
		}

		//$result = $this->db_link->runQuery($query);
		$result = CDBUtils::runQuery( $query, $this->db_link );
		
		
		foreach( $result->fetchAll() as $pool )
			$pools[] = $pool;

		return $pools;
	}
	
	public function getJobsName( $client_id = null )
	{
		$jobs  = array();
		$query = array( 'table' => 'Job', 'fields' => array('name'), 'orderby' => 'name', 'groupby' => 'name' );
		
		if( !is_null($client_id) )
			$query['where'] = "clientid = '$client_id' ";
		
		$result = CDBUtils::runQuery( CDBQuery::get_Select($query), $this->db_link );
		
		foreach( $result->fetchAll() as $job ) {
			$jobs[] = $job['name'];
		}

		return $jobs;
	}
	
	// ==================================================================================
	// Function: 	get_Clients()
	// Parameters: 	none
	// Return:		an array of all clients (except inactive if the option is enabled)
	// ==================================================================================	
	public function get_Clients() 
	{
		$clients  	= array();

		$query = array( 'table' => 'Client', 'fields' => array('ClientId, Name'), 'orderby' => 'Name' );

		if( $this->bwcfg->get_Param( 'show_inactive_clients' ) )
				$query['where'] = "FileRetention > '0' AND JobRetention > '0' "; 

		$result = CDBUtils::runQuery( CDBQuery::get_Select($query), $this->db_link );
		
		foreach( $result->fetchAll() as $client ) {
			$clients[ $client['clientid'] ] = $client['name'];
		}
		
		return $clients;		
	}
	
	public function getClientInfos( $client_id )
    {
		$client = array();
		$result = '';
		$query  = "SELECT name,uname FROM Client WHERE clientid = '$client_id'";
		
		$result = CDBUtils::runQuery( $query, $this->db_link );
			
		foreach( $result->fetchAll() as $client ) {
			$uname			   = explode( ' ', $client['uname'] );
			$client['version'] = $uname[0];
				
			$uname			   = explode(',', $uname[2] );
			$temp    		   = explode('-', $uname[0]);
			$client['arch']	   = $temp[0];
			$client['os']      = $uname[1];
		}
		
		return $client;
	}
	
	// ==================================================================================
	// Function: 	getStoredFiles()
	// Parameters: 	$start_timestamp: 	start date in unix timestamp format
	//				$end_timestamp: 	end date in unix timestamp format
	//				$job_name:			optional job name
	//				$client				optional client name
	// Return:		Total of stored files within the specific period
	// ==================================================================================	
	public function getStoredFiles( $start_timestamp, $end_timestamp, $job_name = 'ALL', $client = 'ALL' )
	{
		$query 		= "";
		$start_date = date( "Y-m-d H:i:s", $start_timestamp);	
		$end_date   = date( "Y-m-d H:i:s", $end_timestamp);	
		
		switch( $this->db_driver )
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
		
		if( $client != 'ALL' )
			$query .= " AND clientid = '$client'";
		
		// Execute query
		$result = CDBUtils::runQuery( $query, $this->db_link );
		$result = $result->fetch();
		
		if( isset($result['stored_files']) and !empty($result['stored_files']) )
			return $result['stored_files'];
		else
			return 0;
	}
	
	// ==================================================================================
	// Function: 	getStoredBytes()
	// Parameters: 	$start_timestamp: 	start date in unix timestamp format
	//				$end_timestamp: 	end date in unix timestamp format
	//				$job_name:			optional job name
	// Return:		Total of stored bytes within the specific period
	// ==================================================================================
	public function getStoredBytes( $start_timestamp, $end_timestamp, $job_name = 'ALL', $client = 'ALL' )
	{
		$query    		= '';
		$result			= '';
		$start_date		= date( "Y-m-d H:i:s", $start_timestamp);	
		$end_date		= date( "Y-m-d H:i:s", $end_timestamp);	
		
		switch( $this->db_driver )
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
		
		if( $client != 'ALL' )
			$query .= " AND clientid = '$client'";		
		
		// Execute SQL statment
		$result = CDBUtils::runQuery( $query, $this->db_link );
		$result = $result->fetch();

		if( isset($result['stored_bytes']) and !empty($result['stored_bytes']) )
			return $result['stored_bytes'];
		else
			return 0;
	}
	
	// ==================================================================================
	// Function: 	getVolumesSize()
	// Parameters: 	none
	// Return:		sum in bytes of all volumes
	// ==================================================================================
	public function getVolumesSize() {
		$query = array( 'table' => 'Media', 'fields' => array('SUM(Media.VolBytes) as volumes_size') );
		
		// Run SQL query
		$result = $this->db_link->query(CDBQuery::get_Select( $query ) );
		$result = $result->fetch();
		
		return $result['volumes_size'];
	}
} // end class Bweb
?>
