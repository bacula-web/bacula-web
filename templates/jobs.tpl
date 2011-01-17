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
  
  <!-- Last jobs -->  
  <div class="box">
	<p class="title">Jobs report</p>
	<!-- Filter jobs -->
	<form action="jobs.php" method="post">
	<table border="0">
	  <tr>
	    <td class="info" width="200">
			{$total_jobs} jobs found
		</td>
		<td class="info" colspan="5" style="text-align: right;">
			Jobs / Page
			<select name="jobs_per_page">
			  {foreach from=$jobs_per_page item=nb_jobs}
			    <option value="{$nb_jobs}" {if $smarty.post.jobs_per_page == $nb_jobs}Selected{/if} >{$nb_jobs}
			  {/foreach}
			</select>
		</td>
		<td class="info" width="200">
			Job Status
			<select name="status">
				{foreach from=$job_status item=status_label}
				  <option value="{$status_label}" {if $smarty.post.status == $status_label}Selected{/if} >{$status_label}
				{/foreach}
			</select>
		</td>
		<td class="info" width="120">
			<input type="submit" value="Update" />
		</td>
	  </tr>
	  <tr>
		<td colspan="8">&nbsp;</td>
	  </tr>
	</table>
	</form>
	
	<table border="0">
	  <tr>
		<td class="info">Status</td>
		<td class="info">Job ID</td>
		<td class="info">BackupJob</td>
		<td class="info">Start Time</td>
		<td class="info">End Time</td>
		<td class="info">Elapsed time</td>
		<td class="info">Level</td>
		<td class="info">Bytes</td>
		<td class="info">Files</td>
		<td class="info">Pool</td>
	  </tr>
	<!-- <div class="listbox"> -->
	  {foreach from=$last_jobs item=job}
	  <tr>
		<td width="50" class="{$job.Job_classe}">
			<img width="20" src="style/images/{$job.Job_icon}" alt="" title="{$job.JobStatusLong}" />
		</td>
		<td class="{$job.Job_classe}">{$job.JobId}</td>
		<td class="{$job.Job_classe}">{$job.Job_name}</td>
		<td class="{$job.Job_classe}">{$job.StartTime}</td>
		<td class="{$job.Job_classe}">{$job.EndTime}</td>
		<td class="{$job.Job_classe}">{$job.elapsed_time}</td>
		<td class="{$job.Job_classe}">{$job.Level}</td>
		<td class="{$job.Job_classe}">{$job.JobBytes}</td>
		<td class="{$job.Job_classe}">{$job.JobFiles}</td>
		<td class="{$job.Job_classe}">{$job.Pool_name}</td>
	  </tr>
	  {/foreach}
	</table>
	<!-- </div> --> <!-- end div class=listbox -->
  </div>

</div>

{include file="footer.tpl"}
