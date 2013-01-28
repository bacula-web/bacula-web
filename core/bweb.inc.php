<?php
	/* 
	+-------------------------------------------------------------------------+
	| Copyright (C) 2004 Juan Luis Francés Jiménez				  			  |
	| Copyright 2010-2013, Davide Franco			                  		  |
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

	require_once( 'core/global.inc.php' );

	class Bweb
	{
		public	$translate;						// Translation class instance
		private $catalogs = array();			// Catalog array
		
		private $view;							// Template class

		public  $db_link;						// Database connection
		private $db_driver;						// Database connection driver
		
		public  $catalog_nb;					// Catalog count
		public	$catalog_current_id;			// Current catalog

		function __construct( &$view )
		{             
			// Loading configuration file parameters
			try {
				if( !FileConfig::open( CONFIG_FILE ) )
					throw new Exception("The configuration file is missing");
				else {
					$this->catalog_nb = FileConfig::count_Catalogs();
				}
			}catch( Exception $e ) {
				CErrorHandler::displayError($e);
			}
				
			// Template engine initalization
			$this->view = $view;
			
			// Checking template cache permissions
			if( !is_writable( VIEW_CACHE_DIR ) )
				throw new Exception("The template cache folder <b>" . VIEW_CACHE_DIR . "</b> must be writable by Apache user");
				
			// Initialize smarty gettext function
			$language = FileConfig::get_Value( 'language' );
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
			
			// Getting database connection paremeter from configuration file
			$dsn 	= FileConfig::get_DataSourceName( $this->catalog_current_id );
			$driver = FileConfig::get_Value( 'db_type', $this->catalog_current_id); 

			if( $driver != 'sqlite' ) {
				$user	= FileConfig::get_Value( 'login', $this->catalog_current_id); 
				$pwd	= FileConfig::get_Value( 'password', $this->catalog_current_id); 
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
			if ( $driver == 'mysql' )
				$this->db_link->setAttribute( PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

			// Bacula catalog selection		
			if( $this->catalog_nb > 1 ) {
				// Catalogs list
				$this->view->assign( 'catalogs', FileConfig::get_Catalogs() );
				// Catalogs count
				$this->view->assign( 'catalog_nb', $this->catalog_nb );
			}
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
			
			if( FileConfig::get_Value( 'hide_empty_pools' ) ) {
				$query .= 'WHERE Pool.NumVols > 0';
			}

			//$result = $this->db_link->runQuery($query);
			$result = CDBUtils::runQuery( $query, $this->db_link );
			
			
			foreach( $result->fetchAll() as $pool )
				$pools[] = $pool;

			return $pools;
		}
		
		// ==================================================================================
		// Function: 	getClientInfos()
		// Parameters: 	client id
		// Return:		array containing client information
		// ==================================================================================

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
		
		// ==================================================================================
		// Function: 	getJobsNameOfClient()
		// Parameters: 	$client_id
		// Return:		jobs list for a specific client
		// ==================================================================================

		public function getJobsNameOfClient( $client_id = null )
		{
			$query          = '';
			$table			= '';
			$result         = '';
			$backupjobs = array();

			switch( $this->db_driver ) {
				case 'sqlite':
				case 'mysql':
					$table = 'Job';
				break;
				case 'pgsql':
					$table = 'job';
				break;
			}
			
			// Build and run SQL statment
			$query  = CDBQuery::get_Select( array(	'table' => $table, 'fields' => array('name'), 'orderby' => 'name', 
													'where' => array("clientid = '$client_id'"), 'groupby' => 'name' ) ); 
						
			$result = CDBUtils::runQuery( $query, $this->db_link);
					
			foreach( $result->fetchAll() as $jobname )
				$backupjobs[] = $jobname['name'];

			return $backupjobs;
		}
	
} // end class Bweb
?>
