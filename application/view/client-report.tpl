{include file=header.tpl}

<div class="container">  
	<h3>{$page_name}</h3>
	
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
	
	<h4>Last good backup job</h4>
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
	
	<h4>{t}Statistics{/t} - {t}last{/t} {$period} {t}days(s){/t}</h4>
	
	<div class="row">
		<div class="col-xs-6"> <td> <img class="img-responsive center-block" src="{$graph_stored_bytes}" alt="" /> </td> </div>
		<div class="col-xs-6"> <td> <img class="img-responsive center-block" src="{$graph_stored_files}" alt="" /> </td> </div>
	</div>
</div> <!-- div class="container" -->

{include file="footer.tpl"}
