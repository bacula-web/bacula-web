{include file=header.tpl}

<div class="container-fluid" id="jobsreport">

    <h3>{$page_name}</h3>

  <div class="row">
	  <!-- Filter jobs form -->
	  <div class="col-xs-12 col-sm-3 col-sm-push-9 col-lg-2 col-lg-push-10">

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
		  <label>{t}Level{/t}</label>
			<select name="level_id" class="form-control">
			  {foreach from=$levels_list key=level_id item=level_name}
				<option value="{$level_id}" {if $level_id eq $level_filter}selected{/if}>{$level_name}</option>
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

		<div class="form-group">
		  <label>{t}Pool{/t}</label>
			<select name="pool_id" class="form-control">
			  {foreach from=$pools_list key=pool_id item=pool}
				<option value="{$pool.poolid}" {if $pool_id eq $pool_filter}selected{/if}>{$pool.name}</option>
			  {/foreach}
			</select>
		</div>

		<div class="form-group">
		  <label>{t}Start time{/t}</label>
            <div class='input-group date datetimepicker' id='datetimepicker1'>
                <input name="start_time" type='text' class="form-control" value="{$start_time_filter}" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
            </div>
		</div>

		<div class="form-group">
		  <label>{t}End time{/t}</label>
            <div class='input-group date datetimepicker' id='datetimepicker1'>
                <input name="end_time" type='text' class="form-control" value="{$end_time_filter}" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
            </div>
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
			<input type="checkbox" name="result_order_asc" value="{t}ASC{/t}" {$result_order_asc_checked}> Up
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

		<button type="reset" class="btn btn-default btn-sm" title="{t}Reset to default options{/t}">{t}Reset{/t}</button>
		<button type="submit" class="btn btn-primary btn-sm pull-right" title="{t}Apply filter and options{/t}">{t}Apply{/t}</button>
	  </form>

	  </div> <!-- div class="col-md-3 cold-lg-3" -->

	  <div class="visible-xs-block"><p>&nbsp;</p></div>

	  <div class="col-xs-12 col-sm-9 col-sm-pull-3 col-lg-10 col-lg-pull-2">
	  <div class="table-responsive">
		<table class="table table-condensed table-striped text-center">
		  <tr>
			<th class="text-center">{t}Status{/t}</th>
			<th class="text-center">{t}Job ID{/t}</th>
			<th class="text-left">{t}Name{/t}</th>
			<th class="text-center">{t}Type{/t}</th>
      <th class="text-center">{t}Scheduled Time{/t}</th>
			<th class="text-center">{t}Start time{/t}</th>
			<th class="text-center">{t}End time{/t}</th>
			<th class="text-center">{t}Elapsed time{/t}</th>
			<th class="text-center">{t}Level{/t}</th>
			<th class="text-center">{t}Bytes{/t}</th>
			<th class="text-center">{t}Files{/t}</th>
			<th class="text-center">{t}Speed{/t}</th>
			<th class="text-center">{t}Compression{/t}</th>
			<th class="text-center">{t}Pool{/t}</th>
			<th class="text-center">{t}Log{/t}</th>
		  </tr>

		  <!-- <div class="listbox"> -->
		  {foreach from=$last_jobs item=job}
		  <tr>
			<td>
				<a href="joblogs.php?jobid={$job.jobid}" title="{t}Show job logs{/t}">
		          <span class="glyphicon glyphicon-{$job.Job_icon}" title="{$job.jobstatuslong}"></span>
				</a>
			</td>
			<td>{$job.jobid}</td>
			<td class="text-left">
			  <a href="backupjob-report.php?backupjob_name={$job.job_name|escape:'url'}">{$job.job_name}</a>
			</td>
			<td>{$job.type}</td>
      <td>{$job.schedtime}</td>
			<td>{$job.starttime}</td>
			<td>{$job.endtime}</td>
			<td>{$job.elapsed_time}</td>
			<td>{$job.level}</td>
			<td>{$job.jobbytes}</td>
			<td>{$job.jobfiles}</td>
			<td>{$job.speed}</td>
			<td>{$job.compression}</td>
			<td>{$job.pool_name}</td>
			<td>
			  <a href="joblogs.php?jobid={$job.jobid}" title="{t}Show job logs{/t}">
			    <span class="glyphicon glyphicon-search"></span>
                          </a>
			</td>
		  </tr>
		  {foreachelse}
		  <tr>
			<td colspan="12">{t}No job(s) to display{/t}</td>
		  </tr>
		  {/foreach}
		</table>
	  </div>

		<div class="alert alert-info text-center" role="alert">
		  Found <b>{$jobs_found}</b> of <b>{$total_jobs} Job(s)</b>
		</div>
	  </div>
  </div> <!-- div class="row" -->
</div> <!-- div class="container-fluid" -->
{include file="footer.tpl"}
