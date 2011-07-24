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
    <a href="index.php" title="Back to the dashboard">Dashboard</a> > Client report
  </div>

  <div id="main_center">
  
  <div class="box">
	<p class="title">Client Report</p>
    <h4>Client informations</h4>	
    <table width="300px">
	  <tr>
		<td width="100px" class="label">Client name:</td> <td>{$client_name}</td>
	  </tr>
	  <tr>
		<td class="label">Client version:</td> <td>{$client_version}</td>
	  </tr>
	  <tr>
		<td class="label">Client os:</td> <td>{$client_os}</td>
	  </tr>
	  <tr>
		<td class="label">Client arch:</td> <td>{$client_arch}</td>
	  </tr>
    </table>
	
	<h4>Last good backup job</h4>
	<table>
		<tr>
			<td class="tbl_header">Name</td>
			<td class="tbl_header">Jod Id</td>
			<td class="tbl_header">Level</td>
			<td class="tbl_header">Bytes</td>
			<td class="tbl_header">Files</td>
			<td class="tbl_header">Status</td>
		</tr>
		{foreach from=$backup_jobs item=job}
		<tr>
			<td class="{$job.Job_classe}">{$job.name}</td>
			<td class="{$job.Job_classe}">{$job.jobid}</td>
			<td class="{$job.Job_classe}">{$job.level}</td>
			<td class="{$job.Job_classe}">{$job.jobbytes}</td>
			<td class="{$job.Job_classe}">{$job.jobfiles}</td>
			<td class="{$job.Job_classe}">{$job.jobstatuslong}</td>
		</tr>
		{/foreach}
	</table>
	
  </div> <!-- end div class=box -->
  
  </div> <!-- end div id=main_center -->

{include file="footer.tpl"}
