<?php
session_start();
include_once( 'config.inc.php' );

$dbSql = new Bweb();

if ( $_GET['default'] == 1) {                                                                                                   // Default params, 1 month
        $dbSql->StartDate = strftime("%Y-%m-%d %H:%M:%S",time()-2678400);
        $dbSql->EndDate = strftime("%Y-%m-%d %H:%M:%S",time());
}
else                                                                                                                                            // With params
        $dbSql->PrepareDate($_GET['StartDateMonth'],$_GET['StartDateDay'],$_GET['StartDateYear'],$_GET['EndDateMonth'],$_GET['EndDateDay'],$_GET['EndDateYear']);
        
$bytes = $dbSql->CalculateBytesPeriod($_GET['server'],$dbSql->StartDate,$dbSql->EndDate);
$files = $dbSql->CalculateFilesPeriod($_GET['server'],$dbSql->StartDate,$dbSql->EndDate);
$smarty->assign('startperiod',$dbSql->StartDate);
$smarty->assign('endperiod',$dbSql->EndDate); 
$smarty->assign('bytesperiod',$dbSql->human_file_size( $bytes ) );
$smarty->assign('filesperiod',$files);

// Array with jobs data
$a_jobs = array();
if ($dbSql->driver == "mysql")
        $res_jobs = $dbSql->db_link->query("select *,SEC_TO_TIME( UNIX_TIMESTAMP(Job.EndTime)-UNIX_TIMESTAMP(Job.StartTime) ) as elapsed from Job where EndTime < '$dbSql->EndDate' and EndTime > '$dbSql->StartDate' and Name='$_GET[server]' order by EndTime")
                or die("Error query row 50");
else if ($dbSql->driver == "pgsql")
        $res_jobs = $dbSql->db_link->query("select jobid as \"JobId\",job as \"Job\",name as \"Name\",type as \"Type\",level as \"Level\",clientid as \"ClientId\",jobstatus as \"JobStatus\",schedtime as \"SchedTime\",starttime as \"StartTime\",endtime as \"EndTime\",jobtdate as \"JobtDate\",volsessionid as \"VolSessionId\",volsessiontime as \"VolSessionTime\",jobfiles as \"JobFiles\",jobbytes as \"JobBytes\",joberrors as \"JobErrors\",jobmissingfiles as \"JobMissingFiles\",poolid as \"PoolId\",filesetid as \"FilesetId\",purgedfiles as \"PurgedFiles\",hasbase,Job.EndTime::timestamp-Job.StartTime::timestamp as elapsed from Job where EndTime < '$dbSql->EndDate' and EndTime > '$dbSql->StartDate' and Name='$_GET[server]' order by EndTime")
                or die("Error query row 56");

while ( $tmp = $res_jobs->fetchRow(DB_FETCHMODE_ASSOC) ) {
        $tdate = explode (":",$tmp['elapsed']);		// Temporal "workaround" ;) Fix later
        if ( $tdate[0] > 300000 )
                $tmp['elapsed'] = "00:00:00";
		$tmp['JobBytes'] = $dbSql->human_file_size( $tmp['JobBytes'] );
        array_push($a_jobs,$tmp);
}
$smarty->assign('jobs',$a_jobs);

// report_select.tpl
$res = $dbSql->db_link->query("select Name from Job group by Name");
$a_jobs = array();
while ( $tmp = $res->fetchRow() )
        array_push($a_jobs, $tmp[0]);
$smarty->assign('total_name_jobs',$a_jobs);
$res->free();
$smarty->assign('time2',( (time())-2678400) );                                  // Current time - 1 month. <select> date



$dbSql->tpl->display('report.tpl');
?>
