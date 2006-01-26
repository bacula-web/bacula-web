<?
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
// Last Err: 
session_start();
require ("paths.php");
require($smarty_path."Smarty.class.php");
include "classes.inc";

$smarty = new Smarty;                                                                                                                   // Template engine
$dbSql = new Bweb();

//$smarty->compile_check = true;
//$smarty->debugging = true;
$smarty->template_dir = "./templates";
$smarty->compile_dir = "./templates_c";
$smarty->config_dir     = "./configs";
$smarty->config_load("bacula.conf");                                                                                    // Load config file
$mode = $smarty->get_config_vars("mode");                                                                               // Lite o Extend?

require("lang.php");

//Assign dbs
if ( count($dbSql->dbs) >1 ) {
  $smarty->assign("dbs", $dbSql->dbs);
  $smarty->assign("dbs_now", $_SESSION['DATABASE']);
}

// generaldata.tpl & last_run_report.tpl (last24bytes)
$client = $dbSql->link->query("select count(*) from Client")
        or die ("Error query: 1");
$totalfiles = $dbSql->link->query("select count(FilenameId) from Filename")
        or die ("Error query: 2");
if ( $dbSql->driver == "mysql")
  $last24bytes = $dbSql->link->query("select sum(JobBytes),count(*) from Job where Endtime <= NOW() and UNIX_TIMESTAMP(EndTime) > UNIX_TIMESTAMP(NOW())-86400")
        or die ("Error query: 3");
if ( $dbSql->driver == "pgsql")
  $last24bytes = $dbSql->link->query("select sum(JobBytes),count(*) from Job where Endtime <= NOW() and EndTime > NOW() - 86400 * interval '1 second'")
        or die ("Error query: 3");
$bytes_stored =& $dbSql->link->getOne("select SUM(VolBytes) from Media")
        or die ("Error query: 4");

$smarty->assign('database_size', $dbSql->GetDbSize());
$smarty->assign('bytes_stored',$bytes_stored);

$tmp = $client->fetchRow();
$smarty->assign('clientes_totales',$tmp[0]);

$tmp = $last24bytes->fetchRow();
/*if ( empty($tmp[0]) ) {                                                                                                                 // No data for last 24, search last 48
        if ( $dbSql->driver == "mysql" )
          $last24bytes = $dbSql->link->query("select sum(JobBytes) from Job where Endtime <= NOW() and UNIX_TIMESTAMP(EndTime) > UNIX_TIMESTAMP(NOW())-172800" );
        if ( $dbSql->driver == "pgsql")
          $last24bytes = $dbSql->link->query("select sum(JobBytes) from Job where Endtime <= NOW() and EndTime > NOW()-172800 * interval '1 second'" )
            or die ("Error query: 4.1");
        $smarty->assign('when',"yesterday");
        $tmp = $last24bytes->fetchRow();        
}*/

$smarty->assign('bytes_totales',$tmp[0]);
$smarty->assign('total_jobs', $tmp[1]);

$tmp = $totalfiles->fetchRow();
$smarty->assign('files_totales',$tmp[0]);

$client->free();
$totalfiles->free();
$last24bytes->free();



// report_select.tpl & last_run_report.tpl
$res = $dbSql->link->query("select Name from Job group by Name");
$a_jobs = array();
while ( $tmp = $res->fetchRow() )
        array_push($a_jobs, $tmp[0]);
$smarty->assign('total_name_jobs',$a_jobs);
$smarty->assign('time2',( (time())-2678400) );                                                                  // Current time - 1 month. <select> date
$res->free();

// volumes.tpl
$volumes = $dbSql->GetDataVolumes();                                                                                    // Obtain array with info
$pools = array_keys($volumes);                                                                                                  // Extract Pools
$smarty->assign('pools',$pools);        
$smarty->assign('volumes',$volumes);

// last_run_report.tpl
if ($mode == "Lite" && $_GET['Full_popup'] != "yes") {
        $tmp = array();
        if ( $dbSql->driver == "mysql" )
          $status = $dbSql->link->query("select JobId,Name,EndTime,JobStatus from Job where EndTime <= NOW() and UNIX_TIMESTAMP(EndTime) >UNIX_TIMESTAMP(NOW())-86400 and JobStatus!='T'" )               
                or die ("Error: query at row 95");
        if ( $dbSql->driver == "pgsql" )
          $status = $dbSql->link->query("select JobId,Name,EndTime,JobStatus from Job where EndTime <= NOW() and EndTime >NOW() - 86400 * interval '1 second' and JobStatus!= 'T'")
                or die ( "Error: query at row 98" );
        $smarty->assign('status', $status->numRows());
        if ( $status->numRows() ) {
                while ( $res = $status->fetchRow() )
                        array_push($tmp, $res);
                $smarty->assign('errors_array',$tmp);
        }
        $status->free();
        
        // Total Elapsed Time. Only for single Job.
        if ( $dbSql->driver == "mysql" )
          $ret = $dbSql->link->query("select UNIX_TIMESTAMP(EndTime)-UNIX_TIMESTAMP(StartTime) as elapsed from Job where EndTime <= NOW() and UNIX_TIMESTAMP(EndTime) > UNIX_TIMESTAMP(NOW())-84600")
                or die ("Error at row 110");
        if ( $dbSql->driver == "pgsql" )
          $ret = $dbSql->link->query("select EndTime - StartTime as elapsed from Job where EndTime <= NOW() and EndTime > NOW() - 84600 * interval '1 second'")
                or die ("Error at row 113");
        while ( $res = $ret->fetchRow() ) {
                if ( $TotalElapsed < 1000000000 )                                                                               // Temporal "workaround" ;) Fix later
                        $TotalElapsed += $res[0];
        }
        if ($TotalElapsed > 86400)                                                                                                      // More than 1 day!
                $TotalElapsed = gmstrftime("%d days %H:%M:%S", $TotalElapsed);
        else
                $TotalElapsed = gmstrftime("%H:%M:%S", $TotalElapsed);
        $smarty->assign('TotalElapsed',$TotalElapsed);
        $ret->free();
}
else if ($mode == "Full" || $_GET['Full_popup'] == "yes" ){
        $tmp1 = array();
        if ( $dbSql->driver == "mysql")
                $query = "select SEC_TO_TIME( UNIX_TIMESTAMP(Job.EndTime)-UNIX_TIMESTAMP(Job.StartTime) )
                                as elapsed,Job.Name,Job.StartTime,Job.EndTime,Job.Level,Pool.Name,Job.JobStatus from Job 
                                LEFT JOIN Pool ON Job.PoolId=Pool.PoolId where EndTime <= NOW() and UNIX_TIMESTAMP(EndTime) >UNIX_TIMESTAMP(NOW())-86400 
                                order by elapsed ";                                                                                                     // Full report array
        if ( $dbSql->driver == "pgsql")
                $query = "select (Job.EndTime - Job.StartTime )
                                as elapsed,Job.Name,Job.StartTime,Job.EndTime,Job.Level,Pool.Name,Job.JobStatus from Job
                                LEFT JOIN Pool ON Job.PoolId=Pool.PoolId where EndTime <= NOW() and EndTime > NOW() - 86400 * interval '1 second'
                                order by elapsed ";
        $status = $dbSql->link->query($query)
                or die ("Error: query at row 138");
        while ( $tmp = $status->fetchRow() ) {
                $tdate = explode (":",$tmp[0]);
                if ( $tdate[0] > 300000 )                                                                                               // Temporal "workaround" ;) Fix later
                        $tmp[0] = "00:00:00";
                array_push($tmp1,$tmp);
        }
        
        $smarty->assign('clients',$tmp1);
}  


if ($_GET['Full_popup'] == "yes" || $_GET['pop_graph1'] == "yes" || $_GET['pop_graph2'] == "yes")
        $smarty->display('full_popup.tpl');
else
        $smarty->display('index.tpl');
?>
