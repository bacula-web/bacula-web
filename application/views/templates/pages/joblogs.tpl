{extends file='default.tpl'}

{block name=title}
	<title>Bacula-Web - {t}Job logs{/t}</title>
{/block}

{block name=body}
<div class="container">

  <div class="page-header">
    <h3>{t}Job logs{/t}<small>&nbsp;{t}Bacula job log{/t}</small></h3>
  </div>

    <div class="panel panel-default">
		<div class="panel-heading"> <h4 class="panel-title">{t}Job details{/t}</h4> </div>
		<div class="panel-body">
			<div class="row">
				<div class="cold-md-6">
					<dl class="dl-horizontal">
						<dt>{t}Job id{/t}</dt> <dd>{$job->jobid}</dd>
						<dt>{t}Job name{/t}</dt> <dd>{$job->job_name}</dd>
						<dt>{t}Job status{/t}</dt> <dd>{$job->jobstatuslong}</dd>
						<dt>{t}Job bytes{/t}</dt> <dd>{$job->getJobBytes()}</dd>
						<dt>{t}Scheduled time{/t}</dt> <dd>{$job->schedtime}</dd>
						<dt>{t}Job start time{/t}</dt> <dd>{$job->starttime}</dd>
						<dt>{t}Job end time{/t}</dt> <dd>{$job->endtime}</dd>
						<dt>{t}Job level{/t}</dt> <dd>{$job->getLevel()}</dd>
						<dt>{t}Pool{/t}</dt> <dd>{$job->pool_name}</dd>
					</dl>
				</div>

			</div>
		</div> <!-- end div class="panel-body" -->
	</div>
	<div class="table-responsive">
    <table class="table table-hover table-striped table-condensed table-bordered">
		<tr>
			<th class="text-center">{t}Time{/t}</th> 
			<th class="text-center">{t}Event{/t}</th>
		</tr>
		{foreach from=$joblogs item=log}
	    <tr>
			<td class="text-center">{$log->getTime()}</td>
			<td class="text-left">{$log->getLogText()}</td>
	    </tr>
        {foreachelse}
        <tr>
			<td colspan="2" class="text-center">{t}No log(s) for this job{/t}</td>
        </tr>
		{/foreach}
	</table>
	</div>
</div> <!-- end div class="container-fluid" -->
{/block}