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

<div id="main_left">
  <!-- General information -->
  <div class="box">
	<p class="title">Overall status</p>
	<table>
	  <tr>
	    <td class="label">{t}Clients{/t}</td> <td class="info">{$clientes_totales}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Total bytes stored{/t}</td> <td class="info">{$stored_bytes}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Total files{/t}</td> <td class="info">{$files_totales} file(s)</td>
	  </tr>
	  <tr>
		<td class="label">{t}Database size{/t}</td> <td class="info">{$database_size}</td>
	  </tr>
	</table>
  </div>

  <!-- Pools and Volumes Status -->
  <div class="box">
	<p class="title">
	Pools and volumes status
	<a href="pools.php" title="Show pools and volumes report">View report</a>
	</p>
	  <img src="{$graph_pools}" alt="" />
  </div> <!-- end div box -->
  
  <div class="box">
	<p class="title">Stored Bytes (GB / Last 7 days)</p>
	  <img src="{$graph_stored_bytes}" alt="" />
  </div> <!-- end div box -->

</div>

<div id="main_right">
  <!-- Last job Status -->
  <div class="box">
	<p class="title">Last 24 hours status
		<a href="jobs.php" title="Show last 24 hours jobs status">View report</a>
	</p>
		<table>
			<tr>
				<td class="label">Failed jobs</td> 
				<td class="info">{$failed_jobs}</td>
				<!-- <td class="info"> <a href="jobs.php" title="View last failed jobs">View</a> </td> -->
			</tr>
			<tr>
				<td class="label">Completed jobs</td> 
				<td class="info">{$completed_jobs}</td>
				<!-- <td class="info"> <a href="jobs.php" title="View last completed jobs">View</a> </td> -->
			</tr> 
			<tr>
				<td class="label">Waiting jobs</td> 
				<td class="info">{$waiting_jobs}</td>
				<!-- <td class="info"> <a href="jobs.php" title="View last completed jobs">View</a> </td> -->
			</tr> 
			<tr>
				<td class="label">Transferred Bytes</td> 
				<td class="info">{$bytes_totales}</td>
				<!-- <td class="info"> <a href="#" title="View report">View</a> </td> -->
			</tr> 
		</table>

  </div> <!-- end div box --> 
  
  <div class="box">
	<p class="title">
	Job Status Report
	<a href="jobs.php" title="Show last 24 hours jobs status">View report</a>
	</p>
	  <img src="{$graph_jobs}" alt="" />
  </div> <!-- end div box -->
  
  <div class="box">
	<p class="title">Last used volumes</p>
	  <table>
		<tr>
		  <td class="info">Volume</td>
		  <td class="info">Status</td>
		  <td class="info">Job Id</td>
		</tr>
		{foreach from=$volume_list item=vol}
		<tr>
		  <td>{$vol.Volumename}</td>
		  <td>{$vol.VolStatus}</td>
		  <td>{$vol.JobId}</td>
		</tr>
		{/foreach}
	  </table>
  </div> <!-- end div box -->
  

  {include file="$last_report"} 	

</div> <!-- end div main_right -->

{include file="footer.tpl"}
