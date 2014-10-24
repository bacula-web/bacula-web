{include file=header.tpl}

<div class="container-fluid">
  <div class="row">
	  <div class="col-xs-9 col-lg-10">
		
		<table class="table table-condensed table-striped">
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
			<th>{t}Speed{/t}</th>
			<th>{t}Pool{/t}</th>
			<th>{t}Log{/t}</th>
		  </tr>
		  
		  <!-- <div class="listbox"> -->
		  {foreach from=$last_jobs item=job}
		  <tr>
			<td>
				<img class="img-responsive" width="20" src="application/view/style/images/{$job.Job_icon}" alt="" title="{$job.jobstatuslong}" />
			</td>
			<td>{$job.jobid}</td>
			<td>
				<a href="backupjob-report.php?backupjob_name={$job.job_name|escape:'url'}">{$job.job_name}</a>
			</td>
			<td>{$job.type}</td>
			<td>{$job.starttime}</td>
			<td>{$job.endtime}</td>
			<td>{$job.elapsed_time}</td>
			<td>{$job.level}</td>
			<td>{$job.jobbytes}</td>
			<td>{$job.jobfiles}</td>
			<td>{$job.speed}</td>
			<td>{$job.pool_name}</td>
			<td>
			  <a href='joblogs.php?jobid={$job.jobid}' title='{t}Show job logs{/t}'> <img src='application/view/style/images/search.png' width='20' alt='' /> </a>
			</td>
		  </tr>
		  {foreachelse}
		  <tr>
			<td colspan="12">{t}No job(s) to display{/t}</td>
		  </tr>
		  {/foreach}
		</table>
		
		<div class="alert alert-info text-center" role="alert">
		  Found <b>{$jobs_found}</b> of <b>{$total_jobs} Job(s)</b>
		</div>
	  </div>

	  <!-- Filter jobs form -->
	  <div class="col-xs-3 col-lg-2">
	  
	  <form class="form" role="form" action="jobs.php" method="post">
		
		<span class="help-block">{t}Filter{/t}</span>
		
		<div class="form-group">
		  <label>{t}Job status{/t}</label>
		  <select name="status" class="form-control">
			{foreach from=$job_status item=status_name key=status_id}
			  <option value="{$status_id}" {if $status_id eq $job_status_filter} selected {/if}>{$status_name}</option>		  
			{/foreach}
		  </select>
		</div>
		
		<div class="form-group">
		  <label>{t}Client{/t}</label>
			<select name="client_id" class="form-control">
			  {foreach from=$clients_list key=client_id item=client_name}
				<option value="{$client_id}" {if $client_id eq $client_filter}selected{/if}>{$client_name}</option>
			  {/foreach}
			</select>
		</div>
			  
		<span class="help-block">{t}Options{/t}</span>
		  
		<label>{t}Order by{/t}</label>
		  
		<select name="orderby" class="form-control">
		  {foreach from=$result_order item=label key=id}
			<option value="{$id}" {if $id eq $result_order_field}selected{/if}>{$label}</option>
		  {/foreach}
		</select>

		<div class="checkbox">
		  <label>
			<input type="checkbox" name="result_order_asc" value="ASC" {$result_order_asc_checked}> Up
		  </label>
		</div>
		
		<div class="form-group">
		  <label>{t}Jobs per Page{/t}</label>
		  <select class="form-control" name="jobs_per_page">
			{foreach from=$jobs_per_page item=label key=id}
			  <option value="{$id}" {if $id eq $jobs_per_page_selected}selected{/if}>{$label}</option>
			{/foreach}			
		  </select>
		</div>

		<button type="reset" class="btn btn-default" title="{t}Reset to default options{/t}">{t}Reset{/t}</button>
		<button type="submit" class="btn btn-primary" title="{t}Apply filter and options{/t}">{t}Apply{/t}</button>
	  </form>
		
	  </div> <!-- div class="col-md-3 cold-lg-3" -->
  </div> <!-- div class="row" -->
</div> <!-- div class="container-fluid" -->

{include file="footer.tpl"}
