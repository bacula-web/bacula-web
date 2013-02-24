<?php
/*
  +-------------------------------------------------------------------------+
  | Copyright (C) 2004 Juan Luis Frances Jimenez						    |
  | Copyright 2010-2013, Davide Franco			                            |
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
$view 	= new CView();
$dbSql 	= null;

try {
 $dbSql = new Bweb($view);

 // Running, completed, failed, waiting and canceled jobs status over last 24 hours
 $view->assign( 'running_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), 'running') );
 $view->assign( 'completed_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), 'completed') );
 $view->assign( 'failed_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), 'failed') );
 $view->assign( 'waiting_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), 'waiting') );
 $view->assign( 'canceled_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), 'canceled') );

 // Stored files number 
 $view->assign( 'stored_files', $dbSql->translate->get_Number_Format( JobsModel::getStoredFiles( $dbSql->db_link, array(FIRST_DAY, NOW) ) ) );
 
 // Overall stored bytes
 $view->assign( 'stored_bytes', CUtils::Get_Human_Size( JobsModel::getStoredBytes( $dbSql->db_link, array(FIRST_DAY, NOW) ) ) );

 // Database size
 $view->assign( 'database_size', Database_Model::get_Size( $dbSql->db_link, $dbSql->catalog_current_id ) );
 
 // Total bytes and files stored over the last 24 hours
 $view->assign( 'bytes_last', CUtils::Get_Human_Size( JobsModel::getStoredBytes( $dbSql->db_link, array(LAST_DAY, NOW) ) ) );
 $view->assign( 'files_last', $dbSql->translate->get_Number_Format( JobsModel::getStoredFiles( $dbSql->db_link, array(LAST_DAY, NOW) ) ) );

 // Number of clients
 $view->assign('clients', Clients_Model::count($dbSql->db_link) );

 // Defined Jobs and Filesets
 $view->assign( 'defined_filesets', FileSets_Model::count( $dbSql->db_link ) );
 $view->assign( 'defined_jobs', JobsModel::count_Job_Names( $dbSql->db_link ) );

 // Incremental, Differential and Full jobs over the last 24 hours
 $view->assign('incr_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), null, J_INCR) );
 $view->assign('diff_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), null, J_DIFF) );
 $view->assign('full_jobs', JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), null, J_FULL) );

 
 // Volumes disk usage
 $volumes_size = Volumes_Model::getDiskUsage($dbSql->db_link);
 $view->assign('volumes_size', CUtils::Get_Human_Size( $volumes_size ) );

 // Pools count
 $view->assign( 'pools_nb', Pools_Model::count( $dbSql->db_link ) );

 // Backup Job list
 $view->assign('jobs_list', JobsModel::get_Jobs_List($dbSql->db_link) );
 
 // Clients list
 $view->assign('clients_list', Clients_Model::getClients($dbSql->db_link) );

 // Count volumes
 $view->assign('volumes_nb', Volumes_Model::count($dbSql->db_link) );

 // ==============================================================
 // Last 24 hours Job status graph
 // ==============================================================
 $jobs_status = array('Running', 'Completed', 'Waiting', 'Failed', 'Canceled');
 $jobs_status_data = array();

 foreach ($jobs_status as $status) {
	$jobs_count 		= JobsModel::count_Jobs( $dbSql->db_link, array( LAST_DAY, NOW), strtolower($status) );
	$jobs_status_data[] = array($status, $jobs_count );
 }
 
 $graph = new CGraph("graph.png");
 $graph->SetData($jobs_status_data, 'pie');
 $graph->SetGraphSize(310, 200);

 // Graph rendering
 $view->assign( 'graph_jobs', $graph->Render() );
 unset($graph);

 // ==============================================================
 // Volumes by pools graph
 // ==============================================================

 $vols_by_pool = array();
 $graph = new CGraph("graph1.png");
 $max_pools = '9';
 $table_pool = 'Pool';
 $limit = '';
 $sum_vols = '';

 // Count defined pools in catalog
 $pools_count = Pools_Model::count( $dbSql->db_link );

 // Display 9 biggest pools and rest of volumes in 10th one display as Other
 if ($pools_count > $max_pools) {
    if( CDBUtils::getDriverName( $dbSql->db_link ) == 'pgsql') {
	$limit = $max_pools . 'OFFSET ' . ($pools_count - $max_pools);
    }else{
	$limit = $max_pools . ',' . ($pools_count - $max_pools);
    }

    $query = array('table' => $table_pool,
        'fields' => array('SUM(numvols) AS sum_vols'),
        'limit' => $limit,
        'groupby' => 'name');
    
	$result 	= CDBUtils::runQuery( CDBQuery::get_Select($query), $dbSql->db_link);
	$sum_vols 	= $result->fetch();
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

 if ($pools_count > $max_pools) 
 	$vols_by_pool[] = array('Others', $sum_vols['sum_vols']);

 $graph->SetData($vols_by_pool, 'pie');
 $graph->SetGraphSize(310, 220);

 // Graph rendering
 $view->assign( 'graph_pools', $graph->Render() );

 // ==============================================================
 // Last 7 days stored Bytes graph
 // ==============================================================
 $days_stored_bytes = array();
 $days = CTimeUtils::getLastDaysIntervals(7);

 foreach ($days as $day) {
    $stored_bytes 			= CUtils::Get_Human_Size( JobsModel::getStoredBytes( $dbSql->db_link, array($day['start'], $day['end']) ), 1, 'GB', false );
    $days_stored_bytes[] 	= array(date("m-d", $day['start']), $stored_bytes);
 } 
 
 $graph = new CGraph("graph2.png");
 $graph->SetData($days_stored_bytes, 'bars');
 $graph->SetGraphSize(310, 220);
 $graph->SetYTitle("GB");

 // Graph rendering
 $view->assign( 'graph_stored_bytes', $graph->Render() );


 // ==============================================================
 // Last used volumes graph
 // ==============================================================

 $last_volumes = array();

 // Building SQL statment
 $where = array();

 switch ( CDBUtils::getDriverName( $dbSql->db_link ) ) {
    case 'mysql':
    case 'pgsql':
        $where[] = "(Media.Volstatus != 'Disabled') OR (Media.LastWritten IS NOT NULL)";
        break;
    case 'sqlite':
        $where[] = "(Media.Lastwritten != 0)";
        break;
 }

 $statment = array(	'table' 	=> 'Media',
					'fields' 	=> array('Media.MediaId', 'Media.Volumename', 'Media.Lastwritten', 'Media.VolStatus', 'Media.VolJobs', 'Pool.Name AS poolname'),
					'join' 		=> array('table' => 'Pool', 'condition' => 'Media.PoolId = Pool.poolid'),
					'where' 	=> $where,
					'orderby' 	=> 'Media.Lastwritten DESC',
					'limit' 	=> '10');

 // Run the query
 $result 	= CDBUtils::runQuery( CDBQuery::get_Select($statment), $dbSql->db_link );

 foreach( $result as $volume ) {
    // odd or even row
    if ((count($last_volumes) % 2) > 0)
        $volume['odd_even'] = "even";

    $last_volumes[] = $volume;
 }

 $view->assign('volumes_list', $last_volumes);
 
 }catch (Exception $e) {
    CErrorHandler::displayError($e);
 }
 
 // Render template
 $view->render('index.tpl');
?>
