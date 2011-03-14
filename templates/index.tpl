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

<div id="main_middle">
  
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
		  <td class="tbl_header">Volume</td>
		  <td class="tbl_header">Status</td>
		  <td class="tbl_header">Last written</td>
		  <td class="tbl_header">Job Id</td>
		</tr>
		{foreach from=$volume_list item=vol}
		<tr>
		  <td>{$vol.Volumename}</td>
		  <td>{$vol.VolStatus}</td>
		  <td>{$vol.Lastwritten}</td>
		  <td>{$vol.JobId}</td>
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
		<td class="label">{t}Total bytes stored{/t}</td> <td class="info">{$stored_bytes}</td>
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
				<td colspan="2" class="label"><hr></td>
			</tr>
			<tr>
				<td class="label">Job Level (Incr / Diff / Full)</td>
				<td class="info">{$incr_jobs} / {$diff_jobs} / {$full_jobs}</td>
			</tr>
			<tr>
				<td colspan="2" class="label"><hr></td>
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
 
 <form method="post" action="backupjob-report.php">
   <table width="100%" cellpadding="0" cellspacing="3" border="0">
	  <tr>
		<td colspan=2 align=center>
		  <a href="javascript:OpenWin('index.php?pop_graph1=yes','600','400')">{t}Last month, bytes transferred{/t}</a>
		</td>
	  </tr>
	  <tr>
		<td colspan=2 align=center>
		  <a href="javascript:OpenWin('index.php?pop.graph2=yes','600','400')">{t}Last month, bytes transferred (pie){/t}</a>
		</td>
	  </tr>
	 <tr> <td colspan="2">&nbsp;</td> </tr>
	 <tr>
 	   <td class="label"><b>{t}Job report:{/t}</b></td>
 	   <td class="info">
	     <input type=hidden name="default" value="1"> 				
		   <select name=backupjob_name>
 		     {*
			 {if $smarty.get.server != ""}
 				{html_options values=$smarty.get.server output=$smarty.get.server}
 			 {else}
			 *}
				{html_options values=$jobs_list output=$jobs_list}
 		     {* {/if} *}
 		   </select>
 	     <input type=submit value="{t}View{/t}">
	   </td>
     </tr>
   </table>
 </form>
</div>

{*  {include file="$last_report"}  *}

  </div> <!-- end div main_right -->

{include file="footer.tpl"}
