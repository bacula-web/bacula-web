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
  
  // Jobs list
  $query 	   = "";
  $last_jobs = array();
  
  // Job Status list
  $job_status = array( 'Any', 'Waiting', 'Running', 'Completed', 'Failed', 'Canceled' );
  $smarty->assign( 'job_status', $job_status );
  
  // Global variables
  $job_level = array( 'D' => 'Diff', 'I' => 'Incr', 'F' => 'Full' );
  
  $query .= "SELECT Job.JobId, Job.Name AS Job_name, Job.StartTime, Job.EndTime, Job.Level, Job.JobBytes, Pool.Name, Job.JobStatus, Pool.Name AS Pool_name, Status.JobStatusLong ";
  $query .= "FROM Job ";
  $query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
  $query .= "LEFT JOIN Status ON Job.JobStatus = Status.JobStatus ";
  
  // Filter by status
  if( isset( $_POST['status'] ) ) {
	switch( strtolower( $_POST['status'] ) )
	{
		case 'running':
			$query .= "WHERE Job.JobStatus = 'R' ";
		break;
		case 'waiting':
			$query .= "WHERE Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','C','R') ";
		break;
		case 'completed':
			$query .= "WHERE Job.JobStatus = 'T' ";
		break;
		case 'failed':
			$query .= "WHERE Job.JobStatus = 'f' ";
		break;
		case 'canceled':
			$query .= "WHERE Job.JobStatus = 'A' ";
		break;
	}
  }
  
  // order by
  $query .= "ORDER BY Job.JobId DESC ";
  
  // Determine how many jobs to display
  if( isset($_POST['limit']) )
	$query .= "LIMIT " . $_POST['limit'];
  else
	$query .= "LIMIT 20 ";
  
  //echo $query . '<br />';
  
  $jobsresult = $dbSql->db_link->query( $query );
  
  if( PEAR::isError( $jobsresult ) ) {
	  echo "SQL query = $query <br />";
	  die("Unable to get last failed jobs from catalog" . $jobsresult->getMessage() );
  }else {
	  while( $job = $jobsresult->fetchRow( DB_FETCHMODE_ASSOC ) ) {
		
		// Determine icon for job status
		switch( $job['JobStatus'] ) {
			case 'R':
				$job['Job_icon'] = "running.png";
			break;
			case 'T':
				$job['Job_icon'] = "s_ok.png";
			break;
			case 'A':
			case 'f':
			case 'E':
				$job['Job_icon'] = "s_error.gif";
			break;
			case 'F':
			case 'S':
			case 'M':
			case 'm':
			case 's':
			case 'j':
			case 'c':
			case 'd':
			case 't':
			case 'C':
				$job['Job_icon'] = "waiting.png";
			break;
		} // end switch
		
		// Odd or even row
		if( count($last_jobs) % 2)
			$job['Job_classe'] = 'odd';
		
		// Elapsed time for the job
		if( $job['StartTime'] == '0000-00-00 00:00:00' )
			$job['elapsed_time'] = 'N/A';
		elseif( $job['EndTime'] == '0000-00-00 00:00:00' )
			$job['elapsed_time'] = $dbSql->Get_ElapsedTime( strtotime($job['StartTime']), mktime() );
		else
			$job['elapsed_time'] = $dbSql->Get_ElapsedTime( strtotime($job['StartTime']), strtotime($job['EndTime']) );

		// Job Level
        $job['Level'] = $job_level[ $job['Level'] ];
		
		// Job Size
		$job['JobBytes'] = $dbSql->human_file_size( $job['JobBytes'] );

		array_push( $last_jobs, $job);
	  }
  }
  $smarty->assign( 'last_jobs', $last_jobs );
  
  // Count jobs
  if( isset( $_POST['status'] ) )
	$total_jobs = $dbSql->CountJobs( ALL, $_POST['status'] );
  else
	$total_jobs = $dbSql->CountJobs( ALL );
  
  $smarty->assign( 'total_jobs', $total_jobs );
  
  // Process and display the template 
  $smarty->display('jobs.tpl');
?>
