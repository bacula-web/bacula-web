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
	session_start();
	include_once( 'core/global.inc.php' );

	try{
		$dbSql = new Bweb();
	}catch( Exception $e  ) {
		CErrorHandler::displayError($e);
    }
	
	// Stored files number 
	$dbSql->tpl->assign('stored_files', $dbSql->translate->get_Number_Format($dbSql->getStoredFiles( FIRST_DAY, NOW ) ) );
	  
	// Database size
	$dbSql->tpl->assign('database_size', $dbSql->getDatabaseSize());

	// Overall stored bytes
	$stored_bytes = CUtils::Get_Human_Size( $dbSql->getStoredBytes( FIRST_DAY, NOW ) );
	$dbSql->tpl->assign('stored_bytes', $stored_bytes);

	// Total bytes and files for last 24 hours
	$dbSql->tpl->assign('bytes_last', CUtils::Get_Human_Size( $dbSql->getStoredBytes( LAST_DAY, NOW ) ) );
	$dbSql->tpl->assign('files_last', $dbSql->translate->get_Number_Format($dbSql->getStoredFiles( LAST_DAY, NOW ) ) );

	// Number of clients
	$nb_clients = $dbSql->Get_Nb_Clients();
	$dbSql->tpl->assign('clients', $nb_clients["nb_client"] );

	// Backup Job list
	$dbSql->tpl->assign( 'jobs_list', $dbSql->getJobsName() );
	
	// Clients list

	$dbSql->tpl->assign( 'clients_list', $dbSql->get_Clients() );

	// Last 24 hours status (completed, failed and waiting jobs)
	$dbSql->tpl->assign( 'completed_jobs', $dbSql->countJobs( LAST_DAY, NOW, 'completed' ) );
	$dbSql->tpl->assign( 'failed_jobs', $dbSql->countJobs( LAST_DAY, NOW, 'failed' ) );
	$dbSql->tpl->assign( 'waiting_jobs', $dbSql->countJobs( LAST_DAY, NOW, 'waiting' ) );
	$dbSql->tpl->assign( 'canceled_jobs', $dbSql->countJobs( LAST_DAY, NOW, 'canceled' ) );

	// Last 24 hours jobs Level
	$dbSql->tpl->assign( 'incr_jobs', $dbSql->countJobs( LAST_DAY, NOW, 'ALL', J_INCR) );
	$dbSql->tpl->assign( 'diff_jobs', $dbSql->countJobs( LAST_DAY, NOW, 'ALL', J_DIFF) );
	$dbSql->tpl->assign( 'full_jobs', $dbSql->countJobs( LAST_DAY, NOW, 'ALL', J_FULL) );

	// Last 24 hours Job status graph
	$jobs_status_data = array();
	$jobs_status 	  = array( 'Running', 'Completed', 'Failed', 'Canceled', 'Waiting' );

	foreach( $jobs_status as $status )
		$jobs_status_data[] = array( $status, $dbSql->countJobs(LAST_DAY, NOW, $status) );

	$graph = new CGraph( "graph.png" );
	$graph->SetData( $jobs_status_data, 'pie', 'text-data-single' );
	$graph->SetGraphSize( 310, 200 );

	$graph->Render();
	$dbSql->tpl->assign('graph_jobs', $graph->Get_Image_file() );
	unset($graph);

	// Volumes by pools graph
	$vols_by_pool = array();
	$graph 	      = new CGraph( "graph1.png" );
	$max_pools	  = '9';

	// count pools
	$query = array('table'=>'pool', 'fields'=>array('count(*) as pools_count') );
	$result = $dbSql->db_link->runQuery( CDBQuery::getQuery($query) );
	$pools_count = $result->fetch();
	
	// Sum volumes for rest of pool (not 9 biggers pools)
	$query = array( 'table'=>'pool', 
	                'fields'=>array('SUM(numvols) AS sum_vols'), 
					'limit' => $max_pools.','.($pools_count['pools_count']-$max_pools),
					'groupby' => 'name');
	$result = $dbSql->db_link->runQuery( CDBQuery::getQuery($query) );
	$sum_vols = $result->fetch();
	
	$query = array('table'=>'pool', 'fields'=>array('poolid,name,numvols'), 'orderby'=>'numvols DESC', 'limit'=>$max_pools );
	$result = $dbSql->db_link->runQuery( CDBQuery::getQuery($query) );
	
	foreach( $result->fetchall() as $pool ) {
		$vols_by_pool[] = array( $pool['name'], $pool['numvols'] );
	}
	
	$vols_by_pool[] = array( 'Others', $sum_vols['sum_vols']);

	$graph->SetData( $vols_by_pool, 'pie', 'text-data-single' );
	$graph->SetGraphSize( 310, 200 );

	$graph->Render();
	$dbSql->tpl->assign('graph_pools', $graph->Get_Image_file() );

	// Last 7 days stored Bytes graph
	$days_stored_bytes 	= array();
	$days = CTimeUtils::getLastDaysIntervals( 7 );
	
	foreach( $days as $day ) {
		$stored_bytes 		 = $dbSql->getStoredBytes( $day['start'], $day['end']);
		$stored_bytes 		 = CUtils::Get_Human_Size( $stored_bytes, 1, 'GB', false );
		$days_stored_bytes[] = array( date("m-d", $day['start']), $stored_bytes );  
	}

	$graph = new CGraph( "graph2.png" );
	$graph->SetData( $days_stored_bytes, 'bars', 'text-data' );
	$graph->SetGraphSize( 310, 200 );
	$graph->SetYTitle( "GB" );

	$graph->Render();
	$dbSql->tpl->assign('graph_stored_bytes', $graph->Get_Image_file() );

    // Last used volumes
	$last_volumes = array();
	
	// Building SQL statment
	$where = '';
	
	switch( $dbSql->db_link->getDriver() ) {
	  case 'mysql':
	  case 'pgsql':
	    $where = "(Media.Volstatus != 'Disabled') OR (Media.LastWritten IS NOT NULL)";
	  break;
	  case 'sqlite':
	    $where = "(Media.Lastwritten != 0)";
	  break;
	}
	
	$query = array( 'table' => 'Media', 
					'fields' => array( 'Media.MediaId', 'Media.Volumename','Media.Lastwritten','Media.VolStatus','Pool.Name as poolname'),
					'join' => array( 'table'=>'Pool', 'condition'=>'Media.PoolId = Pool.poolid'),
					'where' => $where, 
					'orderby' => 'Media.Lastwritten DESC', 
					'limit'=>'10' );

	// Run the query
	$result = $dbSql->db_link->runQuery( CDBQuery::getQuery($query) );
			
	foreach( $result->fetchAll() as $volume ) {
		$query			      = CDBQuery::getQuery( array( 'table'=>'JobMedia', 'fields' => array( 'COUNT(*) as jobs_count'), 
								                           'where' => "JobMedia.MediaId = '".$volume['mediaid']."'" ) );
		$jobs_by_vol 		  = $dbSql->db_link->runQuery($query);
		$jobs_by_vol 		  = $jobs_by_vol->fetchAll();
			
		// Volumes details
		$volume['jobs_count'] = $jobs_by_vol[0]['jobs_count'];
			
		// odd or even row
		if( (count($last_volumes) % 2) > 0 )
			$volume['odd_even'] = "odd";
			
		$last_volumes[] 	  = $volume;
	}

	$dbSql->tpl->assign( 'volumes_list', $last_volumes );	

	// Render template
	$dbSql->tpl->display('index.tpl');
?>
