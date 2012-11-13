{include file=header.tpl}

<div id="nav">
  <ul>
    <li>
      <a class="home" href="index.php" title="{t}Back to the dashboard{/t}">{t}Dashboard{/t}</a>
    </li>
    <li>{t}Jobs report{/t}</li>
  </ul>
</div>

<div class="main_center">

  <div class="box">
	<form action="jobs.php" method="post">
	<!-- Filter jobs form -->
	<table>
		<tr>
			<td style="border: 1px solid #c8c8c8; width: 250px; background-color:#eeeeee;"><b>{t}Filter{/t}</b></td>
			<td style="border: 1px solid #c8c8c8; text-align: right;">
				{t}Job status{/t}
				{html_options name=status values=$job_status options=$job_status selected=$job_status_filter onChange="submit();"}
			</td>
		</tr>
		<tr>
			<td style="border: 1px solid #c8c8c8; width: 250px; background-color:#eeeeee;"><b>{t}Order by{/t}</b></td>
			<td style="border: 1px solid #c8c8c8; text-align: right;">
				<input type="checkbox" name="result_order_asc" value="ASC" {$result_order_asc_checked} onChange="submit();"> Up
				{html_options name=orderby values=$result_order options=$result_order selected=$result_order_field onChange="submit();"}
			</td>
		</tr>
    </table>
    &nbsp;
    <table>
		<tr>
			<td style="border: 1px solid #c8c8c8; width:250px; background-color:#eeeeee;"><b>{$jobs_found}</b> job(s) / <b>{$total_jobs} Job(s)</b></td>
			<td style="border: 1px solid #c8c8c8; text-align: right;">
				{t}Jobs / Page{/t}
				{html_options name=jobs_per_page options=$jobs_per_page selected=$jobs_per_page_selected onChange="submit();"}
			</td>
		</tr>
	</table>
	</form>
	
    <br />
	
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
	  {/foreach}
	</table>
	<!-- </div> --> <!-- end div class=listbox -->
  </div>

</div>

{include file="footer.tpl"}
