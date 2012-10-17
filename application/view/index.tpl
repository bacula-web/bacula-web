{include file=header.tpl}

<div class="main_center">
	<div class="header">{t}Overall status{/t}</div>
  
  <!-- General information -->
  <div class="widget">
	<p class="title">{t}Catalog statistics{/t}</p>
	<table style="border-collapse: separate;">
	  <tr>
	    <td class="label">{t}Clients{/t}</td> <td class="info">{$clients}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Total bytes{/t}</td> <td class="info">{$stored_bytes}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Total files{/t}</td> <td class="info">{$stored_files}</td>
	  </tr>
	  <tr>
		<td class="label">{t}Database size{/t}</td> <td class="info">{$database_size}</td>
	  </tr>
  	  <tr>
		<td class="label">{t}Pools{/t}</td> <td class="info">{$pools_nb}</td>
	  </tr>
  	  <tr>
		<td class="label">{t}Volumes{/t}</td> <td class="info">{$volumes_nb}</td>
	  </tr>
    </table>
  </div>
  
<!-- Last 24 hours job Status -->
  <div class="widget">
	<p class="title">{t}Last 24 hours status{/t}</p>
		<table style="border-collapse: separate;">
			<tr>
				<td class="label">{t}Completed jobs{/t}</td> 
				<td class="info completed_jobs">{$completed_jobs}</td>
			</tr> 
			<tr>
				<td class="label">{t}Waiting jobs{/t}</td> 
				<td class="info waiting_jobs">{$waiting_jobs}</td>
			</tr>
			<tr>
				<td class="label">{t}Failed jobs{/t}</td> 
				<td class="info failed_jobs">{$failed_jobs}</td>
			</tr>
			<tr>
				<td class="label">{t}Canceled jobs{/t}</td> 
				<td class="info failed_jobs">{$canceled_jobs}</td>
			</tr> 
			<tr>
				<td class="label">Job Level (Incr / Diff / Full)</td>
				<td class="info">{$incr_jobs} / {$diff_jobs} / {$full_jobs}</td>
			</tr>
			<tr>
				<td class="label">{t}Transferred Bytes{/t}</td> 
				<td class="info">{$bytes_last}</td>
			</tr>
			<tr>
				<td class="label">{t}Transferred Files{/t}</td> 
				<td class="info">{$files_last}</td>
			</tr>
		</table>
  </div> <!-- end div box -->  
  
<!-- Last 24 hours jobs status -->
  <div class="widget">
	<p class="title" title="{t}Last 24 hours jobs status{/t}">Jobs status</p>
	  <a href="jobs.php" title="{t}Show last 24 hours jobs status{/t}">
	    <img class="graph" src="{$graph_jobs}" alt="" />
	  </a>
	  <p class="box_footer">Click on graph to see the report</p>
  </div> <!-- end div box -->
  
</div>

<div class="main_center">
  <p class="header">{t}Pools and volumes status{/t}</p>

  <!-- Pools and Volumes Status -->
  <div class="widget">
	<p class="title">
	{t}Pools and volumes status{/t}
	</p>
    <a href="pools.php" title="{t}Show pools and volumes report{/t}">
	   <img class="graph" src="{$graph_pools}" alt="" />
	</a>
	<p class="box_footer">Click on graph to see the report</p>
  </div> <!-- end div box -->
  
  <!-- Stored Bytes for last 7 days -->
  <div class="widget">
	<p class="title" title="{t}Stored bytes for last 7 days (GB){/t}">{t}Stored bytes{/t}</p>
	  <img class="graph" src="{$graph_stored_bytes}" alt="" />
  </div> <!-- end div box -->

  <!-- Last used volumes -->
  <div class="widget">
	<p class="title">{t}Last used volumes{/t}</p>
	  <table>
		<tr>
		  <td class="tbl_header" title="Volume name">Volume</td>
		  <td class="tbl_header" title="Volume status">Status</td>
		  <td class="tbl_header" title="Volume pool">Pool</td>
		  <td class="tbl_header" title="Last written date for this volume">Last written</td>
		  <td class="tbl_header" title="Number of jobs">Jobs</td>
		</tr>
		{foreach from=$volumes_list item=vol}
		<tr>
		  <td class="{$vol.odd_even}">{$vol.volumename}</td>
		  <td class="{$vol.odd_even}">{$vol.volstatus}</td>
		  <td class="{$vol.odd_even}">{$vol.poolname}</td>
		  <td class="{$vol.odd_even}">{$vol.lastwritten}</td>
		  <td class="{$vol.odd_even}"><b>{$vol.jobs_count}</b></td>
		</tr>
		{/foreach}
	  </table>
  </div> <!-- end div box -->
</div>

<div class="main_center">
 <p class="header">{t}Reports{/t}</p>

<div class="widget">
 <p class="title">{t}Client report{/t}</p>
 <!-- Client report -->
 <form method="post" action="client-report.php">
	<table>
		<tr>
			<td class="label">{t}Client{/t}</td>
			<td style="text-align: right;">
				{html_options name=client_id options=$clients_list"}
			</td>
		</tr>
		<!--
		<tr>
			<td class="label">Report type</td>
			<td class="info">
				<select>
					<option>{t}Transferred Bytes{/t}
					<option>{t}Transferred Files {/t}
				</select>
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
		-->
		<tr>
			<td class="label">{t}Interval{/t}</td>
			<td style="text-align: right;">
				<select name="period">
					<option value="7">{t}Last week{/t}
					<option value="14">{t}Last 2 week{/t}
					<option value="28">{t}Last month{/t}
				</select>
			</td>
		</tr>		
		<tr>
			<td colspan="2" class="form_submit">
				<input type="submit" value="View report" />
			</td>
		</tr>
	</table>
 </form>
 </div> <!-- end div class=box -->
 
 <!-- Backup job report -->
 <div class="widget">
   <p class="title">{t}Backup Job report{/t}</p>
   <form method="post" action="backupjob-report.php">
   <table>
	 <tr>
		<td class="label">{t}Backup job name{/t}</td>
		<td style="text-align: right;">
	     <input type=hidden name="default" value="1"> 				
		   <select name=backupjob_name>
		     {html_options values=$jobs_list output=$jobs_list}
 		   </select>
	   </td>
	 </tr>
	 <tr>
	   <td colspan="2" class="form_submit"> 
		 <input type=submit value="{t}View report{/t}"> 
	   </td>
	 </tr>
   </table>
 </form>
</div> <!-- end div class=box -->

</div> <!-- end div main_center -->

{include file="footer.tpl"}
