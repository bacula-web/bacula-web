{include file=header.tpl}

<div id="main_center">  
  <div class="box">
    <h4>{t}Client informations{/t}</h4>	
    <table class="table_big">
	  <tr>
		<td>{t}Client name{/t}:</td> <td class="strong">{$client_name}</td>
	  </tr>
	  <tr>
		<td>{t}Client version{/t}:</td> <td class="strong">{$client_version}</td>
	  </tr>
	  <tr>
		<td>{t}Client os{/t}:</td> <td class="strong">{$client_os}</td>
	  </tr>
	  <tr>
		<td>{t}Client arch{/t}:</td> <td class="strong">{$client_arch}</td>
	  </tr>
    </table>
	
	<h4>Last good backup job</h4>
	<table>
		<tr>
			<th>{t}Name{/t}</th>
			<th>{t}Jod Id{/t}</th>
			<th>{t}Level{/t}</th>
			<th>{t}End time{/t}</th>
			<th>{t}Bytes{/t}</th>
			<th>{t}Files{/t}</th>
			<th>{t}Status{/t}</th>
		</tr>
		{foreach from=$backup_jobs item=job}
		<tr class="{$job.odd_even}">
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
	
	&nbsp;
	<h4>Statistics - last {$period} days(s)</h4>
	<table>
		<tr>
			<td> <img class="graph" src="{$graph_stored_bytes}" alt="" /> </td>
			<td> <img class="graph" src="{$graph_stored_files}" alt="" /> </td>
		</tr>
	</table>
	
  </div> <!-- end div class=box -->
  
  </div> <!-- end div id=main_center -->

{include file="footer.tpl"}
