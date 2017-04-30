{include file=header.tpl}

<div class="container-fluid">

    <h3>{$page_name}</h3>    

		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="panel-title">{t}Job informations{/t}</h3></div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt>{t}Job name{/t}</dt> <dd>{$backupjob_name}</dd>
					<dt>{t}Period{/t}</dt> <dd>{$backupjob_period}</dd>
					<dt>{t}Transfered Bytes{/t}</dt> <dd>{$backupjob_bytes}</dd>
					<dt>{t}Transfered Files{/t}</dt> <dd>{$backupjob_files}</dd>
				</dl>
			</div>
		</div> <!-- end div class="panel ..." -->
			
		<!-- Last jobs list -->
		<h4>{t}Last jobs{/t}</h4>
	
	<div class="table-responsive">
	<table class="table table-condensed table-hover table-striped table-bordered text-center">
		<tr>
			<th class="text-center">{t}Job Id{/t}</th>
			<th class="text-center">{t}Level{/t}</th>
			<th class="text-center">{t}Files{/t}</th>
			<th class="text-center">{t}Bytes{/t}</th>
			<th class="text-center">{t}Start time{/t}</th>
			<th class="text-center">{t}End time{/t}</th>
			<th class="text-center">{t}Elapsed time{/t}</th>
			<th class="text-center">{t}Speed{/t}</th>
            <th class="text-center">{t}Compression{/t}</th>
		</tr>
		{foreach from=$jobs item=job}
		<tr> 
			<td>{$job.jobid}</td>
			<td>{$job.joblevel}</td>
			<td>{$job.jobfiles}</td>
			<td>{$job.jobbytes}</td>
			<td>{$job.starttime}</td>
			<td>{$job.endtime}</td>
			<td>{$job.elapsedtime}</td>
			<td>{$job.speed}</td>
            <td>{$job.compression}</td>
		</tr>
		{/foreach}
	</table>
	</div>
  
	<p>&nbsp;</p>
  
   <!-- Transfered Bytes/Files graph -->
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Transfered Bytes{/t}</b></div>
				<div class="panel-body">			
					<div class="img_loader text-center">
						<i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;
						<p>Loading graph</p>
					</div>
					<img class="img-responsive center-block" src="{$graph_stored_bytes}" alt="Stored Bytes">
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6"> 
			<div class="panel panel-default">
				<div class="panel-heading"><b>{t}Transfered Files{/t}</b></div>
				<div class="panel-body">
					<div class="img_loader text-center">
						<i class="fa fa-spinner fa-spin fa-2x"></i>&nbsp;
						<p>Loading graph</p>
					</div>
					<img class="img-responsive center-block" src="{$graph_stored_files}" alt="Stored Files"> 
				</div>
			</div>
		</div>
	</div> <! -- div class="row" -->

	</div> <!-- class="container-fluid" -->

{include file="footer.tpl"}
