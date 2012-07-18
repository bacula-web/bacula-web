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
	
	public  $tpl;							// Template class
	public  $db_link;						// Database link
	
	public  $catalog_nb;					// Catalog count
	private	$catalog_current_id;			// Current catalog

    function __construct()
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
		$this->init_tpl();
		
		// Checking template cache permissions
		if( !is_writable( "./templates_c" ) )
			throw new Exception("The template cache folder must be writable by Apache user");
			
		// Initialize smarty gettext function
		$language  = $this->bwcfg->get_Param( 'language' );
		if( !$language )
			throw new Exception("Language translation problem");
			
		$this->translate = new CTranslation( $language );
		$this->translate->set_Language( $this->tpl );
		
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

		$this->tpl->assign( 'catalog_current_id', $this->catalog_current_id );
		
		// Database connection
		switch( $this->bwcfg->get_Catalog_Param( $this->catalog_current_id, 'db_type') ) {
			case 'mysql':
			case 'pgsql':
				$this->db_link = new CDB( $this->bwcfg->get_DSN($this->catalog_current_id), 
										  $this->bwcfg->get_Catalog_Param( $this->catalog_current_id, 'login'),
										  $this->bwcfg->get_Catalog_Param( $this->catalog_current_id, 'password') );
			break;
			case 'sqlite':
				$this->db_link = new CDB( $this->bwcfg->get_DSN($this->catalog_current_id) );
			break;
		}
										
		$this->db_link->makeConnection();	

		// Catalog selection		
		if( $this->catalog_nb > 1 ) {
			// Catalogs list
			$this->tpl->assign( 'catalogs', $this->bwcfg->get_Catalogs() );			
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

		$this->tpl->template_dir 	= VIEW_DIR;
		$this->tpl->compile_dir 	= "./templates_c";
	}
	
	public function getDatabaseSize() 
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
				$db_size = filesize($this->bwcfg->get_Catalog_Param($this->catalog_current_id, 'db_name') );
				return CUtils::Get_Human_Size($db_size);
			break;
		}
		
		// Execute SQL statment
		$result  = $this->db_link->runQuery( $query );
		$db_size = $result->fetch();
		$db_size = CUtils::Get_Human_Size( $db_size['dbsize'] );

		return $db_size;
	} // end function GetDbSize()
	
	public function Get_Nb_Clients()
	{
		$clients_nb = 0;
		$query     = "SELECT COUNT(*) AS nb_client FROM Client";
		
		$clients    = $this->db_link->runQuery( $query );
		$clients_nb = $clients->fetch();
		
		return $clients_nb;
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
			
			return $volumes_list;
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
		$jobs   = $this->db_link->runQuery($query);
		$result = $jobs->fetch(); 

		return $result['job_nb'];
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
				$query 		= "SELECT name, numvols, poolid FROM Pool";
			break;
			case 'pgsql':
				$query 		= "SELECT name, numvols, poolid FROM pool";
			break;
		}

		$result = $this->db_link->runQuery($query);
		foreach( $result->fetchAll() as $pool )
			$pools[] = $pool;

		return $pools;
	}
	
	public function getJobsName( $client_id = null )
	{
		$query 		= '';
		
		$result 	= '';
		$backupjobs = array();
		
		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
				$query 		= "SELECT name FROM Job ";
			break;
			case 'pgsql':
				$query 		= "SELECT name FROM Job ";
			break;
		}
		
		if( !is_null($client_id) )
			$query .= "WHERE clientid = '$client_id' ";
		
		$query .= 'GROUP BY name ORDER BY name';
		
		$result = $this->db_link->runQuery($query);
		foreach( $result->fetchAll() as $jobname )
			$backupjobs[] = $jobname['name'];
		
		return $backupjobs;
	}
	
	// Return an array with clients list
	public function get_Clients() 
	{
		$query   = '';
		$result  = '';
		$clients = array();

		switch( $this->db_link->getDriver() )
		{
			case 'sqlite':
			case 'mysql':
			case 'pgsql':
				$query  = "SELECT Client.ClientId, Client.Name FROM Client ";
				if( $this->bwcfg->get_Param( 'show_inactive_clients' ) )
					$query .= "WHERE FileRetention > '0' AND JobRetention > '0' "; 
				$query .= "ORDER BY Client.Name;";
			break;
		}

		$result = $this->db_link->runQuery($query);
			
		foreach( $result->fetchAll() as $client )
			$clients[ $client['clientid'] ] = $client['name'];
				
		return $clients;		
	}
	
	public function getClientInfos( $client_id )
    {
		$client = array();
		$result = '';
		$query  = "SELECT name,uname FROM Client WHERE clientid = '$client_id'";
		
		$result = $this->db_link->runQuery($query);
			
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
	
	public function getStoredFiles( $start_timestamp, $end_timestamp, $job_name = 'ALL', $client = 'ALL' )
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
		
		if( $client != 'ALL' )
			$query .= " AND clientid = '$client'";
		
		// Execute query
		$result = $this->db_link->runQuery( $query );
		$result = $result->fetch();

		if( isset($result['stored_files']) and !empty($result['stored_files']) )
			return $result['stored_files'];
		else
			return 0;
	}
	
	// Function: getStoredBytes
	// Parameters:
	// 		$start_timestamp: 	start date in unix timestamp format
	// 		$end_timestamp: 	end date in unix timestamp format
	//		$job_name:			optional job name
	
	public function getStoredBytes( $start_timestamp, $end_timestamp, $job_name = 'ALL', $client = 'ALL' )
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
		
		if( $client != 'ALL' )
			$query .= " AND clientid = '$client'";		
		
		// Execute SQL statment
		$result = $this->db_link->runQuery( $query );
		$result = $result->fetch();

		if( isset($result['stored_bytes']) and !empty($result['stored_bytes']) )
			return $result['stored_bytes'];
		else
			return 0;
	}
} // end class Bweb
?>
