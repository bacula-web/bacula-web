<?php
/*
  +-------------------------------------------------------------------------+
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

 class Jobs_Model extends CModel {
	
	// ==================================================================================
	// Function: 	count_jobs()
	// Parameters:	$start_timestamp
	//				$end_timestamp
	//				$status
	//				$level
	//				$jobname
	//				$client
	// Return:		Total of jobs
	// ==================================================================================
	
	public static function count_Jobs( $pdo_connection, $period = array(), $job_status = null, $job_level = null) {
		$statment 	 		 = '';
		$where		 		 = array();
		$period_timestamp	 = array();
		$fields				 = array('COUNT(*) as job_count');
		
		// Check PDO object
		if( !is_a( $pdo_connection, 'PDO') and is_null($pdo_connection)  ) 
			throw new Exception('Unvalid PDO object provided in count_Jobs() function');

		// PDO object singleton
		if( is_null(parent::$pdo_connection) )
			parent::$pdo_connection = $pdo_connection;

		// Check period
		if( !is_array($period) )
			throw new Exception('Wrong period of missing array provided in count_Jobs() function');
		
		// Convert timestamp to date format
		foreach( $period as $timestamp ) {
			$period_timestamp[] = date( "Y-m-d H:i:s", $timestamp);
		}
		
		// Defined period
		switch( CDBUtils::getDriverName( parent::$pdo_connection ) )
		{
			case 'sqlite':
			case 'mysql':
				$where[] = "(EndTime BETWEEN '$period_timestamp[0]' AND '$period_timestamp[1]')";
			break;
			case 'pgsql':
				$where[] = "(endtime BETWEEN timestamp '$period_timestamp[0]' AND timestamp '$period_timestamp[1]')";
			break;
		}
		
		// Job status
		if( !is_null( $job_status )) {
			switch( strtolower($job_status) ){
				case 'running':
					$where[] = "JobStatus = 'R' ";
				break;
				case 'completed':
					$where[] = "JobStatus = 'T' ";
				break;
				case 'failed':
					$where[] = "JobStatus IN ('f','E') ";
				break;
				case 'canceled':
					$where[] = "JobStatus = 'A' ";
				break;
				case 'waiting':
					$where[] = "Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
				break;
			} // end switch
		}		
		
		// Job level
		if( !is_null($job_level) )
			$where[] = "Level = '$job_level' ";
		
		$statment = array( 'table' => parent::get_Table('Job'), $fields, 'where' => $where);
		$statment = CDBQuery::get_Select( $statment );
		
		// Execute SQL statment
		CDBUtils::runQuery($statment, parent::$pdo_connection);
	}
	
 }
 
?>
