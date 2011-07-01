<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>bacula-web</title>
<link rel="stylesheet" type="text/css" href="style/default.css">
</head>
<body>
{include file=header.tpl}

<div id="main_left">

  <!-- Pools and Volumes Status -->
  <div class="box">
	<p class="title">
	Pools and volumes status	
	</p>
	<img src="{$graph_pools}" alt="" />
	<div class="footer">
		<a href="pools.php" title="Show pools and volumes report">{t}View report{/t}</a>
	</div>
  </div> <!-- end div box -->
  
  <!-- Stored Bytes for last 7 days -->
  <div class="box">
	<p class="title" title="Stored bytes for last 7 days (GB)">Stored bytes</p>
	  <img src="{$graph_stored_bytes}" alt="" />
  </div> <!-- end div box -->

</div>

<div id="main_middle">
  <!-- Last 24 hours jobs status -->
  <div class="box">
	<p class="title" title="Last 24 hours jobs status">Jobs status</p>
	  <img src="{$graph_jobs}" alt="" />
	<div class="footer">
		<a href="jobs.php" title="Show last 24 hours jobs status">View report</a>
	</div>
  </div> <!-- end div box -->
  
  <div class="box">
	<p class="title">Last used volumes</p>
	  <table>
		<tr>
		  <td class="tbl_header">Volume</td>
		  <td class="tbl_header">Status</td>
		  <td class="tbl_header">Last written</td>
		  <td class="tbl_header">Job Id</td>
		</tr>
		{foreach from=$volume_list item=vol}
		<tr>
		  <td>{$vol.volumename}</td>
		  <td>{$vol.volstatus}</td>
		  <td>{$vol.lastwritten}</td>
		  <td>{$vol.jobid}</td>
		</tr>
		{/foreach}
	  </table>
  </div> <!-- end div box -->
  
</div> <!-- end div main_middle -->

<div id="main_right">
  <!-- General information -->
  <div class="box">
	<p class="title">Overall status</p>
	<table>
	  <tr>
	    <td class="label">{t}Clients{/t}</td> <td class="info">{$clientes_totales}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Total bytes{/t}</td> <td class="info">{$stored_bytes}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Total files{/t}</td> <td class="info">{$stored_files} file(s)</td>
	  </tr>
	  <tr>
		<td class="label">{t}Database size{/t}</td> <td class="info">{$database_size}</td>
	  </tr>
	</table>
  </div>

  <!-- Last 24 hours job Status -->
  <div class="box">
	<p class="title">Last 24 hours status</p>
		<table>
			<tr>
				<td class="label">Failed jobs</td> 
				<td class="info">{$failed_jobs}</td>
			</tr>
			<tr>
				<td class="label">Completed jobs</td> 
				<td class="info">{$completed_jobs}</td>
			</tr> 
			<tr>
				<td class="label">Waiting jobs</td> 
				<td class="info">{$waiting_jobs}</td>
			</tr> 
			<tr>
				<td class="label">Job Level (Incr / Diff / Full)</td>
				<td class="info">{$incr_jobs} / {$diff_jobs} / {$full_jobs}</td>
			</tr>
			<tr>
				<td class="label">Transferred Bytes</td> 
				<td class="info">{$bytes_last}</td>
			</tr>
			<tr>
				<td class="label">Transferred Files</td> 
				<td class="info">{$files_last}</td>
			</tr>
		</table>
  </div> <!-- end div box -->   
  
<div class="box">
	<p class="title">Reports</p>
 
 <!-- Custom report -->
 <form method="post" action="client-report.php">
	<table width="100%">
		<tr>
			<td class="tbl_header" colspan="2"><b>Custom report</b></td>
		</tr>
		<tr>
			<td class="label">Report type</td>
			<td class="info">
				<select>
					<option>{t}Transfered Bytes{/t}
					<option>{t}Transfered Files {/t}
				</select>
			</td>
		</tr>
		<tr>
			<td class="label">Client</td>
			<td class="info">
				Client list here + All
			</td>
		</tr>
		<tr>
			<td class="label">Graph type</td>
			<td class="info">
				<select>
					<option>Bars
					<option>Line
				</select>
			</td>
		</tr>
		<tr>
			<td class="label">Interval</td>
			<td class="info">
				<select>
					<option>Last day
					<option>Last week
					<option>Last month
				</select>
			</td>
		</tr>		
		<tr>
			<td colspan="2" class="info">
				<input type="submit" value="View report"/>
			</td>
		</tr>
	</table>
 </form>
 
 <!-- Backup job report form -->
 <form method="post" action="backupjob-report.php">
   <table border="0">
     <tr> <td colspan="2" class="label">&nbsp;</td> </tr>
	 <tr>
 	   <td colspan="2" class="tbl_header"><b>{t}Backup Job report{/t}</b></td>
	 </tr>
	 <tr>
		<td class="label">Select a backup job</td>
		<td class="info">
	     <input type=hidden name="default" value="1"> 				
		   <select name=backupjob_name>
		     {html_options values=$jobs_list output=$jobs_list}
 		   </select>
	   </td>
	 </tr>
	 <tr>
	 <td colspan="2" class="info"> <input type=submit value="{t}View report{/t}"> </td>
	 </tr>
   </table>
 </form>
</div> <!-- end div class=box -->

</div> <!-- end div main_right -->

{include file="footer.tpl"}
