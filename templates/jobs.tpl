<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
</head>
<body>
{include file=header.tpl}

  <div id="nav">
    <a href="index.php" title="Back to the dashboard">Dashboard</a> > Jobs list
  </div>

  <div id="main_center">
  
  <!-- Last jobs -->  
  <div class="box">
	<p class="title">Jobs report</p>
	<!-- Filter jobs -->
	<h4>Filter jobs</h4>
	<form action="jobs.php" method="post">
	<table border="0">
	  <tr>
	    <td class="info" width="200">
			{$total_jobs} jobs found
		</td>
		<td class="info" colspan="5" width="160">
			Jobs / Page
			{html_options name=jobs_per_page options=$jobs_per_page selected=$jobs_per_page_selected onChange="submit();"}
		</td>
		<td class="info" width="160">
			Job status
			{html_options name=status values=$job_status options=$job_status selected=$job_status_filter onChange="submit();"}
		</td>
	  </tr>
	</table>
	</form>
	
	<h4>Jobs result</h4>
	<table border="0">
	  <tr>
		<td class="tbl_header">Status</td>
		<td class="tbl_header">Job ID</td>
		<td class="tbl_header">Name</td>
		<td class="tbl_header">Type</td>
		<td class="tbl_header">Start Time</td>
		<td class="tbl_header">End Time</td>
		<td class="tbl_header">Elapsed time</td>
		<td class="tbl_header">Level</td>
		<td class="tbl_header">Bytes</td>
		<td class="tbl_header">Files</td>
		<td class="tbl_header">Pool</td>
	  </tr>
	<!-- <div class="listbox"> -->
	  {foreach from=$last_jobs item=job}
	  <tr>
		<td width="50" class="{$job.Job_classe}">
			<img width="20" src="style/images/{$job.Job_icon}" alt="" title="{$job.jobstatuslong}" />
		</td>
		<td class="{$job.Job_classe}">{$job.jobid}</td>
		<td class="{$job.Job_classe}">
			<a href="backupjob-report.php?backupjob_name={$job.job_name}">{$job.job_name}</a>
		</td>
		<td class="{$job.Job_classe}">{$job.type}</td>
		<td class="{$job.Job_classe}">{$job.starttime}</td>
		<td class="{$job.Job_classe}">{$job.endtime}</td>
		<td class="{$job.Job_classe}">{$job.elapsed_time}</td>
		<td class="{$job.Job_classe}">{$job.level}</td>
		<td class="{$job.Job_classe}">{$job.jobbytes}</td>
		<td class="{$job.Job_classe}">{$job.jobfiles}</td>
		<td class="{$job.Job_classe}">{$job.pool_name}</td>
	  </tr>
	  {/foreach}
	</table>
	<!-- </div> --> <!-- end div class=listbox -->
  </div>

</div>

{include file="footer.tpl"}
