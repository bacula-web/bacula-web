<?php
/* 
+-------------------------------------------------------------------------+
| Copyright 2010-2011, Davide Franco			                          |
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
  include_once( 'config.inc.php' );

  $dbSql = new Bweb();
  // Jobs list
  $query 	   = "";
  $last_jobs = array();
  
  // Job Status list
  $job_status = array( 'Any', 'Waiting', 'Running', 'Completed', 'Failed', 'Canceled' );
  $dbSql->tpl->assign( 'job_status', $job_status );
  
  // Jobs per page
  $jobs_per_page = array( 25,50,75,100,150 );
  $dbSql->tpl->assign( 'jobs_per_page', $jobs_per_page );

  // Global variables
  $job_level = array( 'D' => 'Diff', 'I' => 'Incr', 'F' => 'Full' );
  
  $query .= "SELECT Job.JobId, Job.Name AS Job_name, Job.StartTime, Job.EndTime, Job.Level, Job.JobBytes, Job.JobFiles, Pool.Name, Job.JobStatus, Pool.Name AS Pool_name, Status.JobStatusLong ";
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
			$query .= "WHERE Job.JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
		break;
		case 'completed':
			$query .= "WHERE Job.JobStatus IN ('T', 'E') ";
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
  if( isset($_POST['jobs_per_page']) )
	$query .= "LIMIT " . $_POST['jobs_per_page'];
  else
	$query .= "LIMIT 25 ";
  
  //echo $query . '<br />';
  
  $jobsresult = $dbSql->db_link->query( $query );
  
  if( PEAR::isError( $jobsresult ) ) {
	  echo "SQL query = $query <br />";
	  die("Unable to get last failed jobs from catalog" . $jobsresult->getMessage() );
  }else {
	  while( $job = $jobsresult->fetchRow( DB_FETCHMODE_ASSOC ) ) {
		
		// Determine icon for job status
		switch( $job['JobStatus'] ) {
			case J_RUNNING:
				$job['Job_icon'] = "running.png";
			break;
			case J_COMPLETED:
				$job['Job_icon'] = "ok.png";
			break;
			case J_CANCELED:
				$job['Job_icon'] = "canceled.png";
			break;
			case J_COMPLETED_ERROR:
				$job['Job_icon'] = "warning.png";
			break;
			case J_FATAL:
				$job['Job_icon'] = "error.png";
			break;
			case J_WAITING_CLIENT:
			case J_WAITING_SD:
			case J_WAITING_MOUNT_MEDIA:
			case J_WAITING_NEW_MEDIA:
			case J_WAITING_STORAGE_RES:
			case J_WAITING_JOB_RES:
			case J_WAITING_CLIENT_RES:
			case J_WAITING_MAX_JOBS:
			case J_WAITING_START_TIME:
			case J_NOT_RUNNING:
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
		$job['JobBytes'] = Utils::Get_Human_Size( $job['JobBytes'] );

		array_push( $last_jobs, $job);
	  }
  }
  $dbSql->tpl->assign( 'last_jobs', $last_jobs );
  
  // Count jobs
  if( isset( $_POST['status'] ) )
	$total_jobs = $dbSql->CountJobs( ALL, $_POST['status'] );
  else
	$total_jobs = $dbSql->CountJobs( ALL );
  
  $dbSql->tpl->assign( 'total_jobs', $total_jobs );
  
  // Process and display the template 
  $dbSql->tpl->display('jobs.tpl');
?>
