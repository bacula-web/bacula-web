<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
{literal}
<script type="text/javascript">
	function OpenWin(URL,wid,hei) {
		window.open(URL,"window1","width="+wid+",height="+hei+",scrollbars=yes,menubar=no,location=no,resizable=no")
	}
</script>
{/literal}

</head>
<body>
{popup_init src='./external_packages/js/overlib.js'}
{include file=header.tpl}

<div id="nav">
  <a href="index.php" title="Back to the dashboard">Dashboard</a> > Jobs list
</div>

<div id="main_center">
  <div class="box">
	<p class="title">Running jobs</p>
	<table class="list">
		<tr>
			<td class="info">Status</td>
			<td class="info">Job ID</td>
			<td class="info">BackupJob</td>
			<td class="info">Start Time</td>
			<td class="info">Elapsed time</td>
			<td class="info">Level</td>
			<td class="info">Pool</td>
		</tr>
		{foreach from=$running_jobs item=job}
		<tr>
			<td>{$job.JobStatusLong}</td>
			<td>{$job.JobId}</td>
			<td>{$job.Name}</td>
			<td>{$job.StartTime}</td>
			<td>{$job.elapsed_time}</td>
			<td>{$job.Level}</td>
			<td>{$job.Pool_name}</td>
		</tr>
		{/foreach}
	</table>
  </div> <!-- end div box -->

  <!-- Last jobs -->  
  <div class="box">
	<p class="title">Last jobs</p>
	<table class="list" border="0">
	  <tr>
		<td width="50" class="info">Status</td>
		<td width="50" class="info">Job ID</td>
		<td width="70" class="info">BackupJob</td>
		<td width="80" class="info">Start Time</td>
		<td width="80" class="info">End Time</td>
		<td width="70" class="info">Elapsed time</td>
		<td width="50" class="info">Level</td>
		<td width="80" class="info">Pool</td>
	  </tr>
	</table>
	<div class="listbox">
	<table class="list" border="0">
	  {foreach from=$last_jobs item=job}
	  <tr>
		<td width="50" class="{$job.Job_classe}">
			<img width="20" src="style/images/{$job.Job_icon}" alt="" title="{$job.JobStatusLong}" />
		</td>
		<td width="50" class="{$job.Job_classe}">{$job.JobId}</td>
		<td width="70" class="{$job.Job_classe}">{$job.Job_name}</td>
		<td width="80" class="{$job.Job_classe}">{$job.StartTime}</td>
		<td width="80" class="{$job.Job_classe}">{$job.EndTime}</td>
		<td width="70" class="{$job.Job_classe}">{$job.elapsed}</td>
		<td width="50" class="{$job.Job_classe}">{$job.Level}</td>
		<td width="80" class="{$job.Job_classe}">{$job.Pool_name}</td>
	  </tr>
	  {/foreach}
	</table>
	</div> <!-- end div class=listbox -->
	
	<form action="jobs.php" method="post">
	<table class="list" border="0">
	  <tr>
	    <td class="info" width="200">
			{$total_jobs} jobs found
		</td>
		<td class="info" colspan="5" style="text-align: right;">
			Jobs / Page
			<select name="limit">
				<option value="20">20
				<option value="40">40
				<option value="60">60
				<option value="80">80
				<option value="100">100
			</select>
		</td>
		<td class="info" width="200">
			Job Status
			<select name="status">
				<option value="Any">Any
				<option value="completed">Completed
				<option value="failed">Failed
				<option value="canceled">Canceled
			</select>
		</td>
		<td class="info" width="120">
			<input type="submit" value="Update" />
		</td>
	  </tr>
	</table>
	</form>
  </div>

</div>

{include file="footer.tpl"}
