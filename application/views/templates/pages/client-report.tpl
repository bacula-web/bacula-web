{extends file='default.tpl'}

{block name=title}
	<title>Bacula-Web - {t}Client report{/t}</title>
{/block}

{block name=body}
<div class="container">
  <div class="page-header">
    <h3>{$page_name} <small>{t}Report per Bacula client{/t}</small></h3>
  </div>

    <div class="panel panel-default">
      <div class="panel-heading"><b>{t}Report options{/t}</b></div>
      <div class="panel-body">
        <form method="post" action="index.php?page=client" class="form-inline">
          <!-- Backup job name -->
          <div class="form-group">
            <label for="clientname">{t}Client{/t}</label>
              {html_options class="form-control" name=client_id options=$clients_list values=$clients_list selected=$selected_client} 
          </div>
          <!-- Period -->
          <div class="form-group">
            <label for="period">{t}Period{/t}</label>
              {html_options class="form-control" name=period options=$periods_list selected=$selected_period}
          </div>

          <!-- Submit button -->
          <div class="form-group pull-right">
              <button type="submit" class="btn btn-primary">{t}View report{/t}</button>
          </div>
        </form>
      </div> <!-- end div class=panel-body -->
    </div> <!-- end div class=panel ... -->

    {if $no_report_options == 'false'}	
      <div class="panel panel-default">
		  <div class="panel-heading"> <h4 class="panel-title">{t}Client informations{/t}</h4>	</div>
		  <div class="panel-body">
		    <dl class="dl-horizontal">
				 <dt>{t}Client name{/t}</dt> <dd>{$client_name}</dd>
				 <dt>{t}Client version{/t}</dt> <dd>{$client_version}</dd>
				 <dt>{t}Client os{/t}</dt> <dd>{$client_os}</dd>
				 <dt>{t}Client arch{/t}<dt> <dd>{$client_arch}</dd>
			  </dl>
		  </div>
      </div>
	
	  <h4>{t}Last good backup job{/t}</h4>
	  <div class="table-responsive">
	  <table class="table table-bordered table-condensed table-striped text-center">
		 <tr>
			<th class="text-center">{t}Name{/t}</th>
			<th class="text-center">{t}Jod Id{/t}</th>
			<th class="text-center">{t}Level{/t}</th>
			<th class="text-center">{t}End time{/t}</th>
			<th class="text-center">{t}Bytes{/t}</th>
			<th class="text-center">{t}Files{/t}</th>
			<th class="text-center">{t}Status{/t}</th>
		</tr>
		{foreach from=$backup_jobs item=job}
		<tr>
			<td>{$job.name}</td>
			<td>{$job.jobid}</td>
			<td>{$job.level}</td>
			<td>{$job.endtime}</td>
			<td>{$job.jobbytes}</td>
			<td>{$job.jobfiles}</td>
			<td>{$job.jobstatuslong}</td>
		</tr>
		{/foreach}
	</table>
	</div>
	
	<h4>{t}Statistics{/t} - {t}Last{/t} {$period} {t}days(s){/t}</h4>

   <!-- Bytes and files charts -->
   {if ( $period > 7 ) } 
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Bytes{/t}</b></div>
				<div class="panel-body">
               <div id="{$stored_bytes_chart_id}"> <svg></svg> </div>
                  {$stored_bytes_chart}
				</div>
			</div>
		</div>
   </div> <!-- div class="row" -->

   <div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Files{/t}</b></div>
				<div class="panel-body">
               <div id="{$stored_files_chart_id}"> <svg></svg> </div>
                  {$stored_files_chart}
				</div>
			</div>
		</div>
	</div>
   {else}
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Bytes{/t}</b></div>
				<div class="panel-body">
               <div id="{$stored_bytes_chart_id}"> <svg></svg> </div>
                  {$stored_bytes_chart}
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Files{/t}</b></div>
				<div class="panel-body">
               <div id="{$stored_files_chart_id}"> <svg></svg> </div>
                  {$stored_files_chart}
				</div>
			</div>
		</div>
	</div>
   {/if}
   {else}
     <div class="alert alert-info" role="alert">{t}Choose the client name and the period interval, then click on the{/t} <strong>{t}View report{/t}</strong> {t}button{/t}</div>
   {/if}
</div> <!-- div class="container" -->
{/block}