{include file=header.tpl}

<div class="container-fluid">  
    <h3>{$page_name}</h3>

  <div class="row">
    <div class="col-md-8">

    <h4>{t}Client informations{/t}</h4>	
    <table class="table table-condensed table-bordered">
	  <tr>
		<td>{t}Client name{/t}:</td> <td>{$client_name}</td>
	  </tr>
	  <tr>
		<td>{t}Client version{/t}:</td> <td>{$client_version}</td>
	  </tr>
	  <tr>
		<td>{t}Client os{/t}:</td> <td>{$client_os}</td>
	  </tr>
	  <tr>
		<td>{t}Client arch{/t}:</td> <td>{$client_arch}</td>
	  </tr>
    </table>
	
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
	<table class="table">
		<tr>
			<td> <img class="img-responsive" src="{$graph_stored_bytes}" alt="" /> </td>
			<td> <img class="img-responsive" src="{$graph_stored_files}" alt="" /> </td>
		</tr>
	</table>
	
    </div> <!-- div class="col-md-..." -->
  </div> <!-- div class="row" -->
  
  </div> <!-- div class="container-fluid" -->

{include file="footer.tpl"}
