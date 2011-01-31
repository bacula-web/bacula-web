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
require ("paths.php");
require($smarty_path."Smarty.class.php");
include "bweb.inc.php";

$smarty = new Smarty();     
$dbSql = new Bweb();

require("lang.php");

$mode = "";				

$smarty->compile_check = true;
$smarty->debugging = false;
$smarty->force_compile = true;

$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir     = "./configs";

/*
$smarty->config_load("bacula.conf");                                                                                    // Load config file
$mode = $smarty->get_config_vars("mode");     
*/                                                                          // Lite o Extend?

// Getting mode from config file
$mode = $dbSql->get_config_param("mode");
if( $mode == false )
	$mode = "Lite";

$smarty->assign( "mode", $mode );

// Determine which template to show
$indexreport = $dbSql->get_config_param( "IndexReport" );

if( $indexreport == 0 ) {
	$smarty->assign( "last_report", "last_run_report.tpl" );
}else {
	$smarty->assign( "last_report", "report_select.tpl" );
}

// Assign to template catalogs number
$smarty->assign( "dbs", $dbSql->Get_Nb_Catalogs() );

//Assign dbs
/*
if ( count($dbSql->dbs) >1 ) {
  $smarty->assign("dbs", $dbSql->dbs);
  $smarty->assign("dbs_now", $_SESSION['DATABASE']);
}
*/

// generaldata.tpl & last_run_report.tpl ( Last 24 hours report )
$last24bytes = "";
$query = "";

// Stored files number 
$totalfiles = $dbSql->GetStoredFiles();
$smarty->assign('files_totales',$totalfiles);
  
// Database size
$smarty->assign('database_size', $dbSql->GetDbSize());

// Overall stored bytes
$result = $dbSql->GetStoredBytes( ALL );
$smarty->assign('stored_bytes', $dbSql->human_file_size($result['stored_bytes']) );

// Total stored bytes since last 24 hours
$result = $dbSql->GetStoredBytes( LAST_DAY );
$smarty->assign('bytes_totales', $dbSql->human_file_size($result['stored_bytes']) );

// Number of clients
$nb_clients = $dbSql->Get_Nb_Clients();
$smarty->assign('clientes_totales',$nb_clients["nb_client"] );

// Backup Job list for report.tpl and last_run_report.tpl
$smarty->assign( 'total_name_jobs', $dbSql->Get_BackupJob_Names() );

// Get volumes list (volumes.tpl)
$smarty->assign('pools', $dbSql->GetVolumeList() );

// Last 24 hours completed jobs number
$smarty->assign( 'completed_jobs', $dbSql->CountJobs( LAST_DAY, 'completed' ) );

// Last 24 hours failed jobs number
$smarty->assign( 'failed_jobs', $dbSql->CountJobs( LAST_DAY, 'failed' ) );

// Last 24 hours waiting jobs number
$smarty->assign( 'waiting_jobs', $dbSql->CountJobs( LAST_DAY, 'waiting' ) );

// Last 24 hours elapsed time (last_run_report.tpl)
//$smarty->assign( 'elapsed_jobs', $dbSql->Get_ElapsedTime_Job() );

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
$smarty->assign('graph_jobs', $graph->Get_Image_file() );
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
$smarty->assign('graph_pools', $graph->Get_Image_file() );

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
$smarty->assign('graph_stored_bytes', $graph->Get_Image_file() );

// Last 15 used volumes
$vol_list = array();

$query  = "SELECT DISTINCT Media.Volumename, Media.VolStatus, Job.JobId FROM Job ";
$query .= "LEFT JOIN JobMedia ON Job.JobId = JobMedia.JobId ";
$query .= "LEFT JOIN Media ON JobMedia.MediaId = Media.MediaId ";
$query .= "ORDER BY Job.JobId DESC ";
$query .= "LIMIT 15 ";

$result = $dbSql->db_link->query( $query );

if ( PEAR::isError( $result ) )
	die( "Unable to get last used volumes from catalog \n " . $result->getMessage() );
else {
	while ( $vol = $result->fetchRow( DB_FETCHMODE_ASSOC ) ) 
		array_push( $vol_list, $vol );
}
$smarty->assign( 'volume_list', $vol_list );	

//if ($_GET['Full_popup'] == "yes" || $_GET['pop_graph1'] == "yes" || $_GET['pop_graph2'] == "yes")
//        $smarty->display('full_popup.tpl');
//else

// Render template
$smarty->display('index.tpl');
?>
