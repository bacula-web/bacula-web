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
  
  // Running jobs
  $running_jobs = array();
  
  $query  = "SELECT Job.JobId, Job.JobStatus, Status.JobStatusLong, Job.Name, Job.StartTime, Job.Level, Pool.Name AS Pool_name ";
  $query .= "FROM Job ";
  $query .= "JOIN Status ON Job.JobStatus = Status.JobStatus ";
  $query .= "LEFT JOIN Pool ON Job.PoolId = Pool.PoolId ";
  $query .= "WHERE Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','C','R')";
  
  $jobsresult = $dbSql->db_link->query( $query );
  
  if( PEAR::isError( $jobsresult ) ) {
	  echo "SQL query = $query <br />";
	  die("Unable to get last failed jobs from catalog" . $jobsresult->getMessage() );
  }else {
	  while( $job = $jobsresult->fetchRow( DB_FETCHMODE_ASSOC ) ) {
		$elapsed = 'N/A';
		
		if( $job['JobStatus'] == 'R') {
			$elapsed = mktime() - strtotime($job['StartTime']);
			if( $elapsed > 3600 )
				$elapsed = date( "H:i:s", $elapsed );
			elseif( $elapsed > 86400 )
				$elapsed = date( "d day(s) i:s", $elapsed );
			else
				$elapsed = date( "i:s", $elapsed );
		}
		$job['elapsed_time'] = $elapsed;
		
		array_push( $running_jobs, $job);
	  }
  }
  
  $smarty->assign( 'running_jobs', $running_jobs );
  
  // Get the last jobs list
  $query 	   = "";
  $last_jobs = array();
  
  switch( $dbSql->driver ) 
  {
	case 'mysql':
		$query  = "SELECT SEC_TO_TIME( UNIX_TIMESTAMP(Job.EndTime)-UNIX_TIMESTAMP(Job.StartTime) ) AS elapsed, ";
	break;
	case 'pgsql':
		$query  = "SELECT (Job.EndTime - Job.StartTime ) AS elapsed, "; 
	break;
  }
  
  $query .= "Job.JobId, Job.Name AS Job_name, Job.StartTime, Job.EndTime, Job.Level, Pool.Name, Job.JobStatus, Pool.Name AS Pool_name, Status.JobStatusLong ";
  $query .= "FROM Job ";
  $query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
  $query .= "LEFT JOIN Status ON Job.JobStatus = Status.JobStatus ";
  $query .= "ORDER BY Job.EndTime DESC ";
  
  // Determine how many jobs to display
  if( isset($_POST['limit']) )
	$query .= "LIMIT " . $_POST['limit'];
  else
	$query .= "LIMIT 20 ";
  
  $jobsresult = $dbSql->db_link->query( $query );
  
  if( PEAR::isError( $jobsresult ) ) {
	  echo "SQL query = $query <br />";
	  die("Unable to get last failed jobs from catalog" . $jobsresult->getMessage() );
  }else {
	  while( $job = $jobsresult->fetchRow( DB_FETCHMODE_ASSOC ) ) {
		// Determine icon for job
		if( $job['JobStatus'] == 'T' )
			$job['Job_icon'] = "s_ok.gif";
		else
			$job['Job_icon'] = "s_error.gif";
		
		// Odd or even row
		if( count($last_jobs) % 2)
			$job['Job_classe'] = 'odd';
			
		array_push( $last_jobs, $job);
	  }
  }
  $smarty->assign( 'last_jobs', $last_jobs );
  
  // Process and display the template 
  $smarty->display('jobs.tpl');
?>
