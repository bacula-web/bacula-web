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
  include "classes.inc.php";

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
		$query  = "select (Job.EndTime - Job.StartTime ) AS elapsed, Job.Name, Job.StartTime, Job.EndTime, Job.Level, Pool.Name, Job.JobStatus ";
		$query .= "FROM Job ";
		$query .= "LEFT JOIN Pool ON Job.PoolId=Pool.PoolId ";
		$query .= "WHERE EndTime <= NOW() and EndTime > NOW() - 86400 * interval '1 second' ";
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
  
  $smarty->display('jobs.tpl');
?>
