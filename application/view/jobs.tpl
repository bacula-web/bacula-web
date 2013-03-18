{include file=header.tpl}

<div class="main_center">
  <div class="box right_box">
  
  <form action="jobs.php" method="post">
	<!-- Filter jobs form -->
	<table>
		<tr>
			<th colspan="2"> <!-- style="border: 1px solid #c8c8c8; width: 150px; background-color:#eeeeee;"-->
			  <b>{t}Filter{/t}</b>
			</th>
		</tr>
		<tr>
			<td style="border: 1px solid #c8c8c8; text-align: right;"><b>{t}Job status{/t}</b></td>
			<td style="text-align: left;">
				{html_options name=status values=$job_status options=$job_status selected=$job_status_filter onChange="submit();"}
			</td>
		</tr>
		<tr>
			<td style="border: 1px solid #c8c8c8; text-align:right;"><b>{t}Order by{/t}</b></td>
			<td style="border: 1px solid #c8c8c8; text-align: left;">
				{html_options name=orderby values=$result_order options=$result_order selected=$result_order_field onChange="submit();"}
				<input type="checkbox" name="result_order_asc" value="ASC" {$result_order_asc_checked} onChange="submit();"> Up
			</td>
		</tr>
		<tr>
			<td style="border: 1px solid #c8c8c8; text-align: right;">
				<b>{t}Jobs per Page{/t}</b>
			</td>
			<td class="left_align">
				{html_options name=jobs_per_page options=$jobs_per_page selected=$jobs_per_page_selected onChange="submit();"}
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2" style="border: 1px solid #c8c8c8; background-color:#eeeeee;">
			  Found <b>{$jobs_found}</b> of <b>{$total_jobs} Job(s)</b>
			</td>
		</tr>
	</table>
	</form>
  </div>
  
  <div class="box left_box">
	
	<table class="grid">
	  <tr>
		<th>{t}Status{/t}</th> 
		<th>{t}Job ID{/t}</th>
		<th>{t}Name{/t}</th>
		<th>{t}Type{/t}</th>
		<th>{t}Start Time{/t}</th>
		<th>{t}End Time{/t}</th>
		<th>{t}Elapsed time{/t}</th>
		<th>{t}Level{/t}</th>
		<th>{t}Bytes{/t}</th>
		<th>{t}Files{/t}</th>
		<th>{t}Pool{/t}</th>
		<th>{t}Log{/t}</th>
	  </tr>
	<!-- <div class="listbox"> -->
	  {foreach from=$last_jobs item=job}
	  <tr class="{$job.odd_even}">
		<td>
			<img width="20" src="application/view/style/images/{$job.Job_icon}" alt="" title="{$job.jobstatuslong}" />
		</td>
		<td>{$job.jobid}</td>
		<td>
			<a href="backupjob-report.php?backupjob_name={$job.job_name}">{$job.job_name}</a>
		</td>
		<td>{$job.type}</td>
		<td>{$job.starttime}</td>
		<td>{$job.endtime}</td>
		<td>{$job.elapsed_time}</td>
		<td>{$job.level}</td>
		<td>{$job.jobbytes}</td>
		<td>{$job.jobfiles}</td>
		<td>{$job.pool_name}</td>
		<td>
		  <a href='joblogs.php?jobid={$job.jobid}' title='{t}Show job logs{/t}'> <img src='application/view/style/images/search.png' width='20' /> </a>
		</td>
	  </tr>
	  {foreachelse}
	  <tr>
		<td colspan="12">{t}No job(s) to display{/t}</td>
      </tr>
	  {/foreach}
	</table>
	<!-- </div> --> <!-- end div class=listbox -->
  </div>

</div>

{include file="footer.tpl"}
