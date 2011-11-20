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
    <a href="index.php" title="{t}Back to the dashboard{/t}">{t}Dashboard{/t}</a> > Client report
  </div>

  <div id="main_center">
  
  <div class="box">
	<p class="title">{t}Client Report{/t}</p>
    <h4>{t}Client informations{/t}</h4>	
    <table width="300px">
	  <tr>
		<td width="100px" class="label">{t}Client name{/t}:</td> <td>{$client_name}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Client version{/t}:</td> <td>{$client_version}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Client os{/t}:</td> <td>{$client_os}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Client arch{/t}:</td> <td>{$client_arch}</td>
	  </tr>
    </table>
	
	<h4>Last good backup job</h4>
	<table>
		<tr>
			<td class="tbl_header">{t}Name{/t}</td>
			<td class="tbl_header">{t}Jod Id{/t}</td>
			<td class="tbl_header">{t}Level{/t}</td>
			<td class="tbl_header">{t}End time{/t}</td>
			<td class="tbl_header">{t}Bytes{/t}</td>
			<td class="tbl_header">{t}Files{/t}</td>
			<td class="tbl_header">{t}Status{/t}</td>
		</tr>
		{foreach from=$backup_jobs item=job}
		<tr>
			<td class="{$job.Job_classe}">{$job.name}</td>
			<td class="{$job.Job_classe}">{$job.jobid}</td>
			<td class="{$job.Job_classe}">{$job.level}</td>
			<td class="{$job.Job_classe}">{$job.endtime}</td>
			<td class="{$job.Job_classe}">{$job.jobbytes}</td>
			<td class="{$job.Job_classe}">{$job.jobfiles}</td>
			<td class="{$job.Job_classe}">{$job.jobstatuslong}</td>
		</tr>
		{/foreach}
	</table>
	
	<h4>Statistics - last {$period} days(s)</h4>
	
	<table>
		<tr>
			<td> <img class="graph" src="{$graph_stored_bytes}" alt="" /> </td>
			<td> <img class="graph" src="{$graph_stored_files}" alt="" /> </td>
		</tr>
	</table>
	
  </div> <!-- end div class=box -->
  
  </div> <!-- end div id=main_center -->

{include file="footer.tpl"}
