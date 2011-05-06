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
    <a href="index.php" title="Back to the dashboard">Dashboard</a> > Backup Job Report
  </div>

  <div id="main_center">
  
  <div class="box">
	<p class="title">Backup Job Report</p>
	
	<table>
		<tr>
			<td width="150">Backup Job name:</td>
			<td>{$backupjob_name}</td>
		</tr>
		<tr>
			<td>Period:</td>
			<td>{$backupjob_period}</td>
		</tr>
		<tr>
			<td>Transfered Bytes</td>
			<td>{$backupjob_bytes} GB</td>
		</tr>
		<tr>
			<td>Transfered Files</td>
			<td>{$backupjob_files}</td>
		</tr>

	</table>
  </div> <!-- end div class=box -->
  
  <!-- Last jobs list -->
  <div class="box">
	<p class="title">Last jobs</p>
	
	<table>
		<tr>
			<td class="tbl_header">Job Id</td>
			<td class="tbl_header">Level</td>
			<td class="tbl_header">Files</td>
			<td class="tbl_header">Bytes</td>
			<td class="tbl_header">End time</td>
		</tr>
		{foreach from=$jobs item=job}
		<tr>
			<td>{$job.JobId}</td>
			<td>{$job.Level}</td>
			<td>{$job.JobFiles}</td>
			<td>{$job.JobBytes}</td>
			<td>{$job.EndTime}</td>
		</tr>
		{/foreach}
	</table>
  </div> <!-- end div class=box -->
  
  <!-- Transfered Bytes/Files graph -->
  <div class="box">
	<p class="title">Transfered Bytes / Files (last 7 days)</p>
	<img src="{$graph_stored_bytes}" alt="" />
	<img src="{$graph_stored_files}" alt="" />
  </div> <!-- end div class=box -->
  
  </div> <!-- end div id=main_center -->

{include file="footer.tpl"}
