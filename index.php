<?php
/* 
+-------------------------------------------------------------------------+
| Copyright (C) 2004 Juan Luis Francés Jiménez                            |
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
require_once('paths.php');
include_once( 'bweb.inc.php' );

$dbSql = new Bweb();

//require("lang.php");

$mode = "";				

/*
// Getting mode from config file
$mode = $dbSql->get_config_param("mode");
if( $mode == false )
	$mode = "Lite";

$smarty->assign( "mode", $mode );
*/

// Assign to template catalogs number
$dbSql->tpl->assign( "dbs", $dbSql->Get_Nb_Catalogs() );

//Assign dbs
/*
if ( count($dbSql->dbs) >1 ) {
  $smarty->assign("dbs", $dbSql->dbs);
  $smarty->assign("dbs_now", $_SESSION['DATABASE']);
}
*/

// Stored files number 
$totalfiles = $dbSql->GetStoredFiles( ALL );
$dbSql->tpl->assign('stored_files',$totalfiles);
  
// Database size
$dbSql->tpl->assign('database_size', $dbSql->GetDbSize());

// Overall stored bytes
$result = $dbSql->GetStoredBytes( ALL );
$dbSql->tpl->assign('stored_bytes', $dbSql->human_file_size($result['stored_bytes']) );

// Total stored bytes since last 24 hours
$result = $dbSql->GetStoredBytes( LAST_DAY );
$dbSql->tpl->assign('bytes_last', $dbSql->human_file_size($result['stored_bytes']) );

// Total stored files since last 24 hours
$files_last = $dbSql->GetStoredFiles( LAST_DAY );
$dbSql->tpl->assign('files_last', $files_last );


// Number of clients
$nb_clients = $dbSql->Get_Nb_Clients();
$dbSql->tpl->assign('clientes_totales',$nb_clients["nb_client"] );

// Backup Job list for report.tpl and last_run_report.tpl
$dbSql->tpl->assign( 'jobs_list', $dbSql->Get_BackupJob_Names() );

// Get volumes list (volumes.tpl)
$dbSql->tpl->assign('pools', $dbSql->GetVolumeList() );

// Last 24 hours completed jobs number
$dbSql->tpl->assign( 'completed_jobs', $dbSql->CountJobs( LAST_DAY, 'completed' ) );

// Last 24 hours failed jobs number
$dbSql->tpl->assign( 'failed_jobs', $dbSql->CountJobs( LAST_DAY, 'failed' ) );

// Last 24 hours waiting jobs number
$dbSql->tpl->assign( 'waiting_jobs', $dbSql->CountJobs( LAST_DAY, 'waiting' ) );

// Last 24 hours elapsed time (last_run_report.tpl)
//$smarty->assign( 'elapsed_jobs', $dbSql->Get_ElapsedTime_Job() );

// Last 24 hours Job Levels
$dbSql->tpl->assign( 'incr_jobs', $dbSql->CountJobsbyLevel( LAST_DAY, 'I') );
$dbSql->tpl->assign( 'diff_jobs', $dbSql->CountJobsbyLevel( LAST_DAY, 'D') );
$dbSql->tpl->assign( 'full_jobs', $dbSql->CountJobsbyLevel( LAST_DAY, 'F') );

// Last 24 hours Job status graph
$data   = array();  
$status = array( 'completed', 'terminated_errors', 'failed', 'waiting', 'created', 'running', 'error' );

foreach( $status as $job_status ) {
	array_push( $data, $dbSql->GetJobsStatistics( $job_status ) );
}

$graph = new BGraph( "graph.png" );
$graph->SetData( $data, 'pie', 'text-data-single' );
$graph->SetGraphSize( 400, 230 );

$graph->Render();
$dbSql->tpl->assign('graph_jobs', $graph->Get_Image_file() );
unset($graph);

// Pool and volumes graph
$data = array();
$graph = new BGraph( "graph1.png" );

$pools = $dbSql->Get_Pools_List();

foreach( $pools as $pool ) {
	array_push( $data, $dbSql->GetPoolsStatistics( $pool ) );
}

$graph->SetData( $data, 'pie', 'text-data-single' );
$graph->SetGraphSize( 400, 230 );

$graph->Render();
$dbSql->tpl->assign('graph_pools', $graph->Get_Image_file() );

// Last 7 days stored Bytes graph
$data  = array();
$graph = new BGraph( "graph2.png" );
$days  = array();

// Get the last 7 days interval (start and end)
for( $c = 6 ; $c >= 0 ; $c-- ) {
	$today = ( mktime() - ($c * LAST_DAY) );
	array_push( $days, array( 'start' => date( "Y-m-d 00:00:00", $today ), 'end' => date( "Y-m-d 23:59:00", $today ) ) );
}

$days_stored_bytes = array();

foreach( $days as $day ) {
  array_push( $days_stored_bytes, $dbSql->GetStoredBytesByInterval( $day['start'], $day['end'] ) );
}

$graph->SetData( $days_stored_bytes, 'bars', 'text-data' );
$graph->SetGraphSize( 400, 230 );

$graph->Render();
$dbSql->tpl->assign('graph_stored_bytes', $graph->Get_Image_file() );

// Last 15 used volumes
$vol_list = array();

$query  = "SELECT DISTINCT Media.Volumename, Media.Lastwritten, Media.VolStatus, Job.JobId FROM Job ";
$query .= "LEFT JOIN JobMedia ON Job.JobId = JobMedia.JobId ";
$query .= "LEFT JOIN Media ON JobMedia.MediaId = Media.MediaId ";
$query .= "ORDER BY Job.JobId DESC ";
$query .= "LIMIT 10 ";

$result = $dbSql->db_link->query( $query );

if ( PEAR::isError( $result ) )
	die( "Unable to get last used volumes from catalog \n " . $result->getMessage() );
else {
	while ( $vol = $result->fetchRow( DB_FETCHMODE_ASSOC ) ) 
		array_push( $vol_list, $vol );
}
$dbSql->tpl->assign( 'volume_list', $vol_list );	

//if ($_GET['Full_popup'] == "yes" || $_GET['pop_graph1'] == "yes" || $_GET['pop_graph2'] == "yes")
//        $smarty->display('full_popup.tpl');
//else

// Render template
$dbSql->tpl->display('index.tpl');
?>
