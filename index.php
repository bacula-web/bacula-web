<?php

/*
  +-------------------------------------------------------------------------+
  | Copyright (C) 2004 Juan Luis Frances Jimenez						    |
  | Copyright 2010-2012, Davide Franco			                            |
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

// Initialise view and model
$view = new CView();

try {
    $dbSql = new Bweb($view);
} catch (Exception $e) {
    CErrorHandler::displayError($e);
}

// Stored files number 
$view->assign( 'stored_files', $dbSql->translate->get_Number_Format($dbSql->getStoredFiles(FIRST_DAY, NOW)));

// Database size
$view->assign( 'database_size', $dbSql->getDatabaseSize());

// Overall stored bytes
$stored_bytes = CUtils::Get_Human_Size( $dbSql->getStoredBytes( FIRST_DAY, NOW ) );
$view->assign( 'stored_bytes', $stored_bytes);

// Total bytes and files stored over the last 24 hours
$view->assign( 'bytes_last', CUtils::Get_Human_Size( $dbSql->getStoredBytes(LAST_DAY, NOW ) ) );
$view->assign( 'files_last', $dbSql->translate->get_Number_Format( $dbSql->getStoredFiles( LAST_DAY, NOW ) ) );

// Number of clients
$nb_clients = $dbSql->Get_Nb_Clients();
$view->assign('clients', $nb_clients["nb_client"]);

// Defined Jobs and Filesets
$defined_filesets = $dbSql->countFilesets();
$defined_jobs = count( $dbSql->getJobsName() );

// Volumes size
$volumes_size = $dbSql->getVolumesSize();
$view->assign('volumes_size', CUtils::Get_Human_Size( $volumes_size ) );

$view->assign('defined_filesets', $defined_filesets);
$view->assign('defined_jobs', $defined_jobs);

// Backup Job list
$view->assign('jobs_list', $dbSql->getJobsName());

// Clients list
$view->assign('clients_list', $dbSql->get_Clients());

// Count pools
$view->assign('pools_nb', $dbSql->countPools() );

// Count volumes
$view->assign('volumes_nb', $dbSql->countVolumes() );

// Last 24 hours status
// Completed jobs
$completed_jobs = $dbSql->countJobs(LAST_DAY, NOW, 'completed');
$view->assign('completed_jobs', $completed_jobs);
// Failed jobs
$failed_jobs = $dbSql->countJobs(LAST_DAY, NOW, 'failed');
$view->assign('failed_jobs', $failed_jobs);
// Waiting jobs
$waiting_jobs = $dbSql->countJobs(LAST_DAY, NOW, 'waiting');
$view->assign('waiting_jobs', $waiting_jobs);
// Canceled jos
$canceled_jobs = $dbSql->countJobs(LAST_DAY, NOW, 'canceled');
$view->assign('canceled_jobs', $canceled_jobs);

// Last 24 hours jobs Level
// Incremental jobs
$incremental_jobs = $dbSql->countJobs(LAST_DAY, NOW, 'ALL', J_INCR);
$view->assign('incr_jobs', $incremental_jobs);
// Differential jobs
$differential_jobs = $dbSql->countJobs(LAST_DAY, NOW, 'ALL', J_DIFF);
$view->assign('diff_jobs', $differential_jobs);
// Full jobs
$full_jobs = $dbSql->countJobs(LAST_DAY, NOW, 'ALL', J_FULL);
$view->assign('full_jobs', $full_jobs);

// Last 24 hours Job status graph
$jobs_status = array('Running', 'Completed', 'Failed', 'Canceled', 'Waiting');
$jobs_status_data = array();

foreach ($jobs_status as $status) {
    $jobs_count = $dbSql->countJobs(LAST_DAY, NOW, $status);
	$jobs_status_data[] = array($status, $jobs_count );
}

$graph = new CGraph("graph.png");
$graph->SetData($jobs_status_data, 'pie');
$graph->SetGraphSize(310, 200);

// Graph rendering
$view->assign( 'graph_jobs', $graph->Render() );
unset($graph);

// Volumes by pools graph
$vols_by_pool = array();
$graph = new CGraph("graph1.png");
$max_pools = '9';
$table_pool = 'Pool';
$limit = '';
$sum_vols = '';

// Count defined pools in catalog
$pools_count = $dbSql->countPools();

// Display 9 biggest pools and rest of volumes in 10th one display as Other
if ($pools_count > $max_pools) {
    $limit = $max_pools . ',' . ($pools_count - $max_pools);

    $query = array('table' => $table_pool,
        'fields' => array('SUM(numvols) AS sum_vols'),
        'limit' => $limit,
        'groupby' => 'name');
    $result = $dbSql->db_link->runQuery( CDBQuery::get_Select($query) );
    $sum_vols = $result->fetch();

    $vols_by_pool[] = array('Others', $sum_vols['sum_vols']);
} else {
    $limit = $pools_count;
}

// Check database driver for pool table name
if ( CDBUtils::getDriverName( $dbSql->db_link ) == 'pgsql') {
	$table_pool = strtolower($table_pool);
}

$query = array('table' => $table_pool, 'fields' => array('poolid,name,numvols'), 'orderby' => 'numvols DESC', 'limit' => $max_pools);
$result = CDBUtils::runQuery( CDBQuery::get_Select($query), $dbSql->db_link );

foreach ($result as $pool) {
    $vols_by_pool[] = array($pool['name'], $pool['numvols']);
}

$graph->SetData($vols_by_pool, 'pie');
$graph->SetGraphSize(310, 200);

// Graph rendering
$view->assign( 'graph_pools', $graph->Render() );

// Last 7 days stored Bytes graph
$days_stored_bytes = array();
$days = CTimeUtils::getLastDaysIntervals(7);

foreach ($days as $day) {
    $stored_bytes = $dbSql->getStoredBytes($day['start'], $day['end']);
    $stored_bytes = CUtils::Get_Human_Size($stored_bytes, 1, 'GB', false);
    $days_stored_bytes[] = array(date("m-d", $day['start']), $stored_bytes);
}

$graph = new CGraph("graph2.png");
$graph->SetData($days_stored_bytes, 'bars');
$graph->SetGraphSize(310, 200);
$graph->SetYTitle("GB");

// Graph rendering
$view->assign( 'graph_stored_bytes', $graph->Render() );

// Last used volumes
$last_volumes = array();

// Building SQL statment
$where = '';

switch ( CDBUtils::getDriverName( $dbSql->db_link ) ) {
    case 'mysql':
    case 'pgsql':
        $where = "(Media.Volstatus != 'Disabled') OR (Media.LastWritten IS NOT NULL)";
        break;
    case 'sqlite':
        $where = "(Media.Lastwritten != 0)";
        break;
}

$query = array('table' => 'Media',
    'fields' => array('Media.MediaId', 'Media.Volumename', 'Media.Lastwritten', 'Media.VolStatus', 'Pool.Name as poolname'),
    'join' => array('table' => 'Pool', 'condition' => 'Media.PoolId = Pool.poolid'),
    'where' => $where,
    'orderby' => 'Media.Lastwritten DESC',
    'limit' => '10');

// Run the query
$result = CDBUtils::runQuery( CDBQuery::get_Select($query), $dbSql->db_link );

foreach ( $result as $volume ) {
    $query = array( 'table' => 'JobMedia', 'fields' => array( 'COUNT(*) as jobs_count' ),
					'where' => "JobMedia.MediaId = '" . $volume['mediaid'] . "'"); 
					
	$jobs_by_vol = CDBUtils::runQuery( CDBQuery::get_Select($query), $dbSql->db_link );
	$jobs_by_vol = $jobs_by_vol->fetch();

    $volume['jobs_count'] = $jobs_by_vol['jobs_count'];

    // odd or even row
    if ((count($last_volumes) % 2) > 0)
        $volume['odd_even'] = "even";

    $last_volumes[] = $volume;
}

$view->assign('volumes_list', $last_volumes);

// Render template
$view->render('index.tpl');
?>
