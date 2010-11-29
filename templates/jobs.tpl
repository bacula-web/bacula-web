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
{popup_init src='./js/overlib.js'}
{include file=header.tpl}
<a href="index.php">Back to main page</a>

<div id="main_center">
  <div class="box">
	<p class="title">Last failed jobs (limited to 10)</p>
	<table>
	  <tr>
		<th>Status</th>
		<th>Job ID</th>
		<th>BackupJob</th>
		<th>Start Time</th>
		<th>End Time</th>
		<th>Elapsed time</th>
		<th>Level</th>
		<th>Pool</th>
	  </tr>
	  {foreach from=$failed_jobs item=job}
	  <tr>
		<td> <img width="20px" src="style/images/s_error.gif" alt=""/> </td>
		<td>{$job.JobId}</td>
		<td>{$job.job_name}</td>
		<td>{$job.StartTime}</td>
		<td>{$job.EndTime}</td>
		<td>{$job.elapsed}</td>
		<td align="center">{$job.Level}</td>
		<td>{$job.pool_name}</td>
	  </tr>
	  {/foreach}
	</table>
  </div>
  <div class="box">
	<p class="title">Last completed jobs</p>
	bkjlajkdjaf
  </div>
</div>

{include file="footer.tpl"}