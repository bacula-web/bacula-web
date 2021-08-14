<div class="container-fluid" id="jobsreport">

  <div class="page-header">
    <h3>{$page_name} <small>{t}Bacula jobs overview{/t}</small></h3>
  </div>

  <div class="row">
	  <!-- Filter jobs form -->
	  <div class="col-xs-12 col-sm-3 col-sm-push-9 col-lg-2 col-lg-push-10">

	  <form class="form" role="form" action="index.php?page=jobs" method="post">

		<span class="help-block">{t}Filter{/t}</span>

		<div class="form-group">
		  <label>{t}Job status{/t}</label>
        {html_options class="form-control" name=job_status_filter options=$job_status selected=$job_status_filter}
		</div>

		<div class="form-group">
		  <label>{t}Level{/t}</label>
        {html_options class="form-control" name=job_levelid_filter options=$levels_list selected=$job_level_filter}
		</div>
      
      <div class="form-group">
		  <label>{t}Type{/t}</label>
        {html_options class="form-control" name=job_type_filter options=$job_types_list selected=$job_type_filter}
      </div>

		<div class="form-group">
		  <label>{t}Client{/t}</label>
        {html_options class="form-control" name=job_clientid_filter options=$clients_list selected=$job_clientid_filter}
		</div>

		<div class="form-group">
		  <label>{t}Pool{/t}</label>
        {html_options class="form-control" name=jobs_poolid_filter options=$pools_list selected=$job_poolid_filter}
		</div>

		<div class="form-group">
		  <label>{t}Start time{/t}</label>
            <div class='input-group date datetimepicker' id='datetimepicker1'>
                <input name="job_starttime_filter" type='text' class="form-control" value="{$job_starttime_filter}" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
            </div>
		</div>

		<div class="form-group">
		  <label>{t}End time{/t}</label>
            <div class='input-group date datetimepicker' id='datetimepicker1'>
                <input name="job_endtime_filter" type='text' class="form-control" value="{$job_endtime_filter}" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
            </div>
		</div>

		<span class="help-block">{t}Options{/t}</span>

		<label>{t}Order by{/t}</label>
        {html_options class="form-control" name=job_orderby options=$result_order selected=$result_order_field}

		<div class="checkbox">
		  <label>
			<input type="checkbox" name="job_orderby_asc" value="{t}ASC{/t}" {$result_order_asc_checked}> Up
		  </label>
		</div>

		<button type="reset" class="btn btn-default btn-sm" title="{t}Reset{/t}">{t}Reset{/t}</button>
		<button type="submit" class="btn btn-primary btn-sm pull-right" title="{t}Apply filter and options{/t}">{t}Apply{/t}</button>

      <a class="btn btn-link btn-sm" title="{t}Reset to default{/t}" href="index.php?page=jobs" role="button">{t}Reset to default{/t}</a>
	  </form>

	  </div> <!-- div class="col-md-3 cold-lg-3" -->

	  <div class="col-xs-12 col-sm-9 col-sm-pull-3 col-lg-10 col-lg-pull-2">
	  <div class="table-responsive">
		<table class="table table-condensed table-striped text-center paginate">
          <thead>
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
        </thead>

		  <!-- <div class="listbox"> -->
		  {foreach from=$last_jobs item=job}
		  <tr>
			<td>
		      <span class="glyphicon glyphicon-{$job.Job_icon}" title="{$job.jobstatuslong}"></span>
			</td>
			<td>{$job.jobid}</td>
			<td class="text-left">
           {if $job.type == 'B'}
			    <a href="index.php?page=backupjob&backupjob_name={$job.job_name|escape:'url'}">{$job.job_name}</a>
           {else}
			    {$job.job_name}
           {/if}
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
			  <a href="index.php?page=joblogs&jobid={$job.jobid}" title="{t}Show job logs{/t}">
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

	  </div>
  </div> <!-- div class="row" -->
</div> <!-- div class="container-fluid" -->
