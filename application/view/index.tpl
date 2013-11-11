{include file=header.tpl}

<div class="main_center">
	<div class="header left_box">{t}Overall status{/t} ({$literal_period})</div>

	<!-- Period selector -->
    <div class="toolbar_box right_box">
	  <form class="period selector" method="post" action="index.php" name="form_period_selector">
		<b>{t}Period{/t}</b>
                {html_options name=period_selector options=$custom_period_list selected=$custom_period_list_selected onchange="submit();"}

		<noscript>
		  <input title="{t}Update with selected period{/t}" type="submit" value="{t}Submit{/t}">
		</noscript>

	  </form>
	</div>
	<div class="clear_both"></div>

  <!-- General information -->
  <div class="widget">
	<p class="title">{t}Catalog statistics{/t}</p>
	<table class="table_big">
	  <tr>
	    <td>{t}Clients{/t}</td> <td class="strong">{$clients}</td>
	  </tr>
	  <tr>
		<td title="Defined Jobs and Filesets">{t}Jobs{/t} / {t}Filesets{/t}</td> <td class="strong">{$defined_jobs} / {$defined_filesets}</td>
	  </tr>
	  <tr>
		<td>{t}Total bytes{/t}</td> <td class="strong">{$stored_bytes}</td>
	  </tr>
	  <tr>
		<td>{t}Total files{/t}</td> <td class="strong">{$stored_files}</td>
	  </tr>
	  <tr>
		<td>{t}Database size{/t}</td> <td class="strong">{$database_size}</td>
	  </tr>
  	  <tr>
		<td>{t}Pool(s){/t} / {t}Volume(s){/t}</td> <td class="strong">{$pools_nb} / {$volumes_nb}</td>
	  </tr>
	  <tr>
		<td>{t}Volume(s) size{/t}</td> <td class="strong">{$volumes_size}</td>
	  </tr>
    </table>
  </div>
  
<!-- Last period job Status -->
  <div class="widget">
	<p class="title">{t}Last period jobs status{/t}</p>
		<table class="table_big">
			<tr>
				<td>{t}Running jobs{/t}</td>
				<td class="strong running">{$running_jobs}</td>
			</tr>
			<tr>
				<td>{t}Completed job(s){/t}</td> 
				<td class="strong good">{$completed_jobs}</td>
			</tr> 
			<tr>
				<td>{t}Waiting job(s){/t}</td> 
				<td class="strong warning">{$waiting_jobs}</td>
			</tr>
			<tr>
				<td>{t}Failed job(s){/t}</td> 
				<td class="strong critical">{$failed_jobs}</td>
			</tr>
			<tr>
				<td>{t}Canceled job(s){/t}</td> 
				<td class="strong">{$canceled_jobs}</td>
			</tr> 
			<tr>
				<td>Job Level (Incr / Diff / Full)</td>
				<td class="strong">{$incr_jobs} / {$diff_jobs} / {$full_jobs}</td>
			</tr>
			<tr>
				<td>{t}Transferred Bytes / Files{/t}</td> 
				<td class="strong">{$bytes_last} / {$files_last}</td>
			</tr>
		</table>
  </div> <!-- end div box -->  
  
<!-- Last period jobs status graph -->
  <div class="widget">
	<p class="title" title="{t}Last 24 hours jobs status{/t}">Jobs status</p>
	  <a href="jobs.php" title="{t}Click here to see the report{/t}">
	    <img class="graph" src="{$graph_jobs}" alt="" />
	  </a>
  </div> <!-- end div box -->

</div> <!-- end <div class="main_center"> -->

<div class="main_center">
  <div class="header left_box">{t}Pools and volumes status{/t}</div>
  <div class="clear_both"></div>
  
  <!-- Pools and Volumes Status -->
  <div class="widget">
	<p class="title">
	{t}Pools and volumes status{/t}
	</p>
    <a href="pools.php" title="{t}Click here to see the report{/t}">
	   <img class="graph" src="{$graph_pools}" alt="" />
	</a>
  </div> <!-- end div box -->
  
  <!-- Stored Bytes for last 7 days -->
  <div class="widget">
	<p class="title" title="{t}Used storage over the last 7 days{/t}">{t}Stored Bytes (GB){/t}</p>
	  <img class="graph" src="{$graph_stored_bytes}" alt="" />
  </div> <!-- end div box -->

  <!-- Last used volumes -->
  <div class="widget">
	<p class="title">{t}Last used volumes{/t}</p>
	  <table class="table_small">
		<tr>
		  <th title="Volume name">Volume</th>
		  <th title="Volume status">Status</th>
		  <th title="Volume pool">Pool</th>
		  <th title="Last written date for this volume">Last written</th>
		  <th title="Number of jobs">Jobs</th>
		</tr>
		{foreach from=$volumes_list item=vol}
		<tr class="{$vol.odd_even}">
		  <td>{$vol.volumename}</td>
		  <td>{$vol.volstatus}</td>
		  <td>{$vol.poolname}</td>
		  <td>{$vol.lastwritten}</td>
		  <td class="strong">{$vol.voljobs}</td>
		</tr>
		{/foreach}
	  </table>
  </div> <!-- end div box -->
</div>

<div class="main_center">
 <div class="header left_box">{t}Reports{/t}</div>
 <div class="clear_both"></div>

<div class="widget">
 <p class="title">{t}Client report{/t}</p>
 <!-- Client report -->
 <form method="post" action="client-report.php">
	<table>
		<tr>
			<td class="label">{t}Client{/t}</td>
			<td class="right_align">
				{html_options name=client_id options=$clients_list}
			</td>
		</tr>
		<!--
		<tr>
			<td class="label left_align">Report type</td>
			<td class="info">
				<select>
					<option>{t}Transferred Bytes{/t}
					<option>{t}Transferred Files {/t}
				</select>
			</td>
		</tr>
		<tr>
			<td class="label left_align">Graph type</td>
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
			<td class="right_align">
				<select name="period">
					<option value="7">{t}Last week{/t}
					<option value="14">{t}Last 2 week{/t}
					<option value="28">{t}Last month{/t}
				</select>
			</td>
		</tr>		
		<tr>
			<td colspan="2" class="form_submit right_align">
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
		<td class="right_align">
	     <input type=hidden name="default" value="1"> 				
		   <select name=backupjob_name>
		     {html_options values=$jobs_list output=$jobs_list}
 		   </select>
	   </td>
	 </tr>
	 <tr>
	   <td colspan="2" class="form_submit right_align"> 
		 <input type=submit value="{t}View report{/t}"> 
	   </td>
	 </tr>
   </table>
 </form>
</div> <!-- end div class=box -->

</div> <!-- end div main_center -->

{include file="footer.tpl"}
