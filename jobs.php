<?php
  session_start();
  require_once ("paths.php");
  require_once ($smarty_path."Smarty.class.php");
  require_once ("bweb.inc.php");
  require_once ("config.inc.php");  

  $smarty = new Smarty();     
  $dbSql = new Bweb();

  require("lang.php");

  // Smarty configuration
  $smarty->compile_check = true;
  $smarty->debugging = false;
  $smarty->force_compile = true;

  $smarty->template_dir = "./templates";
  $smarty->compile_dir = "./templates_c";
  $smarty->config_dir     = "./configs";
  
  // Get the last 10 failed jobs
  $query 	   = "";
  $failed_jobs = array();
  
  switch( $dbSql->driver ) 
  {
	case 'mysql':
		$query  = "SELECT SEC_TO_TIME( UNIX_TIMESTAMP(Job.EndTime)-UNIX_TIMESTAMP(Job.StartTime) ) AS elapsed, Job.JobId, Job.Name AS job_name, Job.StartTime, Job.EndTime, Job.Level, Pool.Name AS pool_name, Job.JobStatus ";
		$query .= "FROM Job ";
		$query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
		$query .= "WHERE Job.JobStatus = 'f' ";
		//$query .= "WHERE Job.EndTime BETWEEN <= NOW() and UNIX_TIMESTAMP(EndTime) >UNIX_TIMESTAMP(NOW())-86400 ";
		$query .= "ORDER BY Job.EndTime DESC ";  
		$query .= "LIMIT 10";
 
	break;
	
	case 'pgsql':
		$query  = "SELECT (Job.EndTime - Job.StartTime ) AS elapsed, Job.Name, Job.StartTime, Job.EndTime, Job.Level, Pool.Name, Job.JobStatus ";
		$query .= "FROM Job ";
		$query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
		$query .= "WHERE Job.JobStatus = 'f' ";
		//$query .= "WHERE EndTime <= NOW() and EndTime > NOW() - 86400 * interval '1 second' AND ";
		$query .= "ORDER BY Job.EndTime DESC";
		$query .= "LIMIT 10";
	break;
  }
  $jobsresult = $dbSql->db_link->query( $query );
  
  if( PEAR::isError( $jobsresult ) ) {
	  echo "SQL query = $query <br />";
	  die("Unable to get last failed jobs from catalog" . $jobsresult->getMessage() );
  }else {
	  while( $job = $jobsresult->fetchRow( DB_FETCHMODE_ASSOC ) ) {
		array_push( $failed_jobs, $job);
	  }
  }
  $smarty->assign( 'failed_jobs', $failed_jobs );
  
  // Get the last completed jobs (last 24 hours)
  $query 	   = "";
  $completed_jobs = array();
  
  // Interval calculation
  $end_date   = mktime();
  $start_date = $end_date - LAST_DAY;
			
  $start_date = date( "Y-m-d H:i:s", $start_date );
  $end_date   = date( "Y-m-d H:i:s", $end_date );
  
  switch( $dbSql->driver ) 
  {
	case 'mysql':
		$query  = "SELECT SEC_TO_TIME( UNIX_TIMESTAMP(Job.EndTime)-UNIX_TIMESTAMP(Job.StartTime) ) AS elapsed, Job.JobId, Job.Name AS job_name, Job.StartTime, Job.EndTime, Job.Level, Pool.Name AS pool_name, Job.JobStatus ";
		$query .= "FROM Job ";
		$query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
		$query .= "WHERE Job.JobStatus = 'T' AND ";
		$query .= "Job.EndTime BETWEEN '$start_date' AND '$end_date' ";
		$query .= "ORDER BY Job.EndTime DESC ";  
		
	break;
	
	case 'pgsql':
		$query  = "SELECT (Job.EndTime - Job.StartTime ) AS elapsed, Job.Name, Job.StartTime, Job.EndTime, Job.Level, Pool.Name, Job.JobStatus ";
		$query .= "FROM Job ";
		$query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
		$query .= "WHERE EndTime BETWEEN '$start_date' and '$end_date' ";
		$query .= "ORDER BY Job.EndTime DESC";
	break;
  }

  $jobsresult = $dbSql->db_link->query( $query );
  
  if( PEAR::isError( $jobsresult ) ) {
	  echo "SQL query = $query <br />";
	  die("Unable to get last failed jobs from catalog" . $jobsresult->getMessage() );
  }else {
	  while( $job = $jobsresult->fetchRow( DB_FETCHMODE_ASSOC ) ) {
		array_push( $completed_jobs, $job);
	  }
  }
  $smarty->assign( 'completed_jobs', $completed_jobs );
  
  $smarty->display('jobs.tpl');
?>
