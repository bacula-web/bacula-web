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
    <a href="index.php" title="{t}Back to the dashboard{/t}">Dashboard</a> > Backup Job Report
  </div>

  <div class="main_center">
  <!-- Backup job informations -->
  <p class="header">{t}Backup Job Report{/t}</p>
  <div class="box">	
	<table>
		<tr>
			<td width="150">{t}Backup Job name{/t}:</td>
			<td>{$backupjob_name}</td>
		</tr>
		<tr>
			<td>{t}Period{/t}:</td>
			<td>{$backupjob_period}</td>
		</tr>
		<tr>
			<td>{t}Transfered Bytes{/t}</td>
			<td>{$backupjob_bytes}</td>
		</tr>
		<tr>
			<td>{t}Transfered Files{/t}</td>
			<td>{$backupjob_files}</td>
		</tr>

	</table>
  </div> <!-- end div class=box -->
  </div> <!-- end div class=main_center -->
  
  <div class="main_center">
  <!-- Last jobs list -->
  <p class="header">{t}Last jobs{/t}</p>
  <div class="box">	
	<table>
		<tr>
			<td class="tbl_header" width="60">{t}Job Id{/t}</td>
			<td class="tbl_header" width="60">{t}Level{/t}</td>
			<td class="tbl_header" width="70">{t}Files{/t}</td>
			<td class="tbl_header" width="70">{t}Bytes{/t}</td>
			<td class="tbl_header" width="80">{t}Start time{/t}</td>
			<td class="tbl_header" width="80">{t}End time{/t}</td>
			<td class="tbl_header" width="80">{t}Elapsed time{/t}</td>
		</tr>
		{foreach from=$jobs item=job}
		<tr>
			<td class="{$job.row_class}">{$job.jobid}</td>
			<td class="{$job.row_class}">{$job.joblevel}</td>
			<td class="{$job.row_class}">{$job.jobfiles}</td>
			<td class="{$job.row_class}">{$job.jobbytes}</td>
			<td class="{$job.row_class}">{$job.starttime}</td>
			<td class="{$job.row_class}">{$job.endtime}</td>
			<td class="{$job.row_class}">{$job.elapsedtime}</td>
		</tr>
		{/foreach}
	</table>
  </div> <!-- end div class=box -->
  
  </div> <!-- end div=main_center -->
  
  <div class="main_center">
    <!-- Transfered Bytes/Files graph -->
	<p class="header">{t}Transfered Bytes / Files{/t}</p>
    <div class="box">
	  <img class="graph" src="{$graph_stored_bytes}" alt="" />
	  <img class="graph" src="{$graph_stored_files}" alt="" />
    </div> <!-- end div class=box -->
  </div> <!-- end div id=main_center -->

{include file="footer.tpl"}