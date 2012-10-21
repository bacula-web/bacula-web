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

  <!-- Filter jobs -->
  <div class="box">
	<form action="jobs.php" method="post">
	<table style="width:100%;">
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
   
    <table width="100%">
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
	
	<table border="0" width="100%">
	  <tr>
		<td class="tbl_header">{t}Status{/t}</td> 
		<td class="tbl_header">{t}Job ID{/t}</td>
		<td class="tbl_header">{t}Name{/t}</td>
		<td class="tbl_header">{t}Type{/t}</td>
		<td class="tbl_header">{t}Start Time{/t}</td>
		<td class="tbl_header">{t}End Time{/t}</td>
		<td class="tbl_header">{t}Elapsed time{/t}</td>
		<td class="tbl_header">{t}Level{/t}</td>
		<td class="tbl_header">{t}Bytes{/t}</td>
		<td class="tbl_header">{t}Files{/t}</td>
		<td class="tbl_header">{t}Pool{/t}</td>
		<td class="tbl_header">{t}Log{/t}</td>
	  </tr>
	<!-- <div class="listbox"> -->
	  {foreach from=$last_jobs item=job}
	  <tr>
		<td width="50" class="{$job.Job_classe}">
			<img width="20" src="application/view/style/images/{$job.Job_icon}" alt="" title="{$job.jobstatuslong}" />
		</td>
		<td class="{$job.Job_classe}">{$job.jobid}</td>
		<td class="{$job.Job_classe}">
			<a href="backupjob-report.php?backupjob_name={$job.job_name}">{$job.job_name}</a>
		</td>
		<td class="{$job.Job_classe}">{$job.type}</td>
		<td class="{$job.Job_classe}">{$job.starttime}</td>
		<td class="{$job.Job_classe}">{$job.endtime}</td>
		<td class="{$job.Job_classe}">{$job.elapsed_time}</td>
		<td class="{$job.Job_classe}">{$job.level}</td>
		<td class="{$job.Job_classe}">{$job.jobbytes}</td>
		<td class="{$job.Job_classe}">{$job.jobfiles}</td>
		<td class="{$job.Job_classe}">{$job.pool_name}</td>
		<td class="{$job.Job_classe}">
		  <a href='joblogs.php?jobid={$job.jobid}' title='{t}Show job logs{/t}'> <img src='application/view/style/images/search.png' width='20' /> </a>
		</td>
	  </tr>
	  {/foreach}
	</table>
	<!-- </div> --> <!-- end div class=listbox -->
  </div>

</div>

{include file="footer.tpl"}
